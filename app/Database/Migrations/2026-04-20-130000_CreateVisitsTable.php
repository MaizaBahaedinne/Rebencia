<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVisitsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],

            // ── Relations ─────────────────────────────────────────────
            'client_id'   => ['type' => 'INT', 'unsigned' => true],
            'property_id' => ['type' => 'INT', 'unsigned' => true],
            'agent_id'    => ['type' => 'INT', 'unsigned' => true],

            // ── Planification ─────────────────────────────────────────
            'visit_date' => ['type' => 'DATE'],
            'visit_time' => ['type' => 'TIME'],
            'duration'   => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 60],

            // ── Statut ────────────────────────────────────────────────
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['planifiee', 'confirmee', 'effectuee', 'annulee', 'replanifiee'],
                'default'    => 'planifiee',
            ],

            // ── Notes internes ────────────────────────────────────────
            'notes' => ['type' => 'TEXT', 'null' => true],

            // ── Feedback post-visite ──────────────────────────────────
            'feedback' => [
                'type'       => 'ENUM',
                'constraint' => ['interesse', 'pas_interesse', 'negociation'],
                'null'       => true,
            ],
            'feedback_notes' => ['type' => 'TEXT', 'null' => true],

            // ── Notifications ─────────────────────────────────────────
            'whatsapp_sent' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'reminder_sent' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],

            // ── Méta ──────────────────────────────────────────────────
            'created_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['visit_date', 'agent_id']);
        $this->forge->addKey('status');
        $this->forge->addKey('client_id');
        $this->forge->addKey('property_id');

        $this->forge->createTable('visits');
    }

    public function down(): void
    {
        $this->forge->dropTable('visits', true);
    }
}
