-- Ajouter les colonnes de répartition agent/agence pour ventes et locations
ALTER TABLE `users` 
ADD COLUMN `agent_commission_share_sale` DECIMAL(5,2) NOT NULL DEFAULT 50.00 
  COMMENT 'Pourcentage de commission pour l''agent sur ventes (reste = agence)',
ADD COLUMN `agent_commission_share_rent` DECIMAL(5,2) NOT NULL DEFAULT 50.00 
  COMMENT 'Pourcentage de commission pour l''agent sur locations (reste = agence)';
