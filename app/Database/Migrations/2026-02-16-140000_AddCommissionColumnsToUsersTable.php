<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCommissionColumnsToUsersTable extends Migration
{
    public function up()
    {
        // Ajouter les colonnes de commission personnalisées à la table users
        $this->forge->addColumn('users', [
            'commission_sale_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 10.00,
                'null' => false,
                'comment' => 'Taux de commission pour les ventes (%)'
            ],
            'commission_rent_percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 50.00,
                'null' => false,
                'comment' => 'Taux de commission pour les locations (%)'
            ],
            'is_commission_exceptional' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'comment' => 'Profil exceptionnel (taux personnalisés spéciaux)'
            ],
            'commission_exceptional_note' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Note sur le statut exceptionnel'
            ]
        ]);
    }

    public function down()
    {
        // Supprimer les colonnes
        $this->forge->dropColumn('users', [
            'commission_sale_percentage',
            'commission_rent_percentage',
            'is_commission_exceptional',
            'commission_exceptional_note'
        ]);
    }
}
