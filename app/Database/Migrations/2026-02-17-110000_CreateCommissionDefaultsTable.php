<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommissionDefaultsTable extends Migration
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
            'agent_commission_share_sale' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 50.00,
                'comment' => 'Taux par défaut du système: % commission pour agent sur ventes'
            ],
            'agent_commission_share_rent' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 50.00,
                'comment' => 'Taux par défaut du système: % commission pour agent sur locations'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('commission_defaults');
        
        // Insérer une ligne par défaut
        $this->db->table('commission_defaults')->insert([
            'agent_commission_share_sale' => 50.00,
            'agent_commission_share_rent' => 50.00,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('commission_defaults');
    }
}
