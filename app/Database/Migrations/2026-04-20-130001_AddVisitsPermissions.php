<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVisitsPermissions extends Migration
{
    private array $permissions = [
        'visits.view'   => 'Voir les visites',
        'visits.create' => 'Planifier des visites',
        'visits.edit'   => 'Modifier des visites',
        'visits.delete' => 'Supprimer des visites',
    ];

    public function up(): void
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        foreach ($this->permissions as $name => $label) {
            $exists = $this->db->table('permissions')
                ->where('name', $name)
                ->countAllResults();
            if ($exists === 0) {
                $this->db->table('permissions')->insert([
                    'name'       => $name,
                    'label'      => $label,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Assigner aux rôles super_admin et admin
        $adminRoles = $this->db->table('roles')
            ->whereIn('name', ['super_admin', 'admin'])
            ->get()->getResultArray();

        foreach ($adminRoles as $role) {
            $permRows = $this->db->table('permissions')
                ->whereIn('name', array_keys($this->permissions))
                ->get()->getResultArray();

            foreach ($permRows as $perm) {
                $exists = $this->db->table('role_permissions')
                    ->where('role_id', $role['id'])
                    ->where('permission_id', $perm['id'])
                    ->countAllResults();
                if ($exists === 0) {
                    $this->db->table('role_permissions')->insert([
                        'role_id'       => $role['id'],
                        'permission_id' => $perm['id'],
                        'created_at'    => $now,
                        'updated_at'    => $now,
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        $permNames = array_keys($this->permissions);

        $perms = $this->db->table('permissions')
            ->whereIn('name', $permNames)
            ->get()->getResultArray();

        foreach ($perms as $p) {
            $this->db->table('role_permissions')
                ->where('permission_id', $p['id'])->delete();
        }

        $this->db->table('permissions')
            ->whereIn('name', $permNames)->delete();
    }
}
