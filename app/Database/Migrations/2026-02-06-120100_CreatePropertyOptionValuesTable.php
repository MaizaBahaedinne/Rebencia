<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertyOptionValuesTable extends Migration
{
    public function up()
    {
        // Table de liaison: propriétés <-> options sélectionnées
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'property_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'option_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'comment' => 'Pour les options qui ont une valeur (ex: nombre de places parking)'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('property_id');
        $this->forge->addKey('option_id');
        $this->forge->addUniqueKey(['property_id', 'option_id']);
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('option_id', 'property_options', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_option_values', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_option_values', true);
    }
}
