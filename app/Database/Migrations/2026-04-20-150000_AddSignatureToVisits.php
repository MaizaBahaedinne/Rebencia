<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSignatureToVisits extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('client_signature', 'visits')) {
            $this->forge->addColumn('visits', [
                'client_signature' => [
                    'type'    => 'LONGTEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'notes',
                ],
            ]);
        }

        if (! $this->db->fieldExists('signed_at', 'visits')) {
            $this->forge->addColumn('visits', [
                'signed_at' => [
                    'type'    => 'DATETIME',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'client_signature',
                ],
            ]);
        }
    }

    public function down(): void
    {
        if ($this->db->fieldExists('client_signature', 'visits')) {
            $this->forge->dropColumn('visits', 'client_signature');
        }

        if ($this->db->fieldExists('signed_at', 'visits')) {
            $this->forge->dropColumn('visits', 'signed_at');
        }
    }
}
