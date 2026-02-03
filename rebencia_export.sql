-- REBENCIA DATABASE EXPORT
-- Base de données: rebe_RebenciaDB
-- Date: 3 février 2026

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- ============================================
-- Nettoyage des données existantes
-- ============================================
DELETE FROM `role_permissions`;
DELETE FROM `users`;
DELETE FROM `permissions`;
DELETE FROM `roles`;

-- Reset AUTO_INCREMENT
ALTER TABLE `roles` AUTO_INCREMENT = 1;
ALTER TABLE `permissions` AUTO_INCREMENT = 1;
ALTER TABLE `role_permissions` AUTO_INCREMENT = 1;
ALTER TABLE `users` AUTO_INCREMENT = 1;

-- ============================================
-- TABLE: roles
-- ============================================
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `description`, `level`) VALUES
(1, 'Super Admin', 'Administrateur global du système', 100),
(2, 'Directeur Siège', 'Directeur du siège social', 90),
(3, 'Manager Siège', 'Manager du siège social', 80),
(4, 'Directeur Agence', 'Directeur d\'agence', 70),
(5, 'Manager Agence', 'Manager d\'agence', 60),
(6, 'Agent Immobilier', 'Agent immobilier', 50),
(7, 'Assistant', 'Assistant administratif', 40);

-- ============================================
-- TABLE: permissions
-- ============================================
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `name`, `description`, `module`) VALUES
(1, 'view_dashboard', 'Voir le tableau de bord', 'dashboard'),
(2, 'manage_users', 'Gérer les utilisateurs', 'users'),
(3, 'manage_roles', 'Gérer les rôles', 'users'),
(4, 'manage_permissions', 'Gérer les permissions', 'users'),
(5, 'view_properties', 'Voir les biens', 'properties'),
(6, 'create_properties', 'Créer des biens', 'properties'),
(7, 'edit_properties', 'Modifier des biens', 'properties'),
(8, 'delete_properties', 'Supprimer des biens', 'properties'),
(9, 'view_clients', 'Voir les clients', 'clients'),
(10, 'create_clients', 'Créer des clients', 'clients'),
(11, 'edit_clients', 'Modifier des clients', 'clients'),
(12, 'delete_clients', 'Supprimer des clients', 'clients'),
(13, 'view_transactions', 'Voir les transactions', 'transactions'),
(14, 'create_transactions', 'Créer des transactions', 'transactions'),
(15, 'edit_transactions', 'Modifier des transactions', 'transactions'),
(16, 'delete_transactions', 'Supprimer des transactions', 'transactions'),
(17, 'view_commissions', 'Voir les commissions', 'commissions'),
(18, 'manage_commissions', 'Gérer les commissions', 'commissions'),
(19, 'view_agencies', 'Voir les agences', 'agencies'),
(20, 'manage_agencies', 'Gérer les agences', 'agencies'),
(21, 'view_reports', 'Voir les rapports', 'reports'),
(22, 'export_data', 'Exporter des données', 'reports'),
(23, 'manage_settings', 'Gérer les paramètres', 'settings'),
(24, 'manage_workflows', 'Gérer les workflows', 'workflows'),
(25, 'view_estimations', 'Voir les estimations IA', 'estimations'),
(26, 'create_estimations', 'Créer des estimations IA', 'estimations'),
(27, 'manage_zones', 'Gérer les zones', 'zones'),
(28, 'view_audit_logs', 'Voir les logs d\'audit', 'audit'),
(29, 'manage_templates', 'Gérer les templates', 'templates'),
(30, 'send_notifications', 'Envoyer des notifications', 'notifications');

-- ============================================
-- TABLE: role_permissions
-- ============================================
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `permission_id` int(10) unsigned NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_role_permission` (`role_id`,`permission_id`),
  KEY `permission_id` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Toutes les permissions pour Super Admin
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1), (1, 2), (1, 3), (1, 4), (1, 5), (1, 6), (1, 7), (1, 8), (1, 9), (1, 10),
(1, 11), (1, 12), (1, 13), (1, 14), (1, 15), (1, 16), (1, 17), (1, 18), (1, 19), (1, 20),
(1, 21), (1, 22), (1, 23), (1, 24), (1, 25), (1, 26), (1, 27), (1, 28), (1, 29), (1, 30);

-- ============================================
-- TABLE: users
-- ============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(10) unsigned NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `role_id` (`role_id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Utilisateur admin avec mot de passe: Admin@2026
INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `status`) VALUES
(1, 1, 'admin', 'admin@rebencia.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'REBENCIA', '+216 20 000 000', 'active');

SET FOREIGN_KEY_CHECKS = 1;
