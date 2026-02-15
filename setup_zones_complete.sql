-- ============================================================================
-- CRÉATION ET REMPLISSAGE DE LA TABLE ZONES - GRAND TUNIS
-- ============================================================================
-- Script complet pour créer la table manquante et insérer les données géographiques

USE `rebe_RebenciaDB`;

-- ============================================================================
-- 1. CRÉER LA TABLE ZONES SI ELLE N'EXISTE PAS
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
-- 2. VIDER LES DONNÉES EXISTANTES ET RÉINITIALISER L'AUTO_INCREMENT
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM zones;
ALTER TABLE zones AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- 3. GOUVERNORATS (Niveau 1 - Racine de l'arbre)
-- ============================================================================

INSERT INTO zones (id, name, name_ar, name_en, type, parent_id, country) VALUES
(1, 'Tunis', 'تونس', 'Tunis', 'governorate', NULL, 'Tunisia'),
(2, 'Ariana', 'أريانة', 'Ariana', 'governorate', NULL, 'Tunisia'),
(3, 'Ben Arous', 'بن عروس', 'Ben Arous', 'governorate', NULL, 'Tunisia'),
(4, 'Manouba', 'منوبة', 'Manouba', 'governorate', NULL, 'Tunisia');

-- ============================================================================
-- 4. TUNIS - VILLES (24 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
('La Marsa', 'المرسى', 'La Marsa', 'city', 1, 'Tunisia'),
('Carthage', 'قرطاج', 'Carthage', 'city', 1, 'Tunisia'),
('Sidi Bou Said', 'سيدي بوسعيد', 'Sidi Bou Said', 'city', 1, 'Tunisia'),
('Les Berges du Lac', 'ضفاف البحيرة', 'Les Berges du Lac', 'city', 1, 'Tunisia'),
('Ennasr', 'النصر', 'Ennasr', 'city', 1, 'Tunisia'),
('El Menzah', 'المنزه', 'El Menzah', 'city', 1, 'Tunisia'),
('El Manar', 'المنار', 'El Manar', 'city', 1, 'Tunisia'),
('Lac 1', 'البحيرة 1', 'Lac 1', 'city', 1, 'Tunisia'),
('Lac 2', 'البحيرة 2', 'Lac 2', 'city', 1, 'Tunisia'),
('La Goulette', 'حلق الوادي', 'La Goulette', 'city', 1, 'Tunisia'),
('Le Kram', 'الكرم', 'Le Kram', 'city', 1, 'Tunisia'),
('Tunis Ville', 'مدينة تونس', 'Tunis City', 'city', 1, 'Tunisia'),
('Le Bardo', 'باردو', 'Le Bardo', 'city', 1, 'Tunisia'),
('Centre Urbain Nord', 'المركز الحضري الشمالي', 'Centre Urbain Nord', 'city', 1, 'Tunisia'),
('Ezzouhour', 'الزهور', 'Ezzouhour', 'city', 1, 'Tunisia'),
('Cité El Khadra', 'مدينة الخضراء', 'Cite El Khadra', 'city', 1, 'Tunisia'),
('El Omrane', 'العمران', 'El Omrane', 'city', 1, 'Tunisia'),
('El Omrane Supérieur', 'العمران الأعلى', 'El Omrane Superieur', 'city', 1, 'Tunisia'),
('Bab El Khadra', 'باب الخضراء', 'Bab El Khadra', 'city', 1, 'Tunisia'),
('Montplaisir', 'مونبليزير', 'Montplaisir', 'city', 1, 'Tunisia'),
('Belvedere', 'بلفيدير', 'Belvedere', 'city', 1, 'Tunisia'),
('Mutuelle Ville', 'المتيوال فيل', 'Mutuelle Ville', 'city', 1, 'Tunisia'),
('Lafayette', 'لافاييت', 'Lafayette', 'city', 1, 'Tunisia'),
('Cite Olympique', 'المدينة الأولمبية', 'Cite Olympique', 'city', 1, 'Tunisia');

-- ============================================================================
-- 5. ARIANA - VILLES (11 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
('Ariana Ville', 'مدينة أريانة', 'Ariana City', 'city', 2, 'Tunisia'),
('Soukra', 'سكرة', 'Soukra', 'city', 2, 'Tunisia'),
('Raoued', 'رواد', 'Raoued', 'city', 2, 'Tunisia'),
('Cite Ennasr', 'مدينة النصر', 'Cite Ennasr', 'city', 2, 'Tunisia'),
('Ghazela', 'غزالة', 'Ghazela', 'city', 2, 'Tunisia'),
('Borj Louzir', 'برج الوزير', 'Borj Louzir', 'city', 2, 'Tunisia'),
('Jardins d El Menzah', 'حدائق المنزه', 'Jardins d El Menzah', 'city', 2, 'Tunisia'),
('Kalaat El Andalous', 'قلعة الأندلس', 'Kalaat El Andalous', 'city', 2, 'Tunisia'),
('Sidi Thabet', 'سيدي ثابت', 'Sidi Thabet', 'city', 2, 'Tunisia'),
('Mnihla', 'منيهلة', 'Mnihla', 'city', 2, 'Tunisia'),
('Ettadhamen', 'التضامن', 'Ettadhamen', 'city', 2, 'Tunisia');

-- ============================================================================
-- 6. BEN AROUS - VILLES (13 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
('El Mourouj', 'المروج', 'El Mourouj', 'city', 3, 'Tunisia'),
('Megrine', 'المقرين', 'Megrine', 'city', 3, 'Tunisia'),
('Ezzahra', 'الزهراء', 'Ezzahra', 'city', 3, 'Tunisia'),
('Radès', 'رادس', 'Rades', 'city', 3, 'Tunisia'),
('Hammam Lif', 'حمام الأنف', 'Hammam Lif', 'city', 3, 'Tunisia'),
('Hammam Chott', 'حمام الشط', 'Hammam Chott', 'city', 3, 'Tunisia'),
('Borj Cedria', 'برج السدرية', 'Borj Cedria', 'city', 3, 'Tunisia'),
('Ben Arous Ville', 'مدينة بن عروس', 'Ben Arous City', 'city', 3, 'Tunisia'),
('Mohamedia', 'المحمدية', 'Mohamedia', 'city', 3, 'Tunisia'),
('Nouvelle Medina', 'المدينة الجديدة', 'Nouvelle Medina', 'city', 3, 'Tunisia'),
('Fouchana', 'فوشانة', 'Fouchana', 'city', 3, 'Tunisia'),
('Boumhel', 'بومهل', 'Boumhel', 'city', 3, 'Tunisia'),
('Mornag', 'مرناق', 'Mornag', 'city', 3, 'Tunisia');

-- ============================================================================
-- 7. MANOUBA - VILLES (9 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
('Manouba Ville', 'مدينة منوبة', 'Manouba City', 'city', 4, 'Tunisia'),
('Oued Ellil', 'وادي الليل', 'Oued Ellil', 'city', 4, 'Tunisia'),
('Den Den', 'دندان', 'Den Den', 'city', 4, 'Tunisia'),
('Douar Hicher', 'دوار هيشر', 'Douar Hicher', 'city', 4, 'Tunisia'),
('Mornaguia', 'المرناقية', 'Mornaguia', 'city', 4, 'Tunisia'),
('Tebourba', 'طبربة', 'Tebourba', 'city', 4, 'Tunisia'),
('Jedaida', 'الجديدة', 'Jedaida', 'city', 4, 'Tunisia'),
('Borj El Amri', 'برج العامري', 'Borj El Amri', 'city', 4, 'Tunisia'),
('El Battan', 'البطان', 'El Battan', 'city', 4, 'Tunisia');

-- ============================================================================
-- 8. CONFIGURATION DES SCORES DE POPULARITÉ
-- ============================================================================

-- Niveau 5 étoiles (100) - Zones les plus demandées
UPDATE zones SET popularity_score = 100 WHERE name IN ('La Marsa', 'Carthage', 'Les Berges du Lac', 'Ennasr', 'El Menzah');

-- Niveau 4 étoiles (90) - Zones très recherchées
UPDATE zones SET popularity_score = 90 WHERE name IN ('Sidi Bou Said', 'La Goulette', 'El Manar', 'Lac 1', 'Lac 2');

-- Niveau 3 étoiles (80) - Zones populaires
UPDATE zones SET popularity_score = 80 WHERE name IN ('Ariana Ville', 'Soukra', 'Raoued', 'El Mourouj', 'Megrine');

-- Niveau 2 étoiles (70) - Zones en développement
UPDATE zones SET popularity_score = 70 WHERE name IN ('Ezzahra', 'Radès', 'Hammam Lif', 'Manouba Ville', 'Oued Ellil');

-- ============================================================================
-- 9. VÉRIFICATION ET STATISTIQUES
-- ============================================================================

SELECT '✓ Données du Grand Tunis insérées avec succès!' AS message;
SELECT CONCAT('Total: ', COUNT(*), ' zones') as total_zones FROM zones;
SELECT type as Type, COUNT(*) as Nombre FROM zones GROUP BY type;

-- Affichage de l'arbre complet
SELECT 
    CASE 
        WHEN z.parent_id IS NULL THEN CONCAT('┌─ ', z.name, ' (', z.type, ')')
        ELSE CONCAT('│  ├─ ', z.name)
    END as Structure
FROM zones z
ORDER BY z.parent_id, z.popularity_score DESC, z.name;
