-- Table pour stocker les taux par défaut du système
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
