<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThemeSettingsTable extends Migration
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
            'primary_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#667eea',
            ],
            'secondary_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#764ba2',
            ],
            'accent_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#f5576c',
            ],
            'text_dark' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#2d3748',
            ],
            'text_light' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#718096',
            ],
            'background_light' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#f7fafc',
            ],
            'font_family_primary' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Poppins',
            ],
            'font_family_secondary' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'default'    => 'Roboto',
            ],
            'font_size_base' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '16px',
            ],
            'border_radius' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '8px',
            ],
            'button_bg_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#667eea',
            ],
            'button_text_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#ffffff',
            ],
            'button_hover_bg_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#764ba2',
            ],
            'button_hover_text_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#ffffff',
            ],
            'button_border_width' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => '0px',
            ],
            'button_border_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'default'    => '#667eea',
            ],
            'button_padding' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '12px 30px',
            ],
            'button_font_size' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => '16px',
            ],
            'button_font_weight' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'default'    => '500',
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
        $this->forge->createTable('theme_settings', true);
        
        // Insert default theme
        $this->db->table('theme_settings')->insert([
            'primary_color' => '#667eea',
            'secondary_color' => '#764ba2',
            'accent_color' => '#f5576c',
            'text_dark' => '#2d3748',
            'text_light' => '#718096',
            'background_light' => '#f7fafc',
            'font_family_primary' => 'Poppins',
            'font_family_secondary' => 'Roboto',
            'font_size_base' => '16px',
            'border_radius' => '8px',
            'button_bg_color' => '#667eea',
            'button_text_color' => '#ffffff',
            'button_hover_bg_color' => '#764ba2',
            'button_hover_text_color' => '#ffffff',
            'button_border_width' => '0px',
            'button_border_color' => '#667eea',
            'button_padding' => '12px 30px',
            'button_font_size' => '16px',
            'button_font_weight' => '500',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('theme_settings');
    }
}
