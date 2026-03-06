-- SCRIPT SQL POUR CHALLENGEHUB
-- Base de données : challengehub_db

CREATE DATABASE IF NOT EXISTS `challengehub_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `challengehub_db`;

-- Table pour les utilisateurs (membres du site)
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)      NOT NULL,
  `email`      VARCHAR(255)     NOT NULL,
  `password`   VARCHAR(255)     NOT NULL,
  `avatar`     VARCHAR(255)         NULL DEFAULT NULL,
  `bio`        TEXT                 NULL DEFAULT NULL,
  `created_at` DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email`    (`email`),
  UNIQUE KEY `uq_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les défis (lancés par les membres)
CREATE TABLE IF NOT EXISTS `challenges` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED     NOT NULL,
  `title`       VARCHAR(150)     NOT NULL,
  `description` TEXT             NOT NULL,
  `category`    VARCHAR(80)      NOT NULL,
  `deadline`    DATE             NOT NULL,
  `image`       VARCHAR(255)         NULL DEFAULT NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_challenges_user_id`  (`user_id`),
  KEY `idx_challenges_category` (`category`),
  KEY `idx_challenges_created`  (`created_at`),
  CONSTRAINT `fk_challenges_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les participations (projets envoyés pour un défi)
CREATE TABLE IF NOT EXISTS `submissions` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` INT UNSIGNED NOT NULL,
  `user_id`      INT UNSIGNED NOT NULL,
  `description`  TEXT         NOT NULL,
  `image`        VARCHAR(255)     NULL DEFAULT NULL,
  `link`         VARCHAR(500)     NULL DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- On s'assure qu'un utilisateur ne participe qu'une fois par défi
  UNIQUE KEY `uq_submission_per_user` (`challenge_id`, `user_id`),
  KEY `idx_submissions_challenge` (`challenge_id`),
  KEY `idx_submissions_user`      (`user_id`),
  CONSTRAINT `fk_submissions_challenge`
    FOREIGN KEY (`challenge_id`) REFERENCES `challenges` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_submissions_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les commentaires sous les projets
CREATE TABLE IF NOT EXISTS `comments` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `user_id`       INT UNSIGNED NOT NULL,
  `content`       TEXT         NOT NULL,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_comments_submission` (`submission_id`),
  KEY `idx_comments_user`       (`user_id`),
  CONSTRAINT `fk_comments_submission`
    FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_comments_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table pour les votes de la communauté
CREATE TABLE IF NOT EXISTS `votes` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `user_id`       INT UNSIGNED NOT NULL,
  `value`         TINYINT      NOT NULL DEFAULT 1,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  -- Un utilisateur ne peut voter qu'une seule fois par projet
  UNIQUE KEY `uq_vote_per_user` (`submission_id`, `user_id`),
  KEY `idx_votes_submission` (`submission_id`),
  KEY `idx_votes_user`       (`user_id`),
  CONSTRAINT `fk_votes_submission`
    FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_votes_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Quelques données de test pour voir si ça marche
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `bio`, `created_at`) VALUES
('eya', 'eya@gmail.com', '$2y$10$U.J12lK37H9I3VvO2E9q/u7R6b9L9jB.u8D8R9E9jB.u8F8P9R9jB', 'Admin de ChallengeHub ⚡', NOW()),
('ranim', 'ranim@gmail.com', '$2y$10$W9E9fB8u8G8R9R9.u8G8H9R9jB8u8G8R9R9.u8G8V9R9jB8u8G', 'Passionnée de code et de design',  NOW()),
('ines', 'ines@gmail.com', '$2y$10$Z8G8R9R9jB8u8G8R9R9.u8L8R9R9jB8u8G8R9R9.u8M8R9R9jB8u', 'Prête pour les défis ! 🎨', NOW());

-- Exemples de défis
INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Logo Challenge', 'Créez un logo pour une application de sport. On veut quelque chose de dynamique.', 'Design', DATE_ADD(NOW(), INTERVAL 30 DAY), NOW()
FROM users u WHERE u.username = 'eya' LIMIT 1;

INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Page de login PHP', 'Réaliser une page de login sécurisée en utilisant PDO et les sessions.', 'Web', DATE_ADD(NOW(), INTERVAL 14 DAY), NOW()
FROM users u WHERE u.username = 'ranim' LIMIT 1;
