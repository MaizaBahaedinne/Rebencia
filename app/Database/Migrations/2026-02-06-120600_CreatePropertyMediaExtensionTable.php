<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

class CreatePropertyMediaExtensionTable extends Migration
{
    public function up()
    {
        // Table pour les fichiers multimédia avancés (plans, rendus 3D, vidéos)
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
            'file_type' => [
                'type' => 'ENUM',
                'constraint' => ['floor_plan', '3d_render', 'video_tour', 'drone_photo', 'technical_plan', 'document', 'other'],
                'default' => 'other',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
                'comment' => 'chemin du fichier'
            ],
            'file_size' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'taille en octets'
            ],
            'file_mime' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'thumbnail_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'comment' => 'miniature du fichier'
            ],
            'floor_plan_room_count' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'nombre de pièces sur le plan'
            ],
            'floor_number' => [
                'type' => 'INT',
                'null' => true,
                'comment' => 'numéro d\'étage représenté'
            ],
            'is_primary' => [
                'type' => 'TINYINT',
                'default' => 0,
                'comment' => 'est le fichier principal du type'
            ],
            'is_published' => [
                'type' => 'TINYINT',
                'default' => 1,
            ],
            'sort_order' => [
                'type' => 'INT',
                'default' => 0,
            ],
            'view_count' => [
                'type' => 'INT',
                'default' => 0,
                'comment' => 'nombre de vues'
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
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

        $this->forge->addKey('id', false, false, 'PRIMARY');
        $this->forge->addKey('property_id');
        $this->forge->addKey('file_type');
        $this->forge->addKey('uploaded_by');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'SET NULL', 'SET NULL');
        $this->forge->createTable('property_media_extension', true);
    }

    public function down()
    {
        $this->forge->dropTable('property_media_extension', true);
    }
}
