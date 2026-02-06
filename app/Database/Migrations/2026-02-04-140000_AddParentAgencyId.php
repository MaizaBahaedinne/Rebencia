<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddParentAgencyId extends Migration
{
    public function up()
    {
        // Vérifier si les colonnes existent déjà
        $fields = $this->db->getFieldNames('agencies');
        
        $columnsToAdd = [];
        
        if (!in_array('parent_agency_id', $fields)) {
            $columnsToAdd['parent_agency_id'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ];
        }
        
        if (!in_array('is_headquarters', $fields)) {
            $columnsToAdd['is_headquarters'] = [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => '1 = Siège, 0 = Agence locale',
                'after' => 'parent_agency_id',
            ];
        }
        
        // Ajouter les colonnes seulement si elles n'existent pas
        if (!empty($columnsToAdd)) {
            $this->forge->addColumn('agencies', $columnsToAdd);
        }

        // Vérifier si la clé étrangère existe déjà
        $foreignKeys = $this->db->getForeignKeyData('agencies');
        $fkExists = false;
        foreach ($foreignKeys as $fk) {
            if ($fk->constraint_name === 'fk_agencies_parent') {
                $fkExists = true;
                break;
            }
        }
        
        // Ajouter la clé étrangère seulement si elle n'existe pas
        if (!$fkExists && in_array('parent_agency_id', $this->db->getFieldNames('agencies'))) {
            $this->forge->addForeignKey('parent_agency_id', 'agencies', 'id', 'SET NULL', 'CASCADE', 'fk_agencies_parent');
        }
    }

    public function down()
    {
        $this->db->query('ALTER TABLE agencies DROP FOREIGN KEY fk_agencies_parent');
        $this->forge->dropColumn('agencies', ['parent_agency_id', 'is_headquarters']);
    }
}
