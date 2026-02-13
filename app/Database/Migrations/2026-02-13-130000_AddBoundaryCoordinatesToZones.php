<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBoundaryCoordinatesToZones extends Migration
{
    public function up()
    {
        $fields = [
            'boundary_coordinates' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of polygon coordinates [[lat, lng], ...]'
            ]
        ];
        
        $this->forge->addColumn('zones', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('zones', 'boundary_coordinates');
    }
}
