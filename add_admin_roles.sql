-- ============================================================
-- Rebencia — Ajout des rôles super_admin et admin
-- Migration pour BDD existante en production
-- ============================================================

-- Insérer les deux nouveaux rôles (si inexistants)
INSERT IGNORE INTO `roles` (`name`, `label`, `description`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
('super_admin', 'Super Administrateur', 'Accès absolu au système, gestion technique',        '#6610f2', 1, NOW(), NOW()),
('admin',       'Administrateur',       'Gestion complète métier hors paramètres système',   '#20c997', 1, NOW(), NOW());

-- Super Administrateur : toutes les permissions
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name = 'super_admin';

-- Administrateur : toutes les permissions sauf system.deploy et system.settings
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name = 'admin'
  AND p.name NOT IN ('system.deploy', 'system.settings');

-- ============================================================
-- Vérification
-- ============================================================
SELECT r.name, r.label, COUNT(rp.permission_id) AS nb_permissions
FROM roles r
LEFT JOIN role_permissions rp ON rp.role_id = r.id
GROUP BY r.id, r.name, r.label
ORDER BY r.id;
