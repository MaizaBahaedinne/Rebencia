<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSuperAdminAndAdminRoles extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------------
        // 1. Insérer les deux nouveaux rôles (idempotent)
        // --------------------------------------------------------
        $this->db->query("
            INSERT IGNORE INTO `roles` (`name`, `label`, `description`, `color`, `is_active`, `created_at`, `updated_at`) VALUES
            ('super_admin', 'Super Administrateur', 'Accès absolu au système, gestion technique',       '#6610f2', 1, NOW(), NOW()),
            ('admin',       'Administrateur',       'Gestion complète métier hors paramètres système',  '#20c997', 1, NOW(), NOW())
        ");

        // --------------------------------------------------------
        // 2. Super Administrateur — toutes les permissions
        // --------------------------------------------------------
        $this->db->query("
            INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
            SELECT r.id, p.id, NOW()
            FROM `permissions` p
            CROSS JOIN `roles` r
            WHERE r.name = 'super_admin'
        ");

        // --------------------------------------------------------
        // 3. Administrateur — toutes les permissions sauf deploy/settings
        // --------------------------------------------------------
        $this->db->query("
            INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
            SELECT r.id, p.id, NOW()
            FROM `permissions` p
            CROSS JOIN `roles` r
            WHERE r.name = 'admin'
              AND p.name NOT IN ('system.deploy', 'system.settings')
        ");
    }

    public function down(): void
    {
        // Supprimer les permissions des deux rôles
        $this->db->query("
            DELETE rp FROM `role_permissions` rp
            INNER JOIN `roles` r ON r.id = rp.role_id
            WHERE r.name IN ('super_admin', 'admin')
        ");

        // Supprimer les rôles (seulement s'il n'y a pas d'utilisateurs assignés)
        $this->db->query("
            DELETE FROM `roles` WHERE `name` IN ('super_admin', 'admin')
        ");
    }
}
