<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyEstimationsTable extends Migration
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
            'client_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'property_type' => [
                'type' => 'ENUM',
                'constraint' => ['apartment', 'villa', 'house', 'land', 'commercial', 'industrial', 'office'],
                'null' => false,
            ],
            'transaction_type' => [
                'type' => 'ENUM',
                'constraint' => ['sale', 'rent'],
                'null' => false,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'city' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'governorate' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'zone_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'area_total' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'rooms' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'bedrooms' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'bathrooms' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'floor' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'construction_year' => [
                'type' => 'INT',
                'constraint' => 4,
                'null' => true,
            ],
            'condition_state' => [
                'type' => 'ENUM',
                'constraint' => ['excellent', 'good', 'average', 'needs_renovation'],
                'null' => true,
            ],
            'has_elevator' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'has_parking' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'has_garden' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'estimated_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'completed', 'cancelled'],
                'default' => 'pending',
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'response' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'responded_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('zone_id', 'zones', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('property_estimations');
    }

    public function down()
    {
        $this->forge->dropTable('property_estimations');
    }
}
