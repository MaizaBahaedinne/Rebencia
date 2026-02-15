<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionPaidToTransactions extends Migration
{
    public function up()
    {
        $fields = [
            'commission_paid' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'after' => 'commission_amount',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'commission_paid');
    }
}
