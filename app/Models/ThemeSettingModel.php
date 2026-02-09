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
        'border_radius',
        'button_bg_color',
        'button_text_color',
        'button_hover_bg_color',
        'button_hover_text_color',
        'button_border_width',
        'button_border_color',
        'button_padding',
        'button_font_size',
        'button_font_weight',
        'button_secondary_bg_color',
        'button_secondary_text_color',
        'button_secondary_hover_bg_color',
        'button_secondary_hover_text_color',
        'link_color',
        'link_hover_color',
        'link_decoration',
        'page_max_width'
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
                'text_light' => '#ffffff',
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
                'button_secondary_bg_color' => '#6c757d',
                'button_secondary_text_color' => '#ffffff',
                'button_secondary_hover_bg_color' => '#5a6268',
                'button_secondary_hover_text_color' => '#ffffff',
                'link_color' => '#667eea',
                'link_hover_color' => '#764ba2',
                'link_decoration' => 'none',
                'page_max_width' => '1200px'
            ];
        }
        
        return $theme;
    }
    
    /**
     * Generate CSS variables from theme
     */
    public function generateCSS($themeData = null)
    {
        $theme = $themeData ?? $this->getCurrentTheme();
        
        $css = ":root {\n";
        $css .= "    /* Couleurs du thème */\n";
        $css .= "    --theme-primary: {$theme['primary_color']};\n";
        $css .= "    --theme-secondary: {$theme['secondary_color']};\n";
        $css .= "    --theme-accent: {$theme['accent_color']};\n";
        $css .= "    --theme-text-dark: {$theme['text_dark']};\n";
        $css .= "    --theme-text-light: {$theme['text_light']};\n";
        $css .= "    --theme-bg-light: {$theme['background_light']};\n";
        $css .= "    \n";
        $css .= "    /* Variables compatibles anciennes */\n";
        $css .= "    --primary-color: {$theme['primary_color']};\n";
        $css .= "    --secondary-color: {$theme['secondary_color']};\n";
        $css .= "    --accent-color: {$theme['accent_color']};\n";
        $css .= "    --text-dark: {$theme['text_dark']};\n";
        $css .= "    --text-light: {$theme['text_light']};\n";
        $css .= "    --bg-light: {$theme['background_light']};\n";
        $css .= "    \n";
        $css .= "    /* Typographie */\n";
        $css .= "    --font-primary: '{$theme['font_family_primary']}', sans-serif;\n";
        $css .= "    --font-secondary: '{$theme['font_family_secondary']}', sans-serif;\n";
        $css .= "    --font-size-base: {$theme['font_size_base']};\n";
        $css .= "    \n";
        $css .= "    /* Bordures */\n";
        $css .= "    --border-radius: {$theme['border_radius']};\n";
        $css .= "    --border-color: rgba(0, 0, 0, 0.1);\n";
        $css .= "    \n";
        $css .= "    /* Boutons */\n";
        $css .= "    --button-bg-color: {$theme['button_bg_color']};\n";
        $css .= "    --button-text-color: {$theme['button_text_color']};\n";
        $css .= "    --button-hover-bg-color: {$theme['button_hover_bg_color']};\n";
        $css .= "    --button-hover-text-color: {$theme['button_hover_text_color']};\n";
        $css .= "    --button-border-width: {$theme['button_border_width']};\n";
        $css .= "    --button-border-color: {$theme['button_border_color']};\n";
        $css .= "    --button-padding: {$theme['button_padding']};\n";
        $css .= "    --button-font-size: {$theme['button_font_size']};\n";
        $css .= "    --button-font-weight: {$theme['button_font_weight']};\n";
        $css .= "    \n";
        $css .= "    /* Boutons Secondaires */\n";
        $css .= "    --button-secondary-bg-color: {$theme['button_secondary_bg_color']};\n";
        $css .= "    --button-secondary-text-color: {$theme['button_secondary_text_color']};\n";
        $css .= "    --button-secondary-hover-bg-color: {$theme['button_secondary_hover_bg_color']};\n";
        $css .= "    --button-secondary-hover-text-color: {$theme['button_secondary_hover_text_color']};\n";
        $css .= "    \n";
        $css .= "    /* Liens */\n";
        $css .= "    --link-color: {$theme['link_color']};\n";
        $css .= "    --link-hover-color: {$theme['link_hover_color']};\n";
        $css .= "    --link-decoration: {$theme['link_decoration']};\n";
        $css .= "    \n";
        $css .= "    /* Mise en page */\n";
        $css .= "    --page-max-width: {$theme['page_max_width']};\n";
        $css .= "}\n\n";
        
        // Ajouter les styles de base
        $css .= "/* Application du thème */\n";
        $css .= "body {\n";
        $css .= "    font-family: var(--font-secondary);\n";
        $css .= "    font-size: var(--font-size-base);\n";
        $css .= "    color: var(--text-dark);\n";
        $css .= "}\n\n";
        
        $css .= "h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {\n";
        $css .= "    font-family: var(--font-primary);\n";
        $css .= "}\n\n";
        
        $css .= "a {\n";
        $css .= "    color: var(--link-color);\n";
        $css .= "    text-decoration: var(--link-decoration);\n";
        $css .= "}\n\n";
        
        $css .= "a:hover {\n";
        $css .= "    color: var(--link-hover-color);\n";
        $css .= "}\n\n";
        
        // Styles des boutons
        $css .= "/* Styles des boutons */\n";
        $css .= ".btn, .button, button.btn-primary, a.btn-primary {\n";
        $css .= "    background-color: var(--button-bg-color);\n";
        $css .= "    color: var(--button-text-color);\n";
        $css .= "    padding: var(--button-padding);\n";
        $css .= "    font-size: var(--button-font-size);\n";
        $css .= "    font-weight: var(--button-font-weight);\n";
        $css .= "    border-width: var(--button-border-width);\n";
        $css .= "    border-color: var(--button-border-color);\n";
        $css .= "    border-style: solid;\n";
        $css .= "    border-radius: var(--border-radius);\n";
        $css .= "    transition: all 0.3s ease;\n";
        $css .= "}\n\n";
        
        $css .= ".btn:hover, .button:hover, button.btn-primary:hover, a.btn-primary:hover {\n";
        $css .= "    background-color: var(--button-hover-bg-color);\n";
        $css .= "    color: var(--button-hover-text-color);\n";
        $css .= "}\n\n";
        
        $css .= ".btn-secondary, button.btn-secondary, a.btn-secondary {\n";
        $css .= "    background-color: var(--button-secondary-bg-color);\n";
        $css .= "    color: var(--button-secondary-text-color);\n";
        $css .= "    border: none;\n";
        $css .= "}\n\n";
        
        $css .= ".btn-secondary:hover, button.btn-secondary:hover, a.btn-secondary:hover {\n";
        $css .= "    background-color: var(--button-secondary-hover-bg-color);\n";
        $css .= "    color: var(--button-secondary-hover-text-color);\n";
        $css .= "}\n\n";
        
        $css .= ".card {\n";
        $css .= "    border-radius: var(--border-radius);\n";
        $css .= "}\n";
        
        return $css;
    }
}
