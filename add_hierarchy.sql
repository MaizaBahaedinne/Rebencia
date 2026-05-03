-- ============================================================
-- Rebencia — Migration hiérarchie multi-niveaux
-- v1.0 — 2026-05-03
-- Idempotente : sans danger si relancée plusieurs fois
-- ============================================================

-- ------------------------------------------------------------
-- 1. TABLE `organizations` — entité regroupant plusieurs agences
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `organizations` (
    `id`         INT          NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(150) NOT NULL,
    `logo`       VARCHAR(255) NULL     DEFAULT NULL,
    `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
    `created_at` DATETIME     NULL     DEFAULT NULL,
    `updated_at` DATETIME     NULL     DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. TABLE `roles` — colonne hierarchy_level
-- ------------------------------------------------------------
ALTER TABLE `roles`
    ADD COLUMN IF NOT EXISTS `hierarchy_level` TINYINT(1) NOT NULL DEFAULT 5
        COMMENT '1=SuperAdmin 2=Admin 3=PDG/DG 4=Dir.Agence/Coord 5=Expert/Collab'
        AFTER `is_active`;

-- ------------------------------------------------------------
-- 3. TABLE `agencies` — lien organisation
-- ------------------------------------------------------------
ALTER TABLE `agencies`
    ADD COLUMN IF NOT EXISTS `organization_id` INT NULL DEFAULT NULL AFTER `id`;

-- ------------------------------------------------------------
-- 4. TABLE `users` — lien organisation (pour PDG/DG)
-- ------------------------------------------------------------
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `organization_id` INT NULL DEFAULT NULL AFTER `agency_id`;

-- ------------------------------------------------------------
-- 5. Mettre à jour les niveaux des rôles existants
-- ------------------------------------------------------------
UPDATE `roles` SET `hierarchy_level` = 1 WHERE `name` = 'super_admin';
UPDATE `roles` SET `hierarchy_level` = 2 WHERE `name` = 'admin';
UPDATE `roles` SET `hierarchy_level` = 4 WHERE `name` IN ('director', 'coordinator');
UPDATE `roles` SET `hierarchy_level` = 5 WHERE `name` IN ('expert', 'collaborator');

-- ------------------------------------------------------------
-- 6. Insérer les nouveaux rôles PDG et Directeur Général
-- ------------------------------------------------------------
INSERT IGNORE INTO `roles`
    (`name`, `label`, `description`, `color`, `is_active`, `hierarchy_level`, `created_at`, `updated_at`)
VALUES
    ('pdg',               'PDG',                'Président Directeur Général — vision multi-agences organisation', '#6f42c1', 1, 3, NOW(), NOW()),
    ('directeur_general', 'Directeur Général',  'Directeur Général — vision multi-agences organisation',          '#0d6efd', 1, 3, NOW(), NOW());

-- PDG : permissions métier complètes (tout sauf system.*)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name = 'pdg'
  AND p.name NOT IN ('system.deploy', 'system.settings', 'system.logs');

-- Directeur Général : idem PDG
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name = 'directeur_general'
  AND p.name NOT IN ('system.deploy', 'system.settings', 'system.logs');

-- ------------------------------------------------------------
-- 7. Vérification
-- ------------------------------------------------------------
SELECT r.name, r.label, r.hierarchy_level, COUNT(rp.permission_id) AS nb_permissions
FROM `roles` r
LEFT JOIN `role_permissions` rp ON rp.role_id = r.id
GROUP BY r.id, r.name, r.label, r.hierarchy_level
ORDER BY r.hierarchy_level, r.id;
