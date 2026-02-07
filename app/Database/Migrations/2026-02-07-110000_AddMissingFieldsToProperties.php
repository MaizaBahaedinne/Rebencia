<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour ajouter les champs manquants à la table properties
 * Date: 2026-02-07
 * 
 * Champs ajoutés :
 * - disponibilite_date : date de disponibilité du bien
 * - neighborhood : quartier
 * - orientation : orientation du bien (N, S, E, O, NE, NO, SE, SO)
 * - floor_type : type de sol
 * - gas_type : type de gaz
 * - energy_class : classe énergétique (A-G)
 * - energy_consumption_kwh : consommation énergétique
 * - co2_emission : émission CO2
 * - promo_price : prix promotionnel
 * - promo_start_date : date début promo
 * - promo_end_date : date fin promo
 * - charge_syndic : charges syndic
 * - charge_water : charges eau
 * - charge_gas : charges gaz
 * - charge_electricity : charges électricité
 * - charge_other : autres charges
 * - internal_notes : notes internes
 * - created_by : utilisateur créateur
 */
class AddMissingFieldsToProperties extends Migration
{
    public function up()
    {
        $fields = [
            // Date de disponibilité
            'disponibilite_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date de disponibilité du bien'
            ],
            
            // Localisation détaillée
            'neighborhood' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Quartier'
            ],
            
            // Orientation du bien
            'orientation' => [
                'type' => 'ENUM',
                'constraint' => ['N', 'S', 'E', 'O', 'NE', 'NO', 'SE', 'SO'],
                'null' => true,
                'comment' => 'Orientation du bien'
            ],
            
            // Type de sol
            'floor_type' => [
                'type' => 'ENUM',
                'constraint' => ['carrelage', 'marbre', 'parquet', 'beton_cire', 'moquette', 'mixte'],
                'null' => true,
                'default' => 'carrelage',
                'comment' => 'Type de sol'
            ],
            
            // Type de gaz
            'gas_type' => [
                'type' => 'ENUM',
                'constraint' => ['ville', 'bouteille', 'propane', 'aucun'],
                'null' => true,
                'default' => 'aucun',
                'comment' => 'Type de gaz'
            ],
            
            // Classe énergétique
            'energy_class' => [
                'type' => 'ENUM',
                'constraint' => ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
                'null' => true,
                'comment' => 'Classe énergétique'
            ],
            
            // Consommation énergétique
            'energy_consumption_kwh' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Consommation énergétique (kWh/m²/an)'
            ],
            
            // Émission CO2
            'co2_emission' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'Émission CO2 (kg/m²/an)'
            ],
            
            // Prix promotionnel
            'promo_price' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'null' => true,
                'comment' => 'Prix promotionnel'
            ],
            
            'promo_start_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date début promotion'
            ],
            
            'promo_end_date' => [
                'type' => 'DATE',
                'null' => true,
                'comment' => 'Date fin promotion'
            ],
            
            // Charges mensuelles
            'charge_syndic' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => '0.00',
                'comment' => 'Charges syndic (TND/mois)'
            ],
            
            'charge_water' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => '0.00',
                'comment' => 'Charges eau (TND/mois)'
            ],
            
            'charge_gas' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => '0.00',
                'comment' => 'Charges gaz (TND/mois)'
            ],
            
            'charge_electricity' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => '0.00',
                'comment' => 'Charges électricité (TND/mois)'
            ],
            
            'charge_other' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'default' => '0.00',
                'comment' => 'Autres charges (TND/mois)'
            ],
            
            // Notes internes
            'internal_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Notes internes (non visibles publiquement)'
            ],
            
            // Créé par
            'created_by' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de l\'utilisateur créateur'
            ]
        ];
        
        $this->forge->addColumn('properties', $fields);
        
        // Ajouter l'index foreign key pour created_by
        $this->forge->addKey('created_by', false, false, 'idx_properties_created_by');
    }

    public function down()
    {
        $fields = [
            'disponibilite_date',
            'neighborhood',
            'orientation',
            'floor_type',
            'gas_type',
            'energy_class',
            'energy_consumption_kwh',
            'co2_emission',
            'promo_price',
            'promo_start_date',
            'promo_end_date',
            'charge_syndic',
            'charge_water',
            'charge_gas',
            'charge_electricity',
            'charge_other',
            'internal_notes',
            'created_by'
        ];
        
        $this->forge->dropColumn('properties', $fields);
    }
}
