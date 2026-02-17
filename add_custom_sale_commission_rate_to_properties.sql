-- Ajouter une colonne pour le taux de commission personnalisé par bien (ventes uniquement)
ALTER TABLE `properties` 
ADD COLUMN `custom_sale_commission_rate` DECIMAL(5,2) NULL DEFAULT NULL 
  COMMENT 'Taux de commission personnalisé pour la vente de ce bien (NULL = utiliser les règles standard)';
