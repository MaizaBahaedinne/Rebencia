<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBoundaryCoordinatesToZones extends Migration
{
    public function up()
    {
        // Add column using raw SQL with error handling
        // This approach handles the case where the column already exists
        try {
            $sql = "ALTER TABLE `zones` ADD COLUMN `boundary_coordinates` LONGTEXT NULL COMMENT 'JSON array of polygon coordinates [[lat, lng], ...]'";
            $this->db->query($sql);
        } catch (\exception $e) {
            // Column probably already exists from migration or table creation
            // This is fine, we can ignore this error
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                // Expected error - column already exists
                return;
            }
            // For any other error, re-throw it
            throw $e;
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
