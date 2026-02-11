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
                'null' => true,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'property_type' => [
                'type' => 'ENUM',
                'constraint' => ['apartment', 'villa', 'studio', 'office', 'shop', 'warehouse', 'land', 'other'],
                'default' => 'apartment',
            ],
            'transaction_type' => [
                'type' => 'ENUM',
                'constraint' => ['sale', 'rent'],
                'default' => 'sale',
            ],
            'address' => [
                'type' => 'TEXT',
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
                'constraint' => ['new', 'excellent', 'good', 'to_renovate', 'to_demolish'],
                'null' => true,
            ],
            'has_elevator' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'has_parking' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'has_garden' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
            'agent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'in_progress', 'estimated', 'contacted', 'converted', 'cancelled'],
                'default' => 'pending',
            ],
            'notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('client_id');
        $this->forge->addKey('agent_id');
        $this->forge->addKey('status');
        $this->forge->addKey('created_at');
        
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('agent_id', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('zone_id', 'zones', 'id', 'CASCADE', 'SET NULL');
        
        $this->forge->createTable('property_estimations');
    }

    public function down()
    {
        $this->forge->dropTable('property_estimations');
    }
}
