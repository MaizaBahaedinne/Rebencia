-- =============================================
-- REMPLISSAGE MATRICE DES PERMISSIONS
-- =============================================

USE `rebe_RebenciaDB`;

-- Vider les tables existantes (DELETE au lieu de TRUNCATE pour éviter les problèmes de clés étrangères)
DELETE FROM `role_permissions`;
DELETE FROM `permissions`;

-- =============================================
-- 1. CRÉER LES PERMISSIONS PAR MODULE
-- =============================================

INSERT INTO `permissions` (`name`, `display_name`, `description`, `module`, `created_at`) VALUES
-- GESTION
('properties_view', 'Voir Biens', 'Voir les biens immobiliers', 'properties', NOW()),
('properties_create', 'Créer Biens', 'Créer des biens immobiliers', 'properties', NOW()),
('properties_update', 'Modifier Biens', 'Modifier des biens immobiliers', 'properties', NOW()),
('properties_delete', 'Supprimer Biens', 'Supprimer des biens immobiliers', 'properties', NOW()),
('properties_export', 'Exporter Biens', 'Exporter des biens immobiliers', 'properties', NOW()),

('clients_view', 'Voir Clients', 'Voir les clients', 'clients', NOW()),
('clients_create', 'Créer Clients', 'Créer des clients', 'clients', NOW()),
('clients_update', 'Modifier Clients', 'Modifier des clients', 'clients', NOW()),
('clients_delete', 'Supprimer Clients', 'Supprimer des clients', 'clients', NOW()),
('clients_export', 'Exporter Clients', 'Exporter des clients', 'clients', NOW()),

('transactions_view', 'Voir Transactions', 'Voir les transactions', 'transactions', NOW()),
('transactions_create', 'Créer Transactions', 'Créer des transactions', 'transactions', NOW()),
('transactions_update', 'Modifier Transactions', 'Modifier des transactions', 'transactions', NOW()),
('transactions_delete', 'Supprimer Transactions', 'Supprimer des transactions', 'transactions', NOW()),
('transactions_export', 'Exporter Transactions', 'Exporter des transactions', 'transactions', NOW()),

('commissions_view', 'Voir Commissions', 'Voir les commissions', 'commissions', NOW()),
('commissions_create', 'Créer Commissions', 'Créer des commissions', 'commissions', NOW()),
('commissions_update', 'Modifier Commissions', 'Modifier des commissions', 'commissions', NOW()),
('commissions_delete', 'Supprimer Commissions', 'Supprimer des commissions', 'commissions', NOW()),
('commissions_export', 'Exporter Commissions', 'Exporter des commissions', 'commissions', NOW()),

-- ORGANISATION
('agencies_view', 'Voir Agences', 'Voir les agences', 'agencies', NOW()),
('agencies_create', 'Créer Agences', 'Créer des agences', 'agencies', NOW()),
('agencies_update', 'Modifier Agences', 'Modifier des agences', 'agencies', NOW()),
('agencies_delete', 'Supprimer Agences', 'Supprimer des agences', 'agencies', NOW()),
('agencies_export', 'Exporter Agences', 'Exporter des agences', 'agencies', NOW()),

('users_view', 'Voir Utilisateurs', 'Voir les utilisateurs', 'users', NOW()),
('users_create', 'Créer Utilisateurs', 'Créer des utilisateurs', 'users', NOW()),
('users_update', 'Modifier Utilisateurs', 'Modifier des utilisateurs', 'users', NOW()),
('users_delete', 'Supprimer Utilisateurs', 'Supprimer des utilisateurs', 'users', NOW()),
('users_export', 'Exporter Utilisateurs', 'Exporter des utilisateurs', 'users', NOW()),

-- OUTILS
('zones_view', 'Voir Zones', 'Voir les zones', 'zones', NOW()),
('zones_create', 'Créer Zones', 'Créer des zones', 'zones', NOW()),
('zones_update', 'Modifier Zones', 'Modifier des zones', 'zones', NOW()),
('zones_delete', 'Supprimer Zones', 'Supprimer des zones', 'zones', NOW()),
('zones_export', 'Exporter Zones', 'Exporter des zones', 'zones', NOW()),

('reports_view', 'Voir Rapports', 'Voir les rapports', 'reports', NOW()),
('reports_create', 'Créer Rapports', 'Créer des rapports', 'reports', NOW()),
('reports_update', 'Modifier Rapports', 'Modifier des rapports', 'reports', NOW()),
('reports_delete', 'Supprimer Rapports', 'Supprimer des rapports', 'reports', NOW()),
('reports_export', 'Exporter Rapports', 'Exporter des rapports', 'reports', NOW());

-- =============================================
-- 2. ASSIGNER LES PERMISSIONS AUX RÔLES
-- =============================================

-- Variables pour les IDs (par niveau au lieu de nom)
SET @super_admin_id = (SELECT id FROM roles WHERE level = 100 LIMIT 1);
SET @admin_id = (SELECT id FROM roles WHERE level BETWEEN 80 AND 89 LIMIT 1);
SET @manager_id = (SELECT id FROM roles WHERE level BETWEEN 50 AND 79 LIMIT 1);
SET @agent_id = (SELECT id FROM roles WHERE level BETWEEN 20 AND 49 LIMIT 1);

-- =============================================
-- SUPER ADMIN (Niveau 100) - TOUT
-- =============================================
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @super_admin_id, id, 1, 1, 1, 1, 1
FROM permissions;

-- =============================================
-- ADMIN (Niveau 80-89) - PRESQUE TOUT
-- =============================================
-- Properties
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 1
FROM permissions WHERE module = 'properties';

-- Clients
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 1
FROM permissions WHERE module = 'clients';

-- Transactions
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 1
FROM permissions WHERE module = 'transactions';

-- Commissions
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 0
FROM permissions WHERE module = 'commissions';

-- Agencies
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 0
FROM permissions WHERE module = 'agencies';

-- Users
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 0
FROM permissions WHERE module = 'users';

-- Zones
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 1, 0
FROM permissions WHERE module = 'zones';

-- Reports
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @admin_id, id, 1, 1, 1, 0, 0
FROM permissions WHERE module = 'reports';

-- =============================================
-- MANAGER (Niveau 50-79) - GESTION ÉQUIPE
-- =============================================
-- Properties - Complet sauf delete
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 1, 1, 1, 0, 1
FROM permissions WHERE module = 'properties';

-- Clients - Complet sauf delete
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 1, 1, 1, 0, 1
FROM permissions WHERE module = 'clients';

-- Transactions - Complet sauf delete
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 1, 1, 1, 0, 1
FROM permissions WHERE module = 'transactions';

-- Commissions - Lecture seule
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 0, 1, 0, 0, 0
FROM permissions WHERE module = 'commissions';

-- Agencies - Lecture seule
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 0, 1, 0, 0, 0
FROM permissions WHERE module = 'agencies';

-- Users - Lecture + modification (pas de création/suppression)
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 0, 1, 1, 0, 0
FROM permissions WHERE module = 'users';

-- Zones - Lecture seule
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 0, 1, 0, 0, 0
FROM permissions WHERE module = 'zones';

-- Reports - Créer et lire
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @manager_id, id, 1, 1, 0, 0, 0
FROM permissions WHERE module = 'reports';

-- =============================================
-- AGENT (Niveau 20-49) - OPÉRATIONNEL
-- =============================================
-- Properties - Créer, lire, modifier (ses biens)
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @agent_id, id, 1, 1, 1, 0, 0
FROM permissions WHERE module = 'properties';

-- Clients - Créer, lire, modifier (ses clients)
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @agent_id, id, 1, 1, 1, 0, 0
FROM permissions WHERE module = 'clients';

-- Transactions - Créer, lire (ses transactions)
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @agent_id, id, 1, 1, 0, 0, 0
FROM permissions WHERE module = 'transactions';

-- Commissions - Lecture seule (ses commissions)
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @agent_id, id, 0, 1, 0, 0, 0
FROM permissions WHERE module = 'commissions';

-- Agencies - Aucun accès
-- Users - Aucun accès
-- Zones - Lecture seule
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @agent_id, id, 0, 1, 0, 0, 0
FROM permissions WHERE module = 'zones';

-- Reports - Lecture seule
INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @agent_id, id, 0, 1, 0, 0, 0
FROM permissions WHERE module = 'reports';

-- =============================================
-- RÉSUMÉ DES PERMISSIONS PAR RÔLE
-- =============================================
/*
SUPER ADMIN (100):
  - TOUT : C R U D V sur tous les modules

ADMIN (80-89):
  - Properties: C R U D V
  - Clients: C R U D V
  - Transactions: C R U D V
  - Commissions: C R U D
  - Agencies: C R U D
  - Users: C R U D
  - Zones: C R U D
  - Reports: C R U

MANAGER (50-79):
  - Properties: C R U V
  - Clients: C R U V
  - Transactions: C R U V
  - Commissions: R
  - Agencies: R
  - Users: R U
  - Zones: R
  - Reports: C R

AGENT (20-49):
  - Properties: C R U
  - Clients: C R U
  - Transactions: C R
  - Commissions: R
  - Zones: R
  - Reports: R
*/

SELECT 'Permissions configurées avec succès!' as message;
