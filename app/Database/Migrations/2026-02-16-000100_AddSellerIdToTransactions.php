<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSellerIdToTransactions extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('transactions')) {
            return;
        }

        $fields = [
            'seller_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'client_id',
            ],
        ];

        $this->forge->addColumn('transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('transactions', 'seller_id');
    }
}
