<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateZonesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
                'comment' => 'Zone name in French',
            ],
            'name_ar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Zone name in Arabic',
            ],
            'name_en' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Zone name in English',
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['governorate', 'city', 'region', 'district'],
                'default' => 'city',
                'comment' => 'Type of geographic zone',
            ],
            'parent_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Parent zone ID for hierarchical structure',
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'Tunisia',
                'comment' => 'Country name',
            ],
            'latitude' => [
                'type' => 'DECIMAL',
                'constraint' => [10, 8],
                'null' => true,
                'comment' => 'Latitude coordinate',
            ],
            'longitude' => [
                'type' => 'DECIMAL',
                'constraint' => [11, 8],
                'null' => true,
                'comment' => 'Longitude coordinate',
            ],
            'popularity_score' => [
                'type' => 'INT',
                'constraint' => 3,
                'default' => 0,
                'comment' => 'Popularity score from 0 to 100',
            ],
            'boundary_coordinates' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'comment' => 'JSON array of polygon coordinates for zone boundary on map',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => new \CodeIgniter\Database\RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('parent_id');
        $this->forge->addKey('type');
        $this->forge->addKey('country');
        $this->forge->addForeignKey('parent_id', 'zones', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('zones', true);
    }

    public function down()
    {
        $this->forge->dropTable('zones');
    }
}
