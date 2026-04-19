<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\Exceptions\DatabaseException;

/**
 * Migration : Gestion des zones administratives
 *
 * - Étend la table `zones` existante :
 *     • Ajout colonne `code`       – code ISO pays ou code postal
 *     • Ajout colonne `is_active`  – activation/désactivation
 *     • Ajout colonne `deleted_at` – soft delete
 *     • Extension de l'ENUM `type` : ajoute pays, ville, code_postal
 *       (« region » existe déjà)
 * - Crée les permissions zones.*
 * - Assigne les permissions aux rôles
 */
class AddZonesAdminModule extends Migration
{
    public function up(): void
    {
        // --------------------------------------------------------
        // 1. Colonnes supplémentaires sur la table zones
        // --------------------------------------------------------
        $columns = [
            "ALTER TABLE `zones` ADD COLUMN `code`      VARCHAR(20)          NULL          AFTER `name`",
            "ALTER TABLE `zones` ADD COLUMN `is_active` TINYINT(1) UNSIGNED  NOT NULL DEFAULT 1 AFTER `parent_id`",
            "ALTER TABLE `zones` ADD COLUMN `deleted_at` DATETIME            NULL          AFTER `updated_at`",
        ];

        foreach ($columns as $sql) {
            try {
                $this->db->query($sql);
            } catch (DatabaseException $e) {
                if (str_contains($e->getMessage(), 'Duplicate column name')) {
                    continue; // déjà présente, on ignore
                }
                throw $e;
            }
        }

        // --------------------------------------------------------
        // 2. Extension de l'ENUM type
        //    Valeurs conservées : governorate, city, region, district
        //    Nouvelles valeurs  : pays, ville, code_postal
        // --------------------------------------------------------
        try {
            $this->db->query("
                ALTER TABLE `zones`
                MODIFY COLUMN `type` ENUM(
                    'governorate', 'city', 'region', 'district',
                    'pays', 'ville', 'code_postal', 'quartier'
                ) NOT NULL DEFAULT 'city'
            ");
        } catch (DatabaseException $e) {
            throw $e;
        }

        // --------------------------------------------------------
        // 3. Permissions du module zones
        // --------------------------------------------------------
        $permissions = [
            ['name' => 'zones.view',   'label' => 'Voir les zones géographiques',      'module' => 'zones'],
            ['name' => 'zones.create', 'label' => 'Créer des zones géographiques',     'module' => 'zones'],
            ['name' => 'zones.edit',   'label' => 'Modifier des zones géographiques',  'module' => 'zones'],
            ['name' => 'zones.delete', 'label' => 'Supprimer des zones géographiques', 'module' => 'zones'],
        ];

        foreach ($permissions as $perm) {
            $exists = $this->db->table('permissions')
                               ->where('name', $perm['name'])
                               ->countAllResults();
            if (! $exists) {
                $this->db->table('permissions')->insert(array_merge($perm, [
                    'created_at' => date('Y-m-d H:i:s'),
                ]));
            }
        }

        // --------------------------------------------------------
        // 4. Attribution des permissions par rôle
        // --------------------------------------------------------

        // super_admin, admin, director → zones.* complet
        foreach (['super_admin', 'admin', 'director'] as $roleName) {
            $this->db->query("
                INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at)
                SELECT r.id, p.id, NOW()
                FROM permissions p
                CROSS JOIN roles r
                WHERE r.name = ? AND p.module = 'zones'
            ", [$roleName]);
        }

        // expert, coordinator → zones.view uniquement
        foreach (['expert', 'coordinator'] as $roleName) {
            $this->db->query("
                INSERT IGNORE INTO role_permissions (role_id, permission_id, created_at)
                SELECT r.id, p.id, NOW()
                FROM permissions p
                CROSS JOIN roles r
                WHERE r.name = ? AND p.name = 'zones.view'
            ", [$roleName]);
        }
    }

    public function down(): void
    {
        // Supprimer les attributions puis les permissions
        $this->db->query("
            DELETE rp FROM role_permissions rp
            INNER JOIN permissions p ON p.id = rp.permission_id
            WHERE p.module = 'zones'
        ");
        $this->db->table('permissions')->where('module', 'zones')->delete();

        // Remettre l'ENUM original
        try {
            $this->db->query("
                ALTER TABLE `zones`
                MODIFY COLUMN `type` ENUM('governorate','city','region','district')
                NOT NULL DEFAULT 'city'
            ");
        } catch (DatabaseException $e) {
            // Ignorer si des lignes utilisent encore les nouvelles valeurs
        }

        // Supprimer les colonnes ajoutées
        foreach (['code', 'is_active', 'deleted_at'] as $col) {
            try {
                $this->forge->dropColumn('zones', $col);
            } catch (DatabaseException $e) {
                // ignore
            }
        }
    }
}
