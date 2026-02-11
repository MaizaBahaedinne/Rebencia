<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddButtonThemeColumns extends Migration
{
    public function up()
    {
        // Vérifier si la colonne existe déjà
        if (!$this->db->fieldExists('button_bg_color', 'theme_settings')) {
            $fields = [
                'button_bg_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 7,
                    'default'    => '#667eea',
                    'after'      => 'border_radius',
                ],
                'button_text_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 7,
                    'default'    => '#ffffff',
                    'after'      => 'button_bg_color',
                ],
                'button_hover_bg_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 7,
                    'default'    => '#764ba2',
                    'after'      => 'button_text_color',
                ],
                'button_hover_text_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 7,
                    'default'    => '#ffffff',
                    'after'      => 'button_hover_bg_color',
                ],
                'button_border_width' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'default'    => '0px',
                    'after'      => 'button_hover_text_color',
                ],
                'button_border_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 7,
                    'default'    => '#667eea',
                    'after'      => 'button_border_width',
                ],
                'button_padding' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => '12px 30px',
                    'after'      => 'button_border_color',
                ],
                'button_font_size' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => '16px',
                    'after'      => 'button_padding',
                ],
                'button_font_weight' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'default'    => '500',
                    'after'      => 'button_font_size',
                ],
            ];

            $this->forge->addColumn('theme_settings', $fields);
        }
        
        // Mettre à jour la ligne existante avec les valeurs par défaut
        $this->db->table('theme_settings')->update([
            'button_bg_color' => '#667eea',
            'button_text_color' => '#ffffff',
            'button_hover_bg_color' => '#764ba2',
            'button_hover_text_color' => '#ffffff',
            'button_border_width' => '0px',
            'button_border_color' => '#667eea',
            'button_padding' => '12px 30px',
            'button_font_size' => '16px',
            'button_font_weight' => '500',
        ], ['id' => 1]);
    }

    public function down()
    {
        $this->forge->dropColumn('theme_settings', [
            'button_bg_color',
            'button_text_color',
            'button_hover_bg_color',
            'button_hover_text_color',
            'button_border_width',
            'button_border_color',
            'button_padding',
            'button_font_size',
            'button_font_weight',
        ]);
    }
}
