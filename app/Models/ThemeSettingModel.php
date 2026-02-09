<?php

namespace App\Models;

use CodeIgniter\Model;

class ThemeSettingModel extends Model
{
    protected $table            = 'theme_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'primary_color',
        'secondary_color',
        'accent_color',
        'text_dark',
        'text_light',
        'background_light',
        'font_family_primary',
        'font_family_secondary',
        'font_size_base',
        'border_radius'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Get current theme
     */
    public function getCurrentTheme()
    {
        $theme = $this->first();
        
        if (!$theme) {
            // Return default theme if none exists
            return [
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
            ];
        }
        
        return $theme;
    }
    
    /**
     * Generate CSS variables from theme
     */
    public function generateCSS()
    {
        $theme = $this->getCurrentTheme();
        
        $css = ":root {\n";
        $css .= "    --primary-color: {$theme['primary_color']};\n";
        $css .= "    --secondary-color: {$theme['secondary_color']};\n";
        $css .= "    --accent-color: {$theme['accent_color']};\n";
        $css .= "    --text-dark: {$theme['text_dark']};\n";
        $css .= "    --text-light: {$theme['text_light']};\n";
        $css .= "    --bg-light: {$theme['background_light']};\n";
        $css .= "    --primary-gradient: linear-gradient(135deg, {$theme['primary_color']} 0%, {$theme['secondary_color']} 100%);\n";
        $css .= "    --font-primary: '{$theme['font_family_primary']}', sans-serif;\n";
        $css .= "    --font-secondary: '{$theme['font_family_secondary']}', sans-serif;\n";
        $css .= "    --font-size-base: {$theme['font_size_base']};\n";
        $css .= "    --border-radius: {$theme['border_radius']};\n";
        $css .= "}\n";
        
        return $css;
    }
}
