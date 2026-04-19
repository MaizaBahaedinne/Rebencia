<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCharacteristicsPermissions extends Migration
{
    private array $permissions = [
        'characteristics.view'   => 'Voir les caractéristiques',
        'characteristics.create' => 'Créer des caractéristiques',
        'characteristics.edit'   => 'Modifier des caractéristiques',
        'characteristics.delete' => 'Supprimer des caractéristiques',
    ];

    public function up(): void
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        foreach ($this->permissions as $name => $label) {
            // Éviter les doublons
            $exists = $this->db->table('permissions')
                ->where('name', $name)
                ->countAllResults();
            if ($exists === 0) {
                $this->db->table('permissions')->insert([
                    'name'        => $name,
                    'label'       => $label,
                    'module'      => 'characteristics',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        // Attribuer toutes ces permissions au(x) rôle(s) super_admin / admin
        if ($this->db->tableExists('roles') && $this->db->tableExists('role_permissions')) {
            $adminRoles = $this->db->table('roles')
                ->whereIn('name', ['super_admin', 'admin'])
                ->get()
                ->getResultArray();

            foreach ($adminRoles as $role) {
                $perms = $this->db->table('permissions')
                    ->like('name', 'characteristics.', 'after')
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
                            'created_at'    => $now,
                        ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        if (! $this->db->tableExists('permissions')) {
            return;
        }

        $perms = $this->db->table('permissions')
            ->like('name', 'characteristics.', 'after')
            ->get()
            ->getResultArray();

        foreach ($perms as $perm) {
            if ($this->db->tableExists('role_permissions')) {
                $this->db->table('role_permissions')
                    ->where('permission_id', $perm['id'])
                    ->delete();
            }
            $this->db->table('permissions')->where('id', $perm['id'])->delete();
        }
    }
}
