<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyLocationScoringTable extends Migration
{
    public function up()
    {
        // Table pour les scores de localisation (proximité écoles, transports, commerces, etc.)
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
            'proximity_to_schools' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => '0-100 score'
            ],
            'proximity_to_transport' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'gare, métro, bus'
            ],
            'proximity_to_shopping' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'commerces, supermarchés'
            ],
            'proximity_to_parks' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'parcs, espaces verts'
            ],
            'proximity_to_healthcare' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'hopitaux, cliniques'
            ],
            'proximity_to_restaurants' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'restaurants, cafés'
            ],
            'proximity_to_entertainment' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'cinémas, théâtres, loisirs'
            ],
            'area_safety_score' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'sécurité du quartier'
            ],
            'area_noise_level' => [
                'type' => 'ENUM',
                'constraint' => ['very_quiet', 'quiet', 'moderate', 'noisy', 'very_noisy'],
                'null' => true,
            ],
            'area_cleanliness_score' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'propreté du quartier'
            ],
            'overall_location_score' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'score de localisation global calculé'
            ],
            'location_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
                'null' => true,
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
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
        $this->forge->createTable('property_location_scoring', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_location_scoring', true);
    }
}
