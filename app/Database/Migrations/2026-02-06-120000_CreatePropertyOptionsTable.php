<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePropertyOptionsTable extends Migration
{
    public function up()
    {
        // Table de configuration des options disponibles
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'unique' => true,
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
            'name_en' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Font Awesome icon class'
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['comfort', 'outdoor', 'parking', 'security', 'amenities', 'other'],
                'default' => 'amenities',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'default' => 1,
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

        $this->forge->addKey('id', true);
        $this->forge->createTable('property_options', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_options', true);
    }
}
