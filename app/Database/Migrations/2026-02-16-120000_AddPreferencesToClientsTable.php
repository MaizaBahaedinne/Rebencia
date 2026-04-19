<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPreferencesToClientsTable extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('clients')) {
            return;
        }

        // Ajouter les colonnes de préférences manquantes à la table clients
        $this->forge->addColumn('clients', [
            'property_type_preference' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type de bien préféré (apartment, villa, land, commercial, etc.)'
            ],
            'transaction_type_preference' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Type de transaction préféré (sale, rent)'
            ],
            'budget_min' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'Budget minimum'
            ],
            'budget_max' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'Budget maximum'
            ],
            'preferred_zones' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Zones préférées (JSON array d\'IDs)'
            ],
            'area_preference' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Préférence de surface'
            ]
        ]);
    }

    public function down()
    {
        // Supprimer les colonnes
        $this->forge->dropColumn('clients', [
            'property_type_preference',
            'transaction_type_preference',
            'budget_min',
            'budget_max',
            'preferred_zones',
            'area_preference'
        ]);
    }
}
