<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomSaleCommissionRateToProperties extends Migration
{
    public function up()
    {
        // Ajouter un taux de commission personnalisé pour les ventes au niveau du bien
        $this->forge->addColumn('properties', [
            'custom_sale_commission_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'default' => null,
                'comment' => 'Taux de commission personnalisé pour la vente de ce bien (NULL = utiliser les règles standard)'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('properties', 'custom_sale_commission_rate');
    }
}
