<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour créer la table property_proximity
 * Stockage des proximités et distances (école, transport, commerce, etc.)
 */
class CreatePropertyProximityTable extends Migration
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
            'proximity_type' => [
                'type' => 'ENUM',
                'constraint' => [
                    'transport_public', 
                    'ecole', 
                    'administration', 
                    'municipalite', 
                    'hopital', 
                    'commerces', 
                    'mosquee', 
                    'eglise',
                    'parc',
                    'plage',
                    'autre'
                ],
                'comment' => 'Type de proximité'
            ],
            'has_access' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
                'comment' => 'Accessible ou non'
            ],
            'distance_m' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Distance en mètres'
            ],
            'distance_text' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Distance textuelle (ex: 5 min à pied)'
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
        $this->forge->createTable('property_proximity');
    }

    public function down()
    {
        $this->forge->dropTable('property_proximity');
    }
}
