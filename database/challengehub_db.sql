-- ══════════════════════════════════════════════════════════════
-- ChallengeHub — Script SQL complet
-- Base de données : challengehub
-- UTF-8MB4, InnoDB
-- ══════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS `challengehub_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `challengehub_db`;

-- ── Table : users ─────────────────────────────────────────────
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

-- ── Table : challenges ────────────────────────────────────────
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

-- ── Table : submissions ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `submissions` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `challenge_id` INT UNSIGNED NOT NULL,
  `user_id`      INT UNSIGNED NOT NULL,
  `description`  TEXT         NOT NULL,
  `image`        VARCHAR(255)     NULL DEFAULT NULL,
  `link`         VARCHAR(500)     NULL DEFAULT NULL,
  `created_at`   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
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

-- ── Table : comments ──────────────────────────────────────────
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

-- ── Table : votes ─────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `votes` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `submission_id` INT UNSIGNED NOT NULL,
  `user_id`       INT UNSIGNED NOT NULL,
  `value`         TINYINT      NOT NULL DEFAULT 1,
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
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

-- ══════════════════════════════════════════════════════════════
-- Données de démonstration (optionnel)
-- ══════════════════════════════════════════════════════════════

-- Utilisateur demo : hamadi/hamadi123hamadi, rym/rym123rym, rania/rania123rania
INSERT IGNORE INTO `users` (`username`, `email`, `password`, `bio`, `created_at`) VALUES
('hamadi', 'hamadi@gmail.com', '$2y$10$U.J12lK37H9I3VvO2E9q/u7R6b9L9jB.u8D8R9E9jB.u8F8P9R9jB', 'Photographe passionné 📷', NOW()),
('rym',    'rym@gmail.com',    '$2y$10$W9E9fB8u8G8R9R9.u8G8H9R9jB8u8G8R9R9.u8G8V9R9jB8u8G', 'Développeuse & passionnée de design',  NOW()),
('rania',  'rania@gmail.com',  '$2y$10$Z8G8R9R9jB8u8G8R9R9.u8L8R9R9jB8u8G8R9R9.u8M8R9R9jB8u', 'Artiste numérique 🎨',               NOW());

-- Défis de démonstration
INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Le plus beau coucher de soleil', 'Photographiez le plus beau coucher de soleil que vous ayez jamais vu. Critères : originalité, composition, lumière.', 'Photographie', DATE_ADD(NOW(), INTERVAL 30 DAY), NOW()
FROM users u WHERE u.username = 'alice' LIMIT 1;

INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Créez votre propre jeu en 48h', 'Développez un mini-jeu fonctionnel en moins de 48 heures. N''importe quel langage ou moteur est accepté.', 'Programmation', DATE_ADD(NOW(), INTERVAL 14 DAY), NOW()
FROM users u WHERE u.username = 'bob' LIMIT 1;

INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Illustration mystérieuse', 'Créez une illustration numérique sur le thème du mystère et de l''inconnu. Technique libre.', 'Art & Design', DATE_ADD(NOW(), INTERVAL 21 DAY), NOW()
FROM users u WHERE u.username = 'carol' LIMIT 1;
