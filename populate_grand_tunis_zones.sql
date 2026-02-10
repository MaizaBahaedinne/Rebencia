-- ============================================================================
-- SCRIPT DE REMPLISSAGE DE LA TABLE ZONES - GRAND TUNIS
-- Structure en arbre (Tree) : Gouvernorats > Villes
-- ============================================================================

-- Vider toutes les données existantes
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM zones;
ALTER TABLE zones AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- GOUVERNORATS (Niveau 1 - Racine de l'arbre)
-- ============================================================================

INSERT INTO zones (id, name, name_ar, name_en, type, parent_id, country) VALUES
(1, 'Tunis', 'تونس', 'Tunis', 'governorate', NULL, 'Tunisia'),
(2, 'Ariana', 'أريانة', 'Ariana', 'governorate', NULL, 'Tunisia'),
(3, 'Ben Arous', 'بن عروس', 'Ben Arous', 'governorate', NULL, 'Tunisia'),
(4, 'Manouba', 'منوبة', 'Manouba', 'governorate', NULL, 'Tunisia');

-- ============================================================================
-- ┌─ TUNIS (ID: 1) - Gouvernorat
-- │  └─ Villes (24 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
-- Villes prestigieuses
('La Marsa', 'المرسى', 'La Marsa', 'city', 1, 'Tunisia'),                    -- ├─ La Marsa ⭐⭐⭐⭐⭐
('Carthage', 'قرطاج', 'Carthage', 'city', 1, 'Tunisia'),                      -- ├─ Carthage ⭐⭐⭐⭐⭐
('Sidi Bou Said', 'سيدي بوسعيد', 'Sidi Bou Said', 'city', 1, 'Tunisia'),     -- ├─ Sidi Bou Said ⭐⭐⭐⭐⭐
('Les Berges du Lac', 'ضفاف البحيرة', 'Les Berges du Lac', 'city', 1, 'Tunisia'), -- ├─ Les Berges du Lac ⭐⭐⭐⭐⭐

-- Quartiers résidentiels du Nord
('Ennasr', 'النصر', 'Ennasr', 'city', 1, 'Tunisia'),                          -- ├─ Ennasr ⭐⭐⭐⭐⭐
('El Menzah', 'المنزه', 'El Menzah', 'city', 1, 'Tunisia'),                   -- ├─ El Menzah ⭐⭐⭐⭐⭐
('El Manar', 'المنار', 'El Manar', 'city', 1, 'Tunisia'),                     -- ├─ El Manar ⭐⭐⭐⭐
('Lac 1', 'البحيرة 1', 'Lac 1', 'city', 1, 'Tunisia'),                        -- ├─ Lac 1 ⭐⭐⭐⭐
('Lac 2', 'البحيرة 2', 'Lac 2', 'city', 1, 'Tunisia'),                        -- ├─ Lac 2 ⭐⭐⭐⭐

-- Zones côtières
('La Goulette', 'حلق الوادي', 'La Goulette', 'city', 1, 'Tunisia'),           -- ├─ La Goulette ⭐⭐⭐⭐
('Le Kram', 'الكرم', 'Le Kram', 'city', 1, 'Tunisia'),                        -- ├─ Le Kram ⭐⭐⭐

-- Centre et zones administratives
('Tunis Ville', 'مدينة تونس', 'Tunis City', 'city', 1, 'Tunisia'),            -- ├─ Tunis Ville (Centre)
('Le Bardo', 'باردو', 'Le Bardo', 'city', 1, 'Tunisia'),                      -- ├─ Le Bardo
('Centre Urbain Nord', 'المركز الحضري الشمالي', 'Centre Urbain Nord', 'city', 1, 'Tunisia'), -- ├─ Centre Urbain Nord

-- Quartiers populaires et mixtes
('Ezzouhour', 'الزهور', 'Ezzouhour', 'city', 1, 'Tunisia'),                   -- ├─ Ezzouhour
('Cité El Khadra', 'مدينة الخضراء', 'Cite El Khadra', 'city', 1, 'Tunisia'),  -- ├─ Cité El Khadra
('El Omrane', 'العمران', 'El Omrane', 'city', 1, 'Tunisia'),                  -- ├─ El Omrane
('El Omrane Supérieur', 'العمران الأعلى', 'El Omrane Superieur', 'city', 1, 'Tunisia'), -- ├─ El Omrane Supérieur
('Bab El Khadra', 'باب الخضراء', 'Bab El Khadra', 'city', 1, 'Tunisia'),      -- ├─ Bab El Khadra
('Montplaisir', 'مونبليزير', 'Montplaisir', 'city', 1, 'Tunisia'),            -- ├─ Montplaisir
('Belvedere', 'بلفيدير', 'Belvedere', 'city', 1, 'Tunisia'),                  -- ├─ Belvedere
('Mutuelle Ville', 'المتيوال فيل', 'Mutuelle Ville', 'city', 1, 'Tunisia'),   -- ├─ Mutuelle Ville
('Lafayette', 'لافاييت', 'Lafayette', 'city', 1, 'Tunisia'),                  -- ├─ Lafayette
('Cite Olympique', 'المدينة الأولمبية', 'Cite Olympique', 'city', 1, 'Tunisia'); -- └─ Cite Olympique

-- ============================================================================
-- ┌─ ARIANA (ID: 2) - Gouvernorat
-- │  └─ Villes (11 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
-- Zones résidentielles haut standing
('Ariana Ville', 'مدينة أريانة', 'Ariana City', 'city', 2, 'Tunisia'),        -- ├─ Ariana Ville ⭐⭐⭐⭐
('Soukra', 'سكرة', 'Soukra', 'city', 2, 'Tunisia'),                           -- ├─ Soukra ⭐⭐⭐⭐
('Raoued', 'رواد', 'Raoued', 'city', 2, 'Tunisia'),                           -- ├─ Raoued ⭐⭐⭐⭐
('Cite Ennasr', 'مدينة النصر', 'Cite Ennasr', 'city', 2, 'Tunisia'),          -- ├─ Cite Ennasr ⭐⭐⭐
('Ghazela', 'غزالة', 'Ghazela', 'city', 2, 'Tunisia'),                        -- ├─ Ghazela ⭐⭐⭐

-- Zones nord
('Borj Louzir', 'برج الوزير', 'Borj Louzir', 'city', 2, 'Tunisia'),           -- ├─ Borj Louzir
('Jardins d El Menzah', 'حدائق المنزه', 'Jardins d El Menzah', 'city', 2, 'Tunisia'), -- ├─ Jardins d'El Menzah
('Kalaat El Andalous', 'قلعة الأندلس', 'Kalaat El Andalous', 'city', 2, 'Tunisia'), -- ├─ Kalaat El Andalous
('Sidi Thabet', 'سيدي ثابت', 'Sidi Thabet', 'city', 2, 'Tunisia'),            -- ├─ Sidi Thabet

-- Zones périphériques
('Mnihla', 'منيهلة', 'Mnihla', 'city', 2, 'Tunisia'),                         -- ├─ Mnihla
('Ettadhamen', 'التضامن', 'Ettadhamen', 'city', 2, 'Tunisia');                -- └─ Ettadhamen

-- ============================================================================
-- ┌─ BEN AROUS (ID: 3) - Gouvernorat
-- │  └─ Villes (13 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
-- Banlieue Sud - Zones modernes
('El Mourouj', 'المروج', 'El Mourouj', 'city', 3, 'Tunisia'),                  -- ├─ El Mourouj ⭐⭐⭐⭐
('Megrine', 'المقرين', 'Megrine', 'city', 3, 'Tunisia'),                       -- ├─ Megrine ⭐⭐⭐⭐
('Ezzahra', 'الزهراء', 'Ezzahra', 'city', 3, 'Tunisia'),                       -- ├─ Ezzahra ⭐⭐⭐
('Radès', 'رادس', 'Rades', 'city', 3, 'Tunisia'),                             -- ├─ Radès ⭐⭐⭐

-- Zones côtières
('Hammam Lif', 'حمام الأنف', 'Hammam Lif', 'city', 3, 'Tunisia'),              -- ├─ Hammam Lif ⭐⭐⭐
('Hammam Chott', 'حمام الشط', 'Hammam Chott', 'city', 3, 'Tunisia'),           -- ├─ Hammam Chott
('Borj Cedria', 'برج السدرية', 'Borj Cedria', 'city', 3, 'Tunisia'),          -- ├─ Borj Cedria

-- Zones centre
('Ben Arous Ville', 'مدينة بن عروس', 'Ben Arous City', 'city', 3, 'Tunisia'), -- ├─ Ben Arous Ville
('Mohamedia', 'المحمدية', 'Mohamedia', 'city', 3, 'Tunisia'),                  -- ├─ Mohamedia
('Nouvelle Medina', 'المدينة الجديدة', 'Nouvelle Medina', 'city', 3, 'Tunisia'), -- ├─ Nouvelle Medina
('Fouchana', 'فوشانة', 'Fouchana', 'city', 3, 'Tunisia'),                      -- ├─ Fouchana
('Boumhel', 'بومهل', 'Boumhel', 'city', 3, 'Tunisia'),                        -- ├─ Boumhel
('Mornag', 'مرناق', 'Mornag', 'city', 3, 'Tunisia');                          -- └─ Mornag

-- ============================================================================
-- ┌─ MANOUBA (ID: 4) - Gouvernorat
-- │  └─ Villes (9 villes)
-- ============================================================================

INSERT INTO zones (name, name_ar, name_en, type, parent_id, country) VALUES
-- Banlieue Ouest
('Manouba Ville', 'مدينة منوبة', 'Manouba City', 'city', 4, 'Tunisia'),        -- ├─ Manouba Ville ⭐⭐⭐
('Oued Ellil', 'وادي الليل', 'Oued Ellil', 'city', 4, 'Tunisia'),             -- ├─ Oued Ellil ⭐⭐⭐
('Den Den', 'دندان', 'Den Den', 'city', 4, 'Tunisia'),                        -- ├─ Den Den
('Douar Hicher', 'دوار هيشر', 'Douar Hicher', 'city', 4, 'Tunisia'),          -- ├─ Douar Hicher
('Mornaguia', 'المرناقية', 'Mornaguia', 'city', 4, 'Tunisia'),                -- ├─ Mornaguia

-- Zones agricoles et périurbaines
('Tebourba', 'طبربة', 'Tebourba', 'city', 4, 'Tunisia'),                       -- ├─ Tebourba
('Jedaida', 'الجديدة', 'Jedaida', 'city', 4, 'Tunisia'),                       -- ├─ Jedaida
('Borj El Amri', 'برج العامري', 'Borj El Amri', 'city', 4, 'Tunisia'),        -- ├─ Borj El Amri
('El Battan', 'البطان', 'El Battan', 'city', 4, 'Tunisia');                    -- └─ El Battan

-- ============================================================================
-- CONFIGURATION DES SCORES DE POPULARITÉ
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
-- STATISTIQUES ET VÉRIFICATION
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
