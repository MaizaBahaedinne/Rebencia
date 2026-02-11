<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePricePerM2Table extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'zone_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'governorate' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'property_type' => [
                'type' => 'ENUM',
                'constraint' => ['apartment', 'villa', 'studio', 'office', 'shop', 'warehouse', 'land'],
                'default' => 'apartment',
            ],
            'transaction_type' => [
                'type' => 'ENUM',
                'constraint' => ['sale', 'rent'],
                'default' => 'sale',
            ],
            'price_min' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'price_max' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => true,
            ],
            'price_average' => [
                'type' => 'DECIMAL',
                'constraint' => '12,2',
                'null' => false,
            ],
            'surface_average' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'properties_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'evolution' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Évolution en pourcentage',
            ],
            'period' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'Format: 2026-Q1, 2026-01, etc.',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('zone_id');
        $this->forge->addKey(['city', 'governorate']);
        $this->forge->addKey('property_type');
        $this->forge->addKey('transaction_type');
        
        $this->forge->addForeignKey('zone_id', 'zones', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('price_per_m2');
    }

    public function down()
    {
        $this->forge->dropTable('price_per_m2');
    }
}
