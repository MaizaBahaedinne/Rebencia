<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgentCommissionShareToUsersTable extends Migration
{
    public function up()
    {
        // Ajouter les % de répartition agent/agence par type de transaction
        $this->forge->addColumn('users', [
            'agent_commission_share_sale' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 50.00,
                'null' => false,
                'comment' => 'Pourcentage de commission pour l\'agent sur ventes (reste = agence)'
            ],
            'agent_commission_share_rent' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 50.00,
                'null' => false,
                'comment' => 'Pourcentage de commission pour l\'agent sur locations (reste = agence)'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['agent_commission_share_sale', 'agent_commission_share_rent']);
    }
}
