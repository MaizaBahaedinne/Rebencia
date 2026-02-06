<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePropertyEstimatedCostsTable extends Migration
{
    public function up()
    {
        // Table pour les coûts estimés et charges mensuelles
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
            'syndic_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'frais de syndic mensuel'
            ],
            'electricity_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'électricité mensuelle'
            ],
            'water_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'eau mensuelle'
            ],
            'gas_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'gaz mensuel'
            ],
            'heating_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'chauffage mensuel'
            ],
            'property_tax_annual' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'taxe foncière annuelle'
            ],
            'income_tax_annual' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'impôt sur revenus annuel'
            ],
            'insurance_annual' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'assurance annuelle'
            ],
            'maintenance_annual' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'maintenance/réparations annuelles'
            ],
            'hoa_fees_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'frais HOA/association mensuels'
            ],
            'other_costs_monthly' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'autres coûts mensuels'
            ],
            'total_monthly_costs' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'total coûts mensuels (calculé)'
            ],
            'total_annual_costs' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'total coûts annuels (calculé)'
            ],
            'cost_notes' => [
                'type' => 'TEXT',
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
        $this->forge->createTable('property_estimated_costs', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_estimated_costs', true);
    }
}
