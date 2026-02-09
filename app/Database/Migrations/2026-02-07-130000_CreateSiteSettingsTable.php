<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type'       => 'ENUM',
                'constraint' => ['text', 'textarea', 'image', 'json'],
                'default'    => 'text',
            ],
            'group_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addKey('setting_key');
        $this->forge->addKey('group_name');
        
        $this->forge->createTable('site_settings', true);
        
        // Insert default settings
        $data = [
            ['setting_key' => 'site_name', 'setting_value' => 'REBENCIA', 'setting_type' => 'text', 'group_name' => 'general'],
            ['setting_key' => 'site_description', 'setting_value' => 'Votre partenaire immobilier de confiance en Tunisie', 'setting_type' => 'textarea', 'group_name' => 'general'],
            ['setting_key' => 'footer_about', 'setting_value' => 'Votre partenaire de confiance pour tous vos projets immobiliers en Tunisie. Nous vous accompagnons dans l\'achat, la vente et la location de biens d\'exception.', 'setting_type' => 'textarea', 'group_name' => 'footer'],
            ['setting_key' => 'contact_phone_1', 'setting_value' => '+216 12 345 678', 'setting_type' => 'text', 'group_name' => 'contact'],
            ['setting_key' => 'contact_phone_2', 'setting_value' => '+216 98 765 432', 'setting_type' => 'text', 'group_name' => 'contact'],
            ['setting_key' => 'contact_email', 'setting_value' => 'contact@rebencia.tn', 'setting_type' => 'text', 'group_name' => 'contact'],
            ['setting_key' => 'contact_address', 'setting_value' => 'Avenue Habib Bourguiba, Tunis, Tunisie', 'setting_type' => 'text', 'group_name' => 'contact'],
            ['setting_key' => 'social_facebook', 'setting_value' => 'https://facebook.com/rebencia', 'setting_type' => 'text', 'group_name' => 'social'],
            ['setting_key' => 'social_instagram', 'setting_value' => 'https://instagram.com/rebencia', 'setting_type' => 'text', 'group_name' => 'social'],
            ['setting_key' => 'social_linkedin', 'setting_value' => 'https://linkedin.com/company/rebencia', 'setting_type' => 'text', 'group_name' => 'social'],
            ['setting_key' => 'social_youtube', 'setting_value' => 'https://youtube.com/@rebencia', 'setting_type' => 'text', 'group_name' => 'social'],
            ['setting_key' => 'social_whatsapp', 'setting_value' => '+21612345678', 'setting_type' => 'text', 'group_name' => 'social'],
        ];
        
        $this->db->table('site_settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('site_settings');
    }
}
