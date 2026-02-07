<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration pour créer la table property_documents
 * Stockage des documents du bien (contrat, titre foncier, plans, diagnostics, etc.)
 */
class CreatePropertyDocumentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'property_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'comment' => 'ID du bien immobilier'
            ],
            'document_type' => [
                'type' => 'ENUM',
                'constraint' => [
                    'contrat', 
                    'titre_foncier', 
                    'plan', 
                    'diagnostic_performance_energetique',
                    'diagnostic_technique',
                    'certificat_conformite',
                    'autorisation_construction',
                    'photo',
                    'autre'
                ],
                'comment' => 'Type de document'
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Nom du fichier'
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'comment' => 'Chemin du fichier'
            ],
            'file_size' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Taille du fichier en octets'
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'comment' => 'Type MIME'
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Description du document'
            ],
            'uploaded_by' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID de l\'utilisateur qui a uploadé'
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('property_id');
        $this->forge->addForeignKey('property_id', 'properties', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('property_documents');
    }

    public function down()
    {
        $this->forge->dropTable('property_documents');
    }
}
