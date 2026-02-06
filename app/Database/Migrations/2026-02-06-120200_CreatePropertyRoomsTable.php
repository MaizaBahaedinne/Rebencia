<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyRoomsTable extends Migration
{
    public function up()
    {
        // Table pour les dimensions des pièces
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
            'name_fr' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'name_ar' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'room_type' => [
                'type' => 'ENUM',
                'constraint' => ['bedroom', 'bathroom', 'kitchen', 'living', 'dining', 'office', 'storage', 'utility', 'other'],
                'default' => 'other',
            ],
            'surface' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'en m²'
            ],
            'width' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'largeur en m'
            ],
            'length' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'longueur en m'
            ],
            'height' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => true,
                'comment' => 'hauteur sous plafond en m'
            ],
            'has_window' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
            'window_type' => [
                'type' => 'ENUM',
                'constraint' => ['none', 'single', 'double', 'bay', 'french_door'],
                'null' => true,
            ],
            'orientation' => [
                'type' => 'ENUM',
                'constraint' => ['N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW'],
                'null' => true,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
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
        $this->forge->addKey('property_id');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_rooms', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_rooms', true);
    }
}
