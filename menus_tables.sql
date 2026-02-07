-- Création des tables pour le système de menus

-- Table des menus
CREATE TABLE IF NOT EXISTS `menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `parent_id` int(11) unsigned DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table de liaison menu-rôle
CREATE TABLE IF NOT EXISTS `role_menus` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `role_menus_menu_fk` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `role_menus_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des menus par défaut (exemples)
INSERT INTO `menus` (`title`, `icon`, `url`, `parent_id`, `order`, `is_active`) VALUES
('Dashboard', 'fas fa-tachometer-alt', 'admin/dashboard', NULL, 1, 1),
('Utilisateurs', 'fas fa-users', 'admin/users', NULL, 2, 1),
('Rôles', 'fas fa-user-shield', 'admin/roles', NULL, 3, 1),
('Agences', 'fas fa-building', 'admin/agencies', NULL, 4, 1),
('Biens', 'fas fa-home', 'admin/properties', NULL, 5, 1),
('Clients', 'fas fa-handshake', 'admin/clients', NULL, 6, 1),
('Transactions', 'fas fa-money-bill-wave', 'admin/transactions', NULL, 7, 1),
('Commissions', 'fas fa-percentage', 'admin/commissions', NULL, 8, 1),
('Hiérarchie', 'fas fa-sitemap', 'admin/hierarchy', NULL, 9, 1),
('Rapports', 'fas fa-chart-bar', 'admin/reports', NULL, 10, 1);
