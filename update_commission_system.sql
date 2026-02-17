-- ============================================================================
-- Script de mise à jour du système de commission
-- Date: 17 Février 2026
-- ============================================================================

-- 1. Ajouter les colonnes de répartition agent/agence (split sale/rent) à la table users
ALTER TABLE `users` 
ADD COLUMN `agent_commission_share_sale` DECIMAL(5,2) NOT NULL DEFAULT 50.00 
  COMMENT 'Pourcentage de commission pour l''agent sur ventes (reste = agence)',
ADD COLUMN `agent_commission_share_rent` DECIMAL(5,2) NOT NULL DEFAULT 50.00 
  COMMENT 'Pourcentage de commission pour l''agent sur locations (reste = agence)';

-- 2. Ajouter la colonne de taux personnalisé par bien (ventes uniquement) à la table properties
ALTER TABLE `properties` 
ADD COLUMN `custom_sale_commission_rate` DECIMAL(5,2) NULL DEFAULT NULL 
  COMMENT 'Taux de commission personnalisé pour la vente de ce bien (NULL = utiliser les règles standard)';

-- 3. Créer la table pour stocker les taux par défaut du système
CREATE TABLE IF NOT EXISTS `commission_defaults` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `agent_commission_share_sale` decimal(5,2) NOT NULL DEFAULT '50.00' COMMENT 'Taux par défaut du système: % commission pour agent sur ventes',
  `agent_commission_share_rent` decimal(5,2) NOT NULL DEFAULT '50.00' COMMENT 'Taux par défaut du système: % commission pour agent sur locations',
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insérer la configuration par défaut
INSERT INTO `commission_defaults` (`agent_commission_share_sale`, `agent_commission_share_rent`, `updated_at`) 
VALUES (50.00, 50.00, NOW());

-- ============================================================================
-- Note: Les colonnes commission_sale_percentage et commission_rent_percentage 
-- des utilisateurs ne sont plus utilisées. Les taux sont maintenant définis 
-- uniquement dans les règles de commission ou au niveau du bien (custom_sale_commission_rate).
-- ============================================================================
