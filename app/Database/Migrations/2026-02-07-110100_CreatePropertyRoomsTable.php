<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour créer la table property_rooms
 * Stockage de la surface détaillée de chaque pièce
 */
class CreatePropertyRoomsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'comment' => 'ID du bien immobilier'
            ],
            'room_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'comment' => 'Nom de la pièce (Salon, Chambre 1, etc.)'
            ],
            'room_type' => [
                'type' => 'ENUM',
                'constraint' => ['salon', 'chambre', 'cuisine', 'salle_bain', 'wc', 'bureau', 'dressing', 'cave', 'garage', 'terrasse', 'balcon', 'autre'],
                'default' => 'autre',
                'comment' => 'Type de pièce'
            ],
            'area_m2' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
                'comment' => 'Surface en m²'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('property_id');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_rooms');
    }

    public function down()
    {
        $this->forge->dropTable('property_rooms');
    }
}
