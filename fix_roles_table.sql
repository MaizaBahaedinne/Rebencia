-- ============================================================
-- Migration de correction — Rebencia
-- À exécuter sur le serveur de production via phpMyAdmin ou SSH
-- Toutes les instructions utilisent IF NOT EXISTS / IF EXISTS
-- pour être idempotentes (sans danger si relancé).
-- ============================================================

-- ------------------------------------------------------------
-- 1. TABLE `roles` — colonnes manquantes
-- ------------------------------------------------------------
ALTER TABLE `roles`
    ADD COLUMN IF NOT EXISTS `label`     VARCHAR(100) NOT NULL DEFAULT '' AFTER `name`,
    ADD COLUMN IF NOT EXISTS `color`     VARCHAR(20)  NULL     DEFAULT '#6c757d' AFTER `description`,
    ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1)   NOT NULL DEFAULT 1 AFTER `color`;

UPDATE `roles`
SET `label` = CASE `name`
    WHEN 'director'     THEN 'Directeur d\'Agence'
    WHEN 'expert'       THEN 'Expert Immobilier'
    WHEN 'coordinator'  THEN 'Coordinateur'
    WHEN 'collaborator' THEN 'Collaborateur'
    ELSE `name`
END
WHERE `label` = '';

UPDATE `roles`
SET `color` = CASE `name`
    WHEN 'director'     THEN '#dc3545'
    WHEN 'expert'       THEN '#0d6efd'
    WHEN 'coordinator'  THEN '#198754'
    WHEN 'collaborator' THEN '#fd7e14'
    ELSE '#6c757d'
END
WHERE `color` IS NULL OR `color` = '';

-- ------------------------------------------------------------
-- 2. TABLE `users` — colonne soft-delete manquante
-- ------------------------------------------------------------
ALTER TABLE `users`
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL;

-- ------------------------------------------------------------
-- 3. TABLE `properties` — colonne soft-delete manquante
-- ------------------------------------------------------------
ALTER TABLE `properties`
    ADD COLUMN IF NOT EXISTS `deleted_at` DATETIME NULL DEFAULT NULL;

-- ------------------------------------------------------------
-- 4. TABLE `leads` — plusieurs colonnes manquantes
-- ------------------------------------------------------------
ALTER TABLE `leads`
    ADD COLUMN IF NOT EXISTS `deleted_at`       DATETIME      NULL DEFAULT NULL,
    ADD COLUMN IF NOT EXISTS `desired_surface`  DECIMAL(10,2) NULL DEFAULT NULL AFTER `budget_max`,
    ADD COLUMN IF NOT EXISTS `desired_location` VARCHAR(255)  NULL DEFAULT NULL AFTER `desired_surface`;

-- Correction du ENUM status
ALTER TABLE `leads`
    MODIFY COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'new';

UPDATE `leads` SET `status` = 'visit_done'   WHERE `status` = 'visit';
UPDATE `leads` SET `status` = 'negotiating'  WHERE `status` = 'negotiation';
UPDATE `leads` SET `status` = 'won'          WHERE `status` = 'sold';

ALTER TABLE `leads`
    MODIFY COLUMN `status` ENUM('new','contacted','interested','visit_done','negotiating','won','lost')
    NOT NULL DEFAULT 'new';

-- ------------------------------------------------------------
-- 5. TABLE `lead_notes` — renommer content → note
-- ------------------------------------------------------------
ALTER TABLE `lead_notes`
    CHANGE COLUMN IF EXISTS `content` `note` TEXT NOT NULL;

-- ------------------------------------------------------------
-- 6. TABLE `property_history` — renommer field_name + ajouter action
-- ------------------------------------------------------------
ALTER TABLE `property_history`
    CHANGE COLUMN IF EXISTS `field_name` `field_changed` VARCHAR(100) NOT NULL,
    ADD COLUMN IF NOT EXISTS `action` VARCHAR(50) NOT NULL DEFAULT 'update' AFTER `user_id`;

-- ------------------------------------------------------------
-- Vérification
-- ------------------------------------------------------------
SELECT 'roles'      AS `table`, COUNT(*) AS rows FROM `roles`
UNION ALL
SELECT 'users'      AS `table`, COUNT(*) AS rows FROM `users`
UNION ALL
SELECT 'properties' AS `table`, COUNT(*) AS rows FROM `properties`
UNION ALL
SELECT 'leads'      AS `table`, COUNT(*) AS rows FROM `leads`;
