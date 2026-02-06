<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePropertyFinancialDataTable extends Migration
{
    public function up()
    {
        // Table pour les données financières et d'investissement
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'property_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'estimated_market_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'estimation prix marché'
            ],
            'estimated_rental_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'estimation loyer mensuel'
            ],
            'gross_yield' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'rendement brut en %'
            ],
            'net_yield' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'rendement net en %'
            ],
            'price_per_sqm' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'prix par m² estimé'
            ],
            'cap_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'cap rate (NOI / valeur) en %'
            ],
            'cash_on_cash_return' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'retour sur capital en %'
            ],
            'roi_annual' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'ROI annuel en %'
            ],
            'payback_period_years' => [
                'type' => 'DECIMAL',
                'constraint' => '5,1',
                'null' => true,
                'comment' => 'période d\'amortissement en années'
            ],
            'appreciation_rate' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'taux appréciation historique %'
            ],
            'annual_appreciation_value' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'valeur appréciation annuelle'
            ],
            'debt_service_ratio' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'ratio de couverture de la dette'
            ],
            'investor_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'notes pour investisseur'
            ],
            'last_valuation_date' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'valuation_method' => [
                'type' => 'ENUM',
                'constraint' => ['comparable_sales', 'income_approach', 'cost_approach', 'appraisal', 'automated_valuation'],
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
                'on_update' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('property_id', false, true);
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_financial_data', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_financial_data', true);
    }
}
