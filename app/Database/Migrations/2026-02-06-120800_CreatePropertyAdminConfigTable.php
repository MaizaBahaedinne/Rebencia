<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePropertyAdminConfigTable extends Migration
{
    public function up()
    {
        // Table pour la configuration admin des fonctionnalités par type de propriété
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'property_type' => [
                'type' => 'ENUM',
                'constraint' => ['apartment', 'villa', 'house', 'land', 'office', 'commercial', 'warehouse', 'other'],
                'null' => false,
            ],
            'enable_rooms' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher tab dimensions pièces'
            ],
            'enable_location_scoring' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher scores localisation'
            ],
            'enable_financial_data' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher données financières'
            ],
            'enable_estimated_costs' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher coûts estimés'
            ],
            'enable_orientation' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher orientation/exposition'
            ],
            'enable_media_extension' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher médias étendus'
            ],
            'enable_options' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher options/équipements'
            ],
            'required_rooms' => [
                'type' => 'TINYINT',
                'default' => 0,
                'comment' => 'données pièces obligatoires'
            ],
            'required_location_scoring' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'required_financial_data' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'required_estimated_costs' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'required_orientation' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'allowed_option_categories' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'JSON array de catégories autorisées'
            ],
            'max_rooms_allowed' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'nombre max pièces à entrer'
            ],
            'default_valuation_method' => [
                'type' => 'ENUM',
                'constraint' => ['comparable_sales', 'income_approach', 'cost_approach', 'appraisal', 'automated_valuation'],
                'null' => true,
            ],
            'show_roi_metrics' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
            'show_on_listings' => [
                'type' => 'TINYINT',
                'default' => 1,
                'comment' => 'afficher sur annonces publiques'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('property_type', false, true);
        $this->forge->createTable('property_admin_config', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_admin_config', true);
    }
}
