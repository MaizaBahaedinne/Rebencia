-- ============================================================
-- REBENCIA — Module Agences v1.0
-- À exécuter sur : rebencia (local) et rebe_RebenciaDB (production)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. TABLE AGENCES
-- ============================================================
CREATE TABLE IF NOT EXISTS `agencies` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(150)     NOT NULL,
  `slug`        VARCHAR(160)     NOT NULL UNIQUE,
  `email`       VARCHAR(191),
  `phone`       VARCHAR(30),
  `address`     VARCHAR(255),
  `city`        VARCHAR(100),
  `logo`        VARCHAR(255),
  `description` TEXT,
  `zone_id`     INT UNSIGNED     NULL COMMENT 'Zone géographique de responsabilité',
  `is_active`   TINYINT(1)       NOT NULL DEFAULT 1,
  `created_at`  DATETIME,
  `updated_at`  DATETIME,
  `deleted_at`  DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_slug`      (`slug`),
  KEY `idx_is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. AJOUT agency_id SUR USERS
-- ============================================================
ALTER TABLE `users`
  ADD COLUMN `agency_id` INT UNSIGNED NULL COMMENT 'Agence de rattachement (NULL = pas d\'agence / super admin)' AFTER `role_id`;

ALTER TABLE `users`
  ADD KEY `idx_users_agency` (`agency_id`),
  ADD CONSTRAINT `fk_users_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON DELETE SET NULL;

-- ============================================================
-- 3. AJOUT agency_id SUR PROPERTIES
-- ============================================================
ALTER TABLE `properties`
  ADD COLUMN `agency_id` INT UNSIGNED NULL COMMENT 'Agence propriétaire du bien' AFTER `agent_id`;

ALTER TABLE `properties`
  ADD KEY `idx_properties_agency` (`agency_id`),
  ADD CONSTRAINT `fk_properties_agency` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`) ON DELETE SET NULL;

-- ============================================================
-- 4. PERMISSIONS AGENCES
-- ============================================================
INSERT IGNORE INTO `permissions` (`name`, `label`, `module`, `created_at`) VALUES
('agencies.view',   'Voir les agences',      'agencies', NOW()),
('agencies.create', 'Créer des agences',     'agencies', NOW()),
('agencies.edit',   'Modifier des agences',  'agencies', NOW()),
('agencies.delete', 'Supprimer des agences', 'agencies', NOW());

-- Super Admin & Admin : toutes les permissions agences
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name IN ('super_admin', 'admin')
  AND p.name LIKE 'agencies.%';

-- Directeur : view uniquement (voir son agence)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name = 'director'
  AND p.name = 'agencies.view';

-- ============================================================
-- 5. AGENCE DEMO
-- ============================================================
INSERT INTO `agencies` (`name`, `slug`, `email`, `phone`, `city`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('Agence Principale Tunis', 'agence-principale-tunis', 'contact@rebencia.com', '+216 71 000 001', 'Tunis',
 'Agence principale couvrant la région du Grand Tunis.', 1, NOW(), NOW()),
('Agence Sfax', 'agence-sfax', 'sfax@rebencia.com', '+216 74 000 001', 'Sfax',
 'Agence régionale couvrant le gouvernorat de Sfax.', 1, NOW(), NOW()),
('Agence Sousse', 'agence-sousse', 'sousse@rebencia.com', '+216 73 000 001', 'Sousse',
 'Agence régionale couvrant le Sahel et Sousse.', 1, NOW(), NOW());

-- Rattacher l'admin existant à l'agence principale
UPDATE `users` SET `agency_id` = 1 WHERE `email` = 'admin@rebencia.com';

SET FOREIGN_KEY_CHECKS = 1;
