<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAgencyZonesTable extends Migration
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
            'agency_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'zone_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'boundary_coordinates' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of polygon coordinates for drawing zone boundaries on map',
            ],
            'is_primary' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 if this is the primary zone for the agency',
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
        $this->forge->addKey(['agency_id', 'zone_id'], false, true); // Unique constraint
        $this->forge->addForeignKey('agency_id', 'agencies', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('zone_id', 'zones', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('agency_zones');
    }

    public function down()
    {
        $this->forge->dropTable('agency_zones');
    }
}
