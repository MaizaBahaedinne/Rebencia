<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyTypesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('is_active');
        $this->forge->createTable('property_types', true);

        // Données initiales
        $now = date('Y-m-d H:i:s');
        $seeds = [
            ['name' => 'Appartement',    'slug' => 'appartement',    'icon' => 'bi-building'],
            ['name' => 'Maison',          'slug' => 'maison',          'icon' => 'bi-house'],
            ['name' => 'Villa',           'slug' => 'villa',           'icon' => 'bi-house-door'],
            ['name' => 'Bureau',          'slug' => 'bureau',          'icon' => 'bi-briefcase'],
            ['name' => 'Local commercial','slug' => 'local-commercial','icon' => 'bi-shop'],
            ['name' => 'Terrain',         'slug' => 'terrain',         'icon' => 'bi-map'],
            ['name' => 'Immeuble',        'slug' => 'immeuble',        'icon' => 'bi-buildings'],
        ];

        foreach ($seeds as $row) {
            $row['is_active']  = 1;
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            $this->db->table('property_types')->insert($row);
        }
    }

    public function down(): void
    {
        $this->forge->dropTable('property_types', true);
    }
}
