-- ============================================================
-- Migration : ajout des colonnes manquantes dans la table `roles`
-- À exécuter sur le serveur de production via phpMyAdmin ou SSH
-- ============================================================

-- Ajout de la colonne `label` si elle n'existe pas
ALTER TABLE `roles`
    ADD COLUMN IF NOT EXISTS `label`     VARCHAR(100) NOT NULL DEFAULT '' AFTER `name`,
    ADD COLUMN IF NOT EXISTS `color`     VARCHAR(20)  NULL     DEFAULT '#6c757d' AFTER `description`,
    ADD COLUMN IF NOT EXISTS `is_active` TINYINT(1)   NOT NULL DEFAULT 1 AFTER `color`;

-- Population du label depuis le nom du rôle pour les lignes existantes
UPDATE `roles`
SET `label` = CASE `name`
    WHEN 'director'     THEN 'Directeur d\'Agence'
    WHEN 'expert'       THEN 'Expert Immobilier'
    WHEN 'coordinator'  THEN 'Coordinateur'
    WHEN 'collaborator' THEN 'Collaborateur'
    ELSE `name`
END
WHERE `label` = '';

-- Population de la couleur pour les lignes existantes
UPDATE `roles`
SET `color` = CASE `name`
    WHEN 'director'     THEN '#dc3545'
    WHEN 'expert'       THEN '#0d6efd'
    WHEN 'coordinator'  THEN '#198754'
    WHEN 'collaborator' THEN '#fd7e14'
    ELSE '#6c757d'
END
WHERE `color` IS NULL OR `color` = '';

-- Vérification
SELECT id, name, label, color, is_active FROM `roles`;
