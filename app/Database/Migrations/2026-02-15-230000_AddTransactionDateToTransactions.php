<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTransactionDateToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'transaction_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'type',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'transaction_date');
    }
}
