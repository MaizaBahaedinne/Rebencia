-- =============================================
-- AJOUTER PERMISSIONS POUR MODULE ROLES
-- =============================================
-- Ce module doit être réservé uniquement aux super admins (niveau 100)

USE `rebe_RebenciaDB`;

-- 1. Créer les permissions pour le module roles
INSERT INTO `permissions` (`name`, `display_name`, `description`, `module`, `created_at`) VALUES
('roles_view', 'Voir Rôles', 'Voir les rôles et permissions', 'roles', NOW()),
('roles_create', 'Créer Rôles', 'Créer des rôles', 'roles', NOW()),
('roles_update', 'Modifier Rôles', 'Modifier des rôles', 'roles', NOW()),
('roles_delete', 'Supprimer Rôles', 'Supprimer des rôles', 'roles', NOW()),
('roles_matrix', 'Matrice Permissions', 'Gérer la matrice des permissions', 'roles', NOW());

-- 2. Assigner UNIQUEMENT au Super Admin (niveau 100)
SET @super_admin_id = (SELECT id FROM roles WHERE level = 100 LIMIT 1);

INSERT INTO `role_permissions` (role_id, permission_id, can_create, can_read, can_update, can_delete, can_validate)
SELECT @super_admin_id, id, 1, 1, 1, 1, 1
FROM permissions WHERE module = 'roles';

-- Vérification
SELECT 
    r.display_name AS 'Rôle',
    r.level AS 'Niveau',
    p.display_name AS 'Permission',
    rp.can_create AS 'C',
    rp.can_read AS 'R',
    rp.can_update AS 'U',
    rp.can_delete AS 'D'
FROM role_permissions rp
JOIN roles r ON rp.role_id = r.id
JOIN permissions p ON rp.permission_id = p.id
WHERE p.module = 'roles'
ORDER BY r.level DESC, p.name;
