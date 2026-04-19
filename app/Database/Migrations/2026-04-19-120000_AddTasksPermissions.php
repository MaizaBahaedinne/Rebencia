<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTasksPermissions extends Migration
{
    public function up(): void
    {
        $permissions = [
            ['name' => 'tasks.view',   'label' => 'Voir les tâches',      'module' => 'tasks'],
            ['name' => 'tasks.create', 'label' => 'Créer des tâches',     'module' => 'tasks'],
            ['name' => 'tasks.edit',   'label' => 'Modifier des tâches',  'module' => 'tasks'],
            ['name' => 'tasks.delete', 'label' => 'Supprimer des tâches', 'module' => 'tasks'],
        ];

        foreach ($permissions as $perm) {
            $exists = $this->db->table('permissions')->where('name', $perm['name'])->countAllResults();
            if (! $exists) {
                $this->db->table('permissions')->insert(array_merge($perm, [
                    'created_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }

        // Donner tasks.* à super_admin, admin, director
        $fullRoles = ['super_admin', 'admin', 'director'];
        foreach ($fullRoles as $roleName) {
            $this->db->query("
                INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at)
                SELECT r.id, p.id, NOW()
                FROM permissions p
                CROSS JOIN roles r
                WHERE r.name = ? AND p.module = 'tasks'
            ", [$roleName]);
        }

        // tasks.view + tasks.create + tasks.edit à expert et coordinator
        $partialRoles = ['expert', 'coordinator'];
        foreach ($partialRoles as $roleName) {
            $this->db->query("
                INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at)
                SELECT r.id, p.id, NOW()
                FROM permissions p
                CROSS JOIN roles r
                WHERE r.name = ? AND p.name IN ('tasks.view','tasks.create','tasks.edit')
            ", [$roleName]);
        }

        // tasks.view à collaborator
        $this->db->query("
            INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at)
            SELECT r.id, p.id, NOW()
            FROM permissions p
            CROSS JOIN roles r
            WHERE r.name = 'collaborator' AND p.name = 'tasks.view'
        ");
    }

    public function down(): void
    {
        $this->db->query("
            DELETE rp FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE p.module = 'tasks'
        ");
        $this->db->table('permissions')->where('module', 'tasks')->delete();
    }
}
