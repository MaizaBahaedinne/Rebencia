<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSearchAlertsTable extends Migration
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
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'JSON array of property types',
            ],
            'transaction_type' => [
                'type' => 'ENUM',
                'constraint' => ['sale', 'rent', 'both'],
                'default' => 'sale',
            ],
            'price_min' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'price_max' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
            ],
            'area_min' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'area_max' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
            ],
            'rooms_min' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'bedrooms_min' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'bathrooms_min' => [
                'type' => 'INT',
                'constraint' => 3,
                'null' => true,
            ],
            'zones' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of zone IDs',
            ],
            'cities' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of cities',
            ],
            'governorates' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of governorates',
            ],
            'has_elevator' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'has_parking' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'has_garden' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'has_pool' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'frequency' => [
                'type' => 'ENUM',
                'constraint' => ['instant', 'daily', 'weekly'],
                'default' => 'daily',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'last_sent_at' => [
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
        $this->forge->addKey('client_id');
        $this->forge->addKey('email');
        $this->forge->addKey('is_active');
        $this->forge->addKey('created_at');
        
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('search_alerts');
    }

    public function down()
    {
        $this->forge->dropTable('search_alerts');
    }
}
