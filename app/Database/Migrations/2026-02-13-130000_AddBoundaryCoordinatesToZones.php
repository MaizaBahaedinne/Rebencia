<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBoundaryCoordinatesToZones extends Migration
{
    public function up()
    {
        // Check if column already exists before adding it
        // This handles the case where the zones table was created with the column already included
        if (!$this->forge->fieldExists('boundary_coordinates', 'zones')) {
            $fields = [
                'boundary_coordinates' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of polygon coordinates [[lat, lng], ...]'
                ]
            ];
            
            $this->forge->addColumn('zones', $fields);
        }
    }

    public function down()
    {
        // Only drop the column if it exists
        if ($this->forge->fieldExists('boundary_coordinates', 'zones')) {
            $this->forge->dropColumn('zones', 'boundary_coordinates');
        }
    }
}
