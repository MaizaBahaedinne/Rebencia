<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        // --------------------------------------------------------
        // 1. Rôles
        // --------------------------------------------------------
        $roles = [
            ['name' => 'super_admin', 'label' => 'Super Administrateur', 'description' => 'Accès absolu au système, gestion technique',      'color' => '#6610f2'],
            ['name' => 'admin',       'label' => 'Administrateur',       'description' => 'Gestion complète métier hors paramètres système', 'color' => '#20c997'],
            ['name' => 'director',    'label' => 'Directeur d\'Agence',   'description' => 'Accès total à la plateforme, gestion stratégique','color' => '#dc3545'],
            ['name' => 'expert',      'label' => 'Expert Immobilier',     'description' => 'Gestion des biens et suivi des ventes',           'color' => '#0d6efd'],
            ['name' => 'coordinator', 'label' => 'Coordinateur',          'description' => 'Gestion des leads, équipe et planning',           'color' => '#198754'],
            ['name' => 'collaborator','label' => 'Collaborateur',         'description' => 'Accès aux tâches et biens assignés',              'color' => '#fd7e14'],
        ];

        foreach ($roles as $role) {
            $exists = $this->db->table('roles')->where('name', $role['name'])->countAllResults();
            if (! $exists) {
                $this->db->table('roles')->insert(array_merge($role, [
                    'is_active'  => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]));
                echo "  + Rôle créé : {$role['label']}\n";
            } else {
                echo "  ~ Rôle déjà présent : {$role['label']}\n";
            }
        }

        // --------------------------------------------------------
        // 2. Permissions par rôle
        // --------------------------------------------------------
        $permMap = [
            'super_admin'  => null, // toutes
            'admin'        => null, // toutes sauf system.deploy et system.settings
            'director'     => null, // toutes
            'expert'       => ['properties.view','properties.create','properties.edit','properties.publish',
                               'leads.view','leads.edit','stats.view'],
            'coordinator'  => ['leads.view','leads.create','leads.edit','leads.assign',
                               'properties.view','users.view','stats.view'],
            'collaborator' => ['properties.view','leads.view','leads.edit'],
        ];

        $excludeAdmin = ['system.deploy', 'system.settings'];

        foreach ($permMap as $roleName => $allowedPerms) {
            $role = $this->db->table('roles')->where('name', $roleName)->get()->getRowArray();
            if (! $role) continue;

            // Vider les permissions existantes
            $this->db->table('role_permissions')->where('role_id', $role['id'])->delete();

            // Récupérer les permissions à assigner
            $query = $this->db->table('permissions');
            if ($allowedPerms !== null) {
                $query->whereIn('name', $allowedPerms);
            } elseif ($roleName === 'admin') {
                $query->whereNotIn('name', $excludeAdmin);
            }
            $perms = $query->get()->getResultArray();

            $inserts = [];
            foreach ($perms as $perm) {
                $inserts[] = [
                    'role_id'       => $role['id'],
                    'permission_id' => $perm['id'],
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
            }
            if ($inserts) {
                $this->db->table('role_permissions')->insertBatch($inserts);
            }

            echo "  + Permissions assignées au rôle '{$roleName}' : " . count($inserts) . "\n";
        }
    }
}
