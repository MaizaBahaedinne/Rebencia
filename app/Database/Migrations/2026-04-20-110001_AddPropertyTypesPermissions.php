<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPropertyTypesPermissions extends Migration
{
    private array $permissions = [
        'property_types.view'   => 'Voir les types de bien',
        'property_types.create' => 'Créer des types de bien',
        'property_types.edit'   => 'Modifier des types de bien',
        'property_types.delete' => 'Supprimer des types de bien',
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
                    'module'     => 'property_types',
                    'created_at' => $now,
                ]);
            }
        }

        // Attribuer au super_admin et admin
        if ($this->db->tableExists('roles') && $this->db->tableExists('role_permissions')) {
            $adminRoles = $this->db->table('roles')
                ->whereIn('name', ['super_admin', 'admin'])
                ->get()
                ->getResultArray();

            foreach ($adminRoles as $role) {
                $perms = $this->db->table('permissions')
                    ->like('name', 'property_types.', 'after')
                    ->get()
                    ->getResultArray();

                foreach ($perms as $perm) {
                    $alreadyLinked = $this->db->table('role_permissions')
                        ->where('role_id', $role['id'])
                        ->where('permission_id', $perm['id'])
                        ->countAllResults();
                    if ($alreadyLinked === 0) {
                        $this->db->table('role_permissions')->insert([
                            'role_id'       => $role['id'],
                            'permission_id' => $perm['id'],
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        $this->db->table('permissions')
            ->like('name', 'property_types.', 'after')
            ->delete();
    }
}
