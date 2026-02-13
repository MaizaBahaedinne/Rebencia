-- Ajouter la colonne boundary_coordinates à la table zones
ALTER TABLE zones ADD COLUMN boundary_coordinates TEXT NULL COMMENT 'JSON array of polygon coordinates [[lat, lng], ...]';
