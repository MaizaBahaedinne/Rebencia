<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNotaryToTransactions extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('transactions')) {
            return;
        }

        $fields = [
            'notary' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'contract_number',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'notary');
    }
}
