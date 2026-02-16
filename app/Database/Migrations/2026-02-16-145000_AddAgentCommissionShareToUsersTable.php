<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgentCommissionShareToUsersTable extends Migration
{
    public function up()
    {
        // Ajouter le % de répartition agent/agence
        $this->forge->addColumn('users', [
            'agent_commission_share' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 50.00,
                'null' => false,
                'comment' => 'Pourcentage de commission pour l\'agent (reste = agence)'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'agent_commission_share');
    }
}
