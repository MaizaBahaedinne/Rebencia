-- ============================================================================
-- CRÉATION DE LA TABLE ZONES - GRAND TUNIS
-- ============================================================================
-- Ce script crée la table zones manquante dans la base de données

USE `rebe_RebenciaDB`;

-- ============================================================================
-- 1. CRÉER LA TABLE ZONES
-- ============================================================================

CREATE TABLE IF NOT EXISTS `zones` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL COMMENT 'Zone name in French',
    `name_ar` VARCHAR(255) NULL COMMENT 'Zone name in Arabic',
    `name_en` VARCHAR(255) NULL COMMENT 'Zone name in English',
    `type` ENUM('governorate', 'city', 'region', 'district') DEFAULT 'city' COMMENT 'Type of geographic zone',
    `parent_id` INT(11) UNSIGNED NULL COMMENT 'Parent zone ID for hierarchical structure',
    `country` VARCHAR(100) DEFAULT 'Tunisia' COMMENT 'Country name',
    `latitude` DECIMAL(10, 8) NULL COMMENT 'Latitude coordinate',
    `longitude` DECIMAL(11, 8) NULL COMMENT 'Longitude coordinate',
    `popularity_score` INT(3) DEFAULT 0 COMMENT 'Popularity score from 0 to 100',
    `boundary_coordinates` LONGTEXT NULL COMMENT 'JSON array of polygon coordinates for zone boundary',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    INDEX `idx_parent_id` (`parent_id`),
    INDEX `idx_type` (`type`),
    INDEX `idx_country` (`country`),
    CONSTRAINT `fk_zones_parent` FOREIGN KEY (`parent_id`) REFERENCES `zones` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Geographic zones for property listings';

-- ============================================================================
-- 2. VÉRIFIER L'INSERTION EST POSSIBLE
-- ============================================================================

SELECT '✓ Table zones créée avec succès!' AS message;
