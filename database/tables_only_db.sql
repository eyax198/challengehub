
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


INSERT IGNORE INTO `users` (`username`, `email`, `password`, `bio`, `created_at`) VALUES
('alice', 'alice@demo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Photographe passionnée 📷', NOW()),
('bob',   'bob@demo.com',   '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Développeur & passionné de design',  NOW()),
('carol', 'carol@demo.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Artiste numérique 🎨', NOW());

INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Le plus beau coucher de soleil', 'Photographiez le plus beau coucher de soleil que vous ayez jamais vu. Critères : originalité, composition, lumière.', 'Photographie', DATE_ADD(NOW(), INTERVAL 30 DAY), NOW()
FROM users u WHERE u.username = 'alice' LIMIT 1;

INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Créez votre propre jeu en 48h', 'Développez un mini-jeu fonctionnel en moins de 48 heures. N''importe quel langage ou moteur est accepté.', 'Programmation', DATE_ADD(NOW(), INTERVAL 14 DAY), NOW()
FROM users u WHERE u.username = 'bob' LIMIT 1;

INSERT IGNORE INTO `challenges` (`user_id`, `title`, `description`, `category`, `deadline`, `created_at`)
SELECT u.id, 'Illustration mystérieuse', 'Créez une illustration numérique sur le thème du mystère et de l''inconnu. Technique libre.', 'Art & Design', DATE_ADD(NOW(), INTERVAL 21 DAY), NOW()
FROM users u WHERE u.username = 'carol' LIMIT 1;
