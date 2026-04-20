-- ============================================================
-- Migration manuelle : notifications + push_subscriptions
-- Équivalent à : 2026-04-20-200000_CreateNotificationsTable
--
-- Exécuter dans phpMyAdmin ou :
--   mysql -u root rebencia < create_notifications_tables.sql
-- ============================================================

CREATE TABLE IF NOT EXISTS `notifications` (
    `id`         INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED     NOT NULL,
    `type`       VARCHAR(50)      NOT NULL DEFAULT 'info'
                    COMMENT 'info|success|warning|lead|property|task|system',
    `title`      VARCHAR(150)     NOT NULL,
    `message`    TEXT             NOT NULL,
    `url`        VARCHAR(500)     NULL,
    `is_read`    TINYINT(1)       NOT NULL DEFAULT 0,
    `read_at`    DATETIME         NULL,
    `created_at` DATETIME         NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_user`      (`user_id`),
    INDEX `idx_user_read` (`user_id`, `is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `push_subscriptions` (
    `id`           INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    `user_id`      INT UNSIGNED  NOT NULL,
    `endpoint`     TEXT          NOT NULL,
    `public_key`   VARCHAR(255)  NOT NULL COMMENT 'clé p256dh',
    `auth_token`   VARCHAR(100)  NOT NULL,
    `user_agent`   VARCHAR(255)  NULL,
    `created_at`   DATETIME      NULL,
    `last_used_at` DATETIME      NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Enregistrer la migration dans la table CI4
INSERT IGNORE INTO `migrations`
    (`version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES (
    '2026-04-20-200000',
    'App\\Database\\Migrations\\CreateNotificationsTable',
    'default',
    'App',
    UNIX_TIMESTAMP(),
    (SELECT COALESCE(MAX(batch),0)+1 FROM migrations m2)
);
