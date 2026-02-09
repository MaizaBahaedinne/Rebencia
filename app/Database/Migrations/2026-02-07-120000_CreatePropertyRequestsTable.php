<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyRequestsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'request_type' => [
                'type'       => 'ENUM',
                'constraint' => ['visit', 'information'],
                'default'    => 'information',
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'visit_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'visit_time' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'contacted', 'scheduled', 'completed', 'cancelled'],
                'default'    => 'pending',
            ],
            'source' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'website',
            ],
            'assigned_to' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
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
        $this->forge->addKey('property_id');
        $this->forge->addKey('client_id');
        $this->forge->addKey('status');
        $this->forge->addKey('assigned_to');
        
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'SET NULL', 'CASCADE');
        
        $this->forge->createTable('property_requests');
    }

    public function down()
    {
        $this->forge->dropTable('property_requests');
    }
}
