<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateObjectivesTable extends Migration
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
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['personal', 'agency'],
                'null' => false,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Pour objectifs personnels'
            ],
            'agency_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Pour objectifs agence'
            ],
            'period' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'null' => false,
                'comment' => 'Format: YYYY-MM'
            ],
            'revenue_target' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'default' => 0,
                'comment' => 'Chiffre d\'affaires cible'
            ],
            'new_contacts_target' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
                'comment' => 'Nombre de nouveaux contacts'
            ],
            'properties_rent_target' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
                'comment' => 'Nombre de biens location'
            ],
            'properties_sale_target' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
                'comment' => 'Nombre de biens vente'
            ],
            'transactions_target' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
                'comment' => 'Nombre de transactions'
            ],
            'revenue_achieved' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'default' => 0,
            ],
            'new_contacts_achieved' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'properties_rent_achieved' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'properties_sale_achieved' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'transactions_achieved' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'completed', 'cancelled'],
                'default' => 'active',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey(['type', 'period']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('agency_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('agency_id', 'agencies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('objectives');
    }

    public function down()
    {
        $this->forge->dropTable('objectives');
    }
}
