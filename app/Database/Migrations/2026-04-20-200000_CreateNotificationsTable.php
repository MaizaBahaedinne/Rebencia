<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificationsTable extends Migration
{
    public function up(): void
    {
        // ── Table notifications (in-app) ─────────────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'info'],
            // Valeurs : info | success | warning | lead | property | task | system
            'title'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'message'    => ['type' => 'TEXT'],
            'url'        => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => true],
            // Lien optionnel : /admin/leads/123
            'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'read_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey(['user_id', 'is_read']);
        $this->forge->createTable('notifications', true);

        // ── Table push_subscriptions (Web Push) ──────────────────────
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'endpoint'   => ['type' => 'TEXT'],
            'public_key' => ['type' => 'VARCHAR', 'constraint' => 255],
            // clé p256dh de la subscription
            'auth_token' => ['type' => 'VARCHAR', 'constraint' => 100],
            'user_agent' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'last_used_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('push_subscriptions', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('push_subscriptions', true);
        $this->forge->dropTable('notifications', true);
    }
}
