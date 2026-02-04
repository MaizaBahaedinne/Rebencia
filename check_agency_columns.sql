-- =============================================
-- VÉRIFICATION HIÉRARCHIE AGENCES
-- =============================================

USE `rebe_RebenciaDB`;

-- 1. Vérifier si les colonnes existent
SHOW COLUMNS FROM `agencies` LIKE 'parent_agency_id';
SHOW COLUMNS FROM `agencies` LIKE 'is_headquarters';

-- 2. Si les colonnes n'existent pas, les créer
-- Décommenter ces lignes si les colonnes n'existent pas encore:

-- ALTER TABLE `agencies` 
-- ADD COLUMN `parent_agency_id` INT(11) UNSIGNED NULL AFTER `id`,
-- ADD COLUMN `is_headquarters` TINYINT(1) DEFAULT 0 COMMENT '1 = Siège, 0 = Agence locale' AFTER `parent_agency_id`;

-- ALTER TABLE `agencies`
-- ADD CONSTRAINT `fk_agencies_parent` 
-- FOREIGN KEY (`parent_agency_id`) REFERENCES `agencies`(`id`) 
-- ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Afficher toutes les agences
SELECT id, name, parent_agency_id, is_headquarters FROM agencies;

-- 4. SOLUTION TEMPORAIRE SI LES COLONNES N'EXISTENT PAS:
-- Si parent_agency_id n'existe pas, le filtre ne fonctionnera pas
-- En attendant, vous pouvez filtrer uniquement par agency_id de l'utilisateur
