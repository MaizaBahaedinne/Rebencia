-- ============================================================
-- REBENCIA - Schéma Base de Données MySQL
-- Plateforme Immobilière Multi-Rôles
-- Version 1.0 - Architecture complète
-- ============================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

CREATE DATABASE IF NOT EXISTS `rebencia` 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE `rebencia`;

-- ============================================================
-- RÔLES
-- ============================================================
CREATE TABLE `roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(50)  NOT NULL UNIQUE,
  `label`       VARCHAR(100) NOT NULL,
  `description` TEXT,
  `color`       VARCHAR(20)  DEFAULT '#6c757d',
  `is_active`   TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  DATETIME,
  `updated_at`  DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`name`, `label`, `description`, `color`, `created_at`, `updated_at`) VALUES
('director',      'Directeur d\'Agence',  'Accès total à la plateforme, gestion stratégique', '#dc3545', NOW(), NOW()),
('expert',        'Expert Immobilier',    'Gestion des biens et suivi des ventes', '#0d6efd', NOW(), NOW()),
('coordinator',   'Coordinateur',         'Gestion des leads, équipe et planning', '#198754', NOW(), NOW()),
('collaborator',  'Collaborateur',        'Accès aux tâches et biens assignés', '#fd7e14', NOW(), NOW());

-- ============================================================
-- PERMISSIONS
-- ============================================================
CREATE TABLE `permissions` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(100) NOT NULL UNIQUE,
  `label`       VARCHAR(150) NOT NULL,
  `module`      VARCHAR(50)  NOT NULL,
  `created_at`  DATETIME,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`name`, `label`, `module`, `created_at`) VALUES
-- Utilisateurs
('users.view',        'Voir les utilisateurs',            'users',      NOW()),
('users.create',      'Créer des utilisateurs',           'users',      NOW()),
('users.edit',        'Modifier des utilisateurs',        'users',      NOW()),
('users.delete',      'Supprimer des utilisateurs',       'users',      NOW()),
-- Rôles
('roles.view',        'Voir la matrice des rôles',        'roles',      NOW()),
('roles.manage',      'Gérer les rôles et permissions',   'roles',      NOW()),
-- Biens Immobiliers
('properties.view',   'Voir les biens',                   'properties', NOW()),
('properties.create', 'Créer des biens',                  'properties', NOW()),
('properties.edit',   'Modifier des biens',               'properties', NOW()),
('properties.delete', 'Supprimer des biens',              'properties', NOW()),
('properties.publish','Publier/Valider des annonces',     'properties', NOW()),
-- Leads
('leads.view',        'Voir les leads',                   'leads',      NOW()),
('leads.create',      'Créer des leads',                  'leads',      NOW()),
('leads.edit',        'Modifier des leads',               'leads',      NOW()),
('leads.delete',      'Supprimer des leads',              'leads',      NOW()),
('leads.assign',      'Assigner des leads',               'leads',      NOW()),
-- Statistiques
('stats.view',        'Accéder aux statistiques',         'stats',      NOW()),
('stats.export',      'Exporter les statistiques',        'stats',      NOW()),
-- Système
('system.logs',       'Accéder aux logs système',         'system',     NOW()),
('system.deploy',     'Accéder au module déploiement',    'system',     NOW()),
('system.settings',   'Modifier les paramètres système',  'system',     NOW());

-- ============================================================
-- PERMISSIONS PAR RÔLE
-- ============================================================
CREATE TABLE `role_permissions` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`       INT UNSIGNED NOT NULL,
  `permission_id` INT UNSIGNED NOT NULL,
  `created_at`    DATETIME,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_role_perm` (`role_id`, `permission_id`),
  FOREIGN KEY (`role_id`)       REFERENCES `roles`(`id`)       ON DELETE CASCADE,
  FOREIGN KEY (`permission_id`) REFERENCES `permissions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Directeur : toutes les permissions
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 1, id, NOW() FROM `permissions`;

-- Expert : biens + leads view + stats
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 2, id, NOW() FROM `permissions` 
WHERE `name` IN ('properties.view','properties.create','properties.edit','properties.publish',
                 'leads.view','leads.edit','stats.view');

-- Coordinateur : leads complets + biens view + stats + users view
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 3, id, NOW() FROM `permissions` 
WHERE `name` IN ('leads.view','leads.create','leads.edit','leads.assign',
                 'properties.view','users.view','stats.view');

-- Collaborateur : biens view + leads view assignés
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT 4, id, NOW() FROM `permissions` 
WHERE `name` IN ('properties.view','leads.view','leads.edit');

-- ============================================================
-- UTILISATEURS
-- ============================================================
CREATE TABLE `users` (
  `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`         INT UNSIGNED NOT NULL,
  `first_name`      VARCHAR(100) NOT NULL,
  `last_name`       VARCHAR(100) NOT NULL,
  `email`           VARCHAR(191) NOT NULL UNIQUE,
  `phone`           VARCHAR(30),
  `password_hash`   VARCHAR(255) NOT NULL,
  `avatar`          VARCHAR(255),
  `status`          ENUM('active','pending','suspended') NOT NULL DEFAULT 'pending',
  `last_login_at`   DATETIME,
  `last_login_ip`   VARCHAR(45),
  `remember_token`  VARCHAR(100),
  `created_at`      DATETIME,
  `updated_at`      DATETIME,
  `deleted_at`      DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Directeur par défaut (password: Admin@2024)
INSERT INTO `users` (`role_id`, `first_name`, `last_name`, `email`, `password_hash`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Rebencia', 'admin@rebencia.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uXOFUpEYK', 'active', NOW(), NOW());

-- ============================================================
-- BIENS IMMOBILIERS
-- ============================================================
CREATE TABLE `properties` (
  `id`                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`          VARCHAR(30)  NOT NULL UNIQUE,
  `agent_id`           INT UNSIGNED NOT NULL,
  `title`              VARCHAR(255) NOT NULL,
  `description`        TEXT,
  `type`               ENUM('apartment','house','villa','commercial','land','office') NOT NULL DEFAULT 'apartment',
  `transaction_type`   ENUM('sale','rent') NOT NULL DEFAULT 'sale',
  `status`             ENUM('available','reserved','sold','rented','inactive') NOT NULL DEFAULT 'available',
  `price`              DECIMAL(15,2) NOT NULL DEFAULT 0,
  `surface`            DECIMAL(10,2),
  `rooms`              TINYINT UNSIGNED,
  `bedrooms`           TINYINT UNSIGNED,
  `bathrooms`          TINYINT UNSIGNED,
  `floor`              TINYINT,
  `total_floors`       TINYINT UNSIGNED,
  `parking`            TINYINT(1) DEFAULT 0,
  `furnished`          TINYINT(1) DEFAULT 0,
  `address`            VARCHAR(255),
  `city`               VARCHAR(100),
  `zone`               VARCHAR(100),
  `latitude`           DECIMAL(10,7),
  `longitude`          DECIMAL(10,7),
  `features`           JSON,
  `is_published`       TINYINT(1) NOT NULL DEFAULT 0,
  `published_at`       DATETIME,
  `published_by`       INT UNSIGNED,
  `featured`           TINYINT(1) NOT NULL DEFAULT 0,
  `views_count`        INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`         DATETIME,
  `updated_at`         DATETIME,
  `deleted_at`         DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_status`   (`status`),
  KEY `idx_type`     (`type`),
  KEY `idx_city`     (`city`),
  KEY `idx_agent`    (`agent_id`),
  FOREIGN KEY (`agent_id`)      REFERENCES `users`(`id`) ON DELETE RESTRICT,
  FOREIGN KEY (`published_by`)  REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- IMAGES DES BIENS
-- ============================================================
CREATE TABLE `property_images` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id` INT UNSIGNED NOT NULL,
  `filename`    VARCHAR(255) NOT NULL,
  `path`        VARCHAR(500) NOT NULL,
  `alt_text`    VARCHAR(255),
  `is_primary`  TINYINT(1)   NOT NULL DEFAULT 0,
  `sort_order`  TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at`  DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- HISTORIQUE MODIFICATIONS BIENS
-- ============================================================
CREATE TABLE `property_history` (
  `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `property_id`  INT UNSIGNED NOT NULL,
  `user_id`      INT UNSIGNED NOT NULL,
  `field_name`   VARCHAR(100) NOT NULL,
  `old_value`    TEXT,
  `new_value`    TEXT,
  `created_at`   DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LEADS / CRM
-- ============================================================
CREATE TABLE `leads` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `assigned_to`    INT UNSIGNED,
  `property_id`    INT UNSIGNED,
  `first_name`     VARCHAR(100) NOT NULL,
  `last_name`      VARCHAR(100) NOT NULL,
  `email`          VARCHAR(191),
  `phone`          VARCHAR(30),
  `source`         ENUM('website','phone','referral','social','walk_in','other') NOT NULL DEFAULT 'website',
  `status`         ENUM('new','contacted','visit','negotiation','sold','lost') NOT NULL DEFAULT 'new',
  `budget_min`     DECIMAL(15,2),
  `budget_max`     DECIMAL(15,2),
  `notes`          TEXT,
  `property_type`  VARCHAR(50),
  `transaction_type` ENUM('sale','rent') DEFAULT 'sale',
  `priority`       ENUM('low','medium','high') NOT NULL DEFAULT 'medium',
  `next_follow_up` DATE,
  `created_at`     DATETIME,
  `updated_at`     DATETIME,
  `deleted_at`     DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_status`      (`status`),
  KEY `idx_assigned`    (`assigned_to`),
  KEY `idx_property`    (`property_id`),
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`)      ON DELETE SET NULL,
  FOREIGN KEY (`property_id`) REFERENCES `properties`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- HISTORIQUE STATUTS LEADS
-- ============================================================
CREATE TABLE `lead_status_history` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lead_id`     INT UNSIGNED NOT NULL,
  `user_id`     INT UNSIGNED NOT NULL,
  `old_status`  VARCHAR(50),
  `new_status`  VARCHAR(50) NOT NULL,
  `notes`       TEXT,
  `created_at`  DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- NOTES LEADS
-- ============================================================
CREATE TABLE `lead_notes` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `lead_id`    INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `content`    TEXT NOT NULL,
  `created_at` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`lead_id`) REFERENCES `leads`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOGS ACTIVITÉ UTILISATEURS
-- ============================================================
CREATE TABLE `activity_logs` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED,
  `action`      VARCHAR(100) NOT NULL,
  `module`      VARCHAR(50),
  `entity_type` VARCHAR(50),
  `entity_id`   INT UNSIGNED,
  `description` TEXT,
  `ip_address`  VARCHAR(45),
  `user_agent`  VARCHAR(500),
  `created_at`  DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_user`    (`user_id`),
  KEY `idx_action`  (`action`),
  KEY `idx_module`  (`module`),
  KEY `idx_created` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- LOGS SYSTÈME
-- ============================================================
CREATE TABLE `system_logs` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `level`       ENUM('debug','info','notice','warning','error','critical','alert','emergency') NOT NULL DEFAULT 'info',
  `channel`     VARCHAR(50) NOT NULL DEFAULT 'app',
  `message`     TEXT NOT NULL,
  `context`     JSON,
  `ip_address`  VARCHAR(45),
  `url`         VARCHAR(1000),
  `user_id`     INT UNSIGNED,
  `created_at`  DATETIME,
  PRIMARY KEY (`id`),
  KEY `idx_level`   (`level`),
  KEY `idx_channel` (`channel`),
  KEY `idx_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DÉPLOIEMENTS
-- ============================================================
CREATE TABLE `deployments` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `commit_hash`   VARCHAR(40),
  `commit_message`TEXT,
  `branch`        VARCHAR(100) DEFAULT 'main',
  `deployed_by`   INT UNSIGNED,
  `status`        ENUM('pending','running','success','failed') NOT NULL DEFAULT 'pending',
  `output`        TEXT,
  `started_at`    DATETIME,
  `completed_at`  DATETIME,
  `created_at`    DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`deployed_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
