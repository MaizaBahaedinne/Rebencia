<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBoundaryCoordinatesToZones extends Migration
{
    public function up()
    {
        // Try to add the column - if it already exists, the exception will be caught and ignored
        try {
            $fields = [
                'boundary_coordinates' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of polygon coordinates [[lat, lng], ...]'
                ]
            ];
            
            $this->forge->addColumn('zones', $fields);
        } catch (\Exception $e) {
            // Column already exists, which is fine - just ignore the error
            // This can happen if the zones table was created with the column already included
        }
    }

    public function down()
    {
        // Try to drop the column - if it doesn't exist, the exception will be caught and ignored
        try {
            $this->forge->dropColumn('zones', 'boundary_coordinates');
        } catch (\Exception $e) {
            // Column doesn't exist, which is fine - just ignore the error
        }
    }
}
