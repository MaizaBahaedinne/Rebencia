<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateClientsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],

            // ── Section 1 : Infos de base ──────────────────────────────
            'client_type' => [
                'type'       => 'ENUM',
                'constraint' => ['acheteur', 'locataire', 'proprietaire', 'investisseur'],
                'default'    => 'acheteur',
            ],
            'first_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'last_name'   => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'       => ['type' => 'VARCHAR', 'constraint' => 30],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],

            // ── Section 2 : Infos pro ──────────────────────────────────
            'profession'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'company'     => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],

            // ── Section 3 : Adresse ────────────────────────────────────
            'address'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'zone_pays_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'zone_region_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'zone_ville_id'  => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'postal_code' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],

            // ── Section 4 : Besoin immobilier ──────────────────────────
            // Commun
            'property_type_id' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            // Acheteur / Locataire
            'budget_min'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'budget_max'       => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],
            'desired_zone'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // Propriétaire
            'owner_location'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'desired_price'    => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => true],

            // ── Section 5 : CRM ────────────────────────────────────────
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['nouveau', 'contacte', 'actif', 'en_attente', 'converti', 'inactif', 'perdu'],
                'default'    => 'nouveau',
            ],
            'assigned_to' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'source'      => [
                'type'       => 'ENUM',
                'constraint' => ['site_web', 'facebook', 'instagram', 'appel', 'email', 'agence', 'referral', 'autre'],
                'default'    => 'site_web',
            ],

            // ── Section 6 : Notes ──────────────────────────────────────
            'notes' => ['type' => 'TEXT', 'null' => true],

            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('client_type');
        $this->forge->addKey('status');
        $this->forge->addKey('assigned_to');
        $this->forge->createTable('clients', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('clients', true);
    }
}
