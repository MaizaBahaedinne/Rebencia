<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePropertyOrientationTable extends Migration
{
    public function up()
    {
        // Table pour l'orientation et l'exposition au soleil
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
            'primary_orientation' => [
                'type' => 'ENUM',
                'constraint' => ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'],
                'null' => true,
                'comment' => 'orientation principale'
            ],
            'secondary_orientation' => [
                'type' => 'ENUM',
                'constraint' => ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'],
                'null' => true,
                'comment' => 'orientation secondaire'
            ],
            'sun_exposure' => [
                'type' => 'ENUM',
                'constraint' => ['none', 'morning', 'afternoon', 'all_day', 'partial'],
                'null' => true,
            ],
            'morning_sun' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'afternoon_sun' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'evening_sun' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'view_type' => [
                'type' => 'ENUM',
                'constraint' => ['garden', 'street', 'water', 'mountain', 'city', 'park', 'courtyard', 'none'],
                'null' => true,
                'comment' => 'type de vue'
            ],
            'view_quality' => [
                'type' => 'ENUM',
                'constraint' => ['poor', 'average', 'good', 'excellent'],
                'null' => true,
            ],
            'natural_light_level' => [
                'type' => 'ENUM',
                'constraint' => ['minimal', 'moderate', 'bright', 'very_bright'],
                'null' => true,
            ],
            'has_balcony_or_terrace' => [
                'type' => 'TINYINT',
                'default' => 0,
            ],
            'balcony_orientation' => [
                'type' => 'ENUM',
                'constraint' => ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'],
                'null' => true,
            ],
            'balcony_surface' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'surface en m²'
            ],
            'wind_exposure' => [
                'type' => 'ENUM',
                'constraint' => ['sheltered', 'moderate', 'exposed'],
                'null' => true,
            ],
            'privacy_level' => [
                'type' => 'ENUM',
                'constraint' => ['none', 'partial', 'good', 'excellent'],
                'null' => true,
            ],
            'orientation_notes' => [
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
        $this->forge->createTable('property_orientation', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_orientation', true);
    }
}
