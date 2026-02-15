<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBoundaryCoordinatesToZones extends Migration
{
    public function up()
    {
        // Check if column already exists before adding it
        // This handles the case where the zones table was created with the column already included
        try {
            $result = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='zones' AND COLUMN_NAME='boundary_coordinates' AND TABLE_SCHEMA=DATABASE()");
            
            if ($result->getRowCount() === 0) {
                // Column doesn't exist, add it
                $fields = [
                    'boundary_coordinates' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'comment' => 'JSON array of polygon coordinates [[lat, lng], ...]'
                    ]
                ];
                
                $this->forge->addColumn('zones', $fields);
            }
        } catch (\Exception $e) {
            // If query fails, try to add the column anyway
            $fields = [
                'boundary_coordinates' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of polygon coordinates [[lat, lng], ...]'
                ]
            ];
            
            try {
                $this->forge->addColumn('zones', $fields);
            } catch (\Exception $e2) {
                // Column likely already exists, ignore
            }
        }
    }

    public function down()
    {
        // Only drop the column if it exists
        try {
            $result = $this->db->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='zones' AND COLUMN_NAME='boundary_coordinates' AND TABLE_SCHEMA=DATABASE()");
            
            if ($result->getRowCount() > 0) {
                $this->forge->dropColumn('zones', 'boundary_coordinates');
            }
        } catch (\Exception $e) {
            // If query fails, try to drop the column anyway
            try {
                $this->forge->dropColumn('zones', 'boundary_coordinates');
            } catch (\Exception $e2) {
                // Column doesn't exist, ignore
            }
        }
    }
}
