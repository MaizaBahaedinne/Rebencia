<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContractNumberToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'contract_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'transaction_date',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'contract_number');
    }
}
