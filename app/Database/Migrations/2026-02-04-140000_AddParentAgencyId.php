<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentAgencyId extends Migration
{
    public function up()
    {
        // Ajouter parent_agency_id pour créer une hiérarchie d'agences
        $this->forge->addColumn('agencies', [
            'parent_agency_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ],
            'is_headquarters' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = Siège, 0 = Agence locale',
                'after' => 'parent_agency_id',
            ],
        ]);

        // Ajouter une clé étrangère
        $this->forge->addForeignKey('parent_agency_id', 'agencies', 'id', 'SET NULL', 'CASCADE', 'fk_agencies_parent');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE agencies DROP FOREIGN KEY fk_agencies_parent');
        $this->forge->dropColumn('agencies', ['parent_agency_id', 'is_headquarters']);
    }
}
