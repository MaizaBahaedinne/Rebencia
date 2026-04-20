<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExtendClientsForDemandProfile extends Migration
{
    public function up(): void
    {
        // ── 1. Colonnes supplémentaires dans clients ──────────────────────────

        $add = [];

        if (! $this->db->fieldExists('demand_type', 'clients')) {
            $add['demand_type'] = "ALTER TABLE clients ADD COLUMN demand_type ENUM('achat','location') NULL AFTER client_type";
        }
        if (! $this->db->fieldExists('orientations', 'clients')) {
            $add['orientations'] = "ALTER TABLE clients ADD COLUMN orientations JSON NULL AFTER desired_zone";
        }
        if (! $this->db->fieldExists('surface_min', 'clients')) {
            $add['surface_min'] = "ALTER TABLE clients ADD COLUMN surface_min DECIMAL(8,2) NULL AFTER orientations";
        }
        if (! $this->db->fieldExists('surface_max', 'clients')) {
            $add['surface_max'] = "ALTER TABLE clients ADD COLUMN surface_max DECIMAL(8,2) NULL AFTER surface_min";
        }
        if (! $this->db->fieldExists('rooms_min', 'clients')) {
            $add['rooms_min'] = "ALTER TABLE clients ADD COLUMN rooms_min TINYINT UNSIGNED NULL AFTER surface_max";
        }
        if (! $this->db->fieldExists('bedrooms_min', 'clients')) {
            $add['bedrooms_min'] = "ALTER TABLE clients ADD COLUMN bedrooms_min TINYINT UNSIGNED NULL AFTER rooms_min";
        }
        if (! $this->db->fieldExists('floor_preferred', 'clients')) {
            $add['floor_preferred'] = "ALTER TABLE clients ADD COLUMN floor_preferred VARCHAR(50) NULL AFTER bedrooms_min";
        }
        if (! $this->db->fieldExists('has_elevator', 'clients')) {
            $add['has_elevator'] = "ALTER TABLE clients ADD COLUMN has_elevator TINYINT(1) NULL AFTER floor_preferred";
        }
        if (! $this->db->fieldExists('urgency', 'clients')) {
            $add['urgency'] = "ALTER TABLE clients ADD COLUMN urgency ENUM('faible','moyenne','elevee') NULL DEFAULT 'moyenne' AFTER has_elevator";
        }
        if (! $this->db->fieldExists('budget_flexibility', 'clients')) {
            $add['budget_flexibility'] = "ALTER TABLE clients ADD COLUMN budget_flexibility ENUM('strict','flexible','tres_flexible') NULL DEFAULT 'flexible' AFTER urgency";
        }

        foreach ($add as $sql) {
            $this->db->query($sql);
        }

        // ── 2. Table pivot : types de bien ────────────────────────────────────

        if (! $this->db->tableExists('client_property_types')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'        => ['type' => 'INT', 'unsigned' => true],
                'property_type_id' => ['type' => 'INT', 'unsigned' => true],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['client_id', 'property_type_id'], 'uq_cpt');
            $this->forge->createTable('client_property_types');
        }

        // ── 3. Table pivot : zones de recherche ───────────────────────────────

        if (! $this->db->tableExists('client_search_zones')) {
            $this->forge->addField([
                'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'  => ['type' => 'INT', 'unsigned' => true],
                'zone_id'    => ['type' => 'INT', 'unsigned' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['client_id', 'zone_id'], 'uq_csz');
            $this->forge->createTable('client_search_zones');
        }

        // ── 4. Table pivot : caractéristiques souhaitées ──────────────────────

        if (! $this->db->tableExists('client_features')) {
            $this->forge->addField([
                'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
                'client_id'        => ['type' => 'INT', 'unsigned' => true],
                'feature_key'      => ['type' => 'VARCHAR', 'constraint' => 100],
                'requirement_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['obligatoire', 'optionnel'],
                    'default'    => 'optionnel',
                ],
                'weight'           => ['type' => 'TINYINT', 'unsigned' => true, 'default' => 1],
                'created_at'       => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['client_id', 'feature_key'], 'uq_cf');
            $this->forge->createTable('client_features');
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('client_features', true);
        $this->forge->dropTable('client_search_zones', true);
        $this->forge->dropTable('client_property_types', true);

        $dropCols = [
            'demand_type', 'orientations', 'surface_min', 'surface_max',
            'rooms_min', 'bedrooms_min', 'floor_preferred', 'has_elevator',
            'urgency', 'budget_flexibility',
        ];
        foreach ($dropCols as $col) {
            if ($this->db->fieldExists($col, 'clients')) {
                $this->db->query("ALTER TABLE clients DROP COLUMN {$col}");
            }
        }
    }
}
