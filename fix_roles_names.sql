-- =============================================
-- CORRIGER LES NOMS DE RÔLES
-- =============================================
-- Les noms techniques (name) doivent être en snake_case sans espaces
-- Les noms d'affichage (display_name) peuvent contenir des espaces

USE `rebe_RebenciaDB`;

-- Afficher les rôles actuels
SELECT id, name, display_name, level FROM roles;

-- Corriger les noms si nécessaire (remplacer espaces par underscores, mettre en minuscules)
UPDATE roles SET name = LOWER(REPLACE(name, ' ', '_'));
UPDATE roles SET name = LOWER(REPLACE(name, '-', '_'));

-- Vérifier le résultat
SELECT id, name, display_name, level FROM roles;
