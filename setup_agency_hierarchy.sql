-- =============================================
-- CONFIGURATION HIÉRARCHIE DES AGENCES
-- =============================================
-- Ce script configure la hiérarchie des agences pour la ségrégation des données

USE `rebe_RebenciaDB`;

-- 1. Ajouter les colonnes parent_agency_id et is_headquarters
ALTER TABLE `agencies` 
ADD COLUMN `parent_agency_id` INT(11) UNSIGNED NULL AFTER `id`,
ADD COLUMN `is_headquarters` TINYINT(1) DEFAULT 0 COMMENT '1 = Siège, 0 = Agence locale' AFTER `parent_agency_id`;

-- 2. Ajouter la clé étrangère
ALTER TABLE `agencies`
ADD CONSTRAINT `fk_agencies_parent` 
FOREIGN KEY (`parent_agency_id`) REFERENCES `agencies`(`id`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- 3. Configurer le siège principal (exemple)
-- Remplacer 1 par l'ID de votre siège principal
UPDATE agencies SET is_headquarters = 1 WHERE id = 1;

-- 4. Configurer la hiérarchie (exemples)
-- Agence 2 et 3 sont des sous-agences du siège (ID 1)
-- UPDATE agencies SET parent_agency_id = 1 WHERE id IN (2, 3);

-- Agence 4 est une sous-agence de l'agence 2
-- UPDATE agencies SET parent_agency_id = 2 WHERE id = 4;

-- 5. Vérifier la hiérarchie
SELECT 
    a1.id,
    a1.name AS 'Agence',
    CASE WHEN a1.is_headquarters = 1 THEN 'OUI' ELSE 'NON' END AS 'Siège',
    a2.name AS 'Agence Parente',
    (SELECT COUNT(*) FROM agencies WHERE parent_agency_id = a1.id) AS 'Nb Sous-Agences'
FROM agencies a1
LEFT JOIN agencies a2 ON a1.parent_agency_id = a2.id
ORDER BY a1.parent_agency_id, a1.id;

-- =============================================
-- EXEMPLE DE STRUCTURE HIÉRARCHIQUE
-- =============================================
/*
Siège National (ID: 1, is_headquarters=1, parent=NULL)
  ├─ Agence Paris (ID: 2, parent=1)
  │   ├─ Agence Paris 15ème (ID: 5, parent=2)
  │   └─ Agence Paris 8ème (ID: 6, parent=2)
  ├─ Agence Lyon (ID: 3, parent=1)
  └─ Agence Marseille (ID: 4, parent=1)

Résultats d'accès:
- Utilisateur du Siège National (ID:1) → Voit TOUT (1,2,3,4,5,6)
- Utilisateur Agence Paris (ID:2) → Voit Paris + sous-agences (2,5,6)
- Utilisateur Agence Paris 15ème (ID:5) → Voit uniquement (5)
- Utilisateur Agence Lyon (ID:3) → Voit uniquement (3)
*/
