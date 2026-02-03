<?php

if (!function_exists('get_template_styles')) {
    /**
     * Get template customization styles from settings
     */
    function get_template_styles()
    {
        $settingModel = model('SettingModel');
        
        // Get template settings
        $settings = [
            'primary_color' => $settingModel->get('primary_color', '#0d6efd'),
            'secondary_color' => $settingModel->get('secondary_color', '#6c757d'),
            'success_color' => $settingModel->get('success_color', '#198754'),
            'danger_color' => $settingModel->get('danger_color', '#dc3545'),
            'warning_color' => $settingModel->get('warning_color', '#ffc107'),
            'info_color' => $settingModel->get('info_color', '#0dcaf0'),
            'font_family' => $settingModel->get('font_family', '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif'),
            'font_size_base' => $settingModel->get('font_size_base', '14'),
            'font_size_h1' => $settingModel->get('font_size_heading_h1', '28'),
            'font_size_h2' => $settingModel->get('font_size_heading_h2', '24'),
            'font_size_h3' => $settingModel->get('font_size_heading_h3', '20'),
            'font_size_h4' => $settingModel->get('font_size_heading_h4', '18'),
            'sidebar_bg' => $settingModel->get('sidebar_bg', '#1e293b'),
            'card_shadow' => $settingModel->get('card_shadow', '0 2px 8px rgba(0,0,0,0.08)'),
            'border_radius' => $settingModel->get('border_radius', '10'),
        ];
        
        // Generate CSS variables
        $css = '<style id="template-custom-styles">';
        $css .= ':root {';
        $css .= '--primary-color: ' . $settings['primary_color'] . ';';
        $css .= '--secondary-color: ' . $settings['secondary_color'] . ';';
        $css .= '--success-color: ' . $settings['success_color'] . ';';
        $css .= '--danger-color: ' . $settings['danger_color'] . ';';
        $css .= '--warning-color: ' . $settings['warning_color'] . ';';
        $css .= '--info-color: ' . $settings['info_color'] . ';';
        $css .= '--sidebar-bg: ' . $settings['sidebar_bg'] . ';';
        $css .= '--card-shadow: ' . $settings['card_shadow'] . ';';
        $css .= '--border-radius: ' . $settings['border_radius'] . 'px;';
        $css .= '}';
        
        // Apply font styles
        $css .= 'body { font-family: ' . $settings['font_family'] . '; font-size: ' . $settings['font_size_base'] . 'px; }';
        $css .= 'h1, .h1 { font-size: ' . $settings['font_size_h1'] . 'px !important; }';
        $css .= 'h2, .h2 { font-size: ' . $settings['font_size_h2'] . 'px !important; }';
        $css .= 'h3, .h3 { font-size: ' . $settings['font_size_h3'] . 'px !important; }';
        $css .= 'h4, .h4 { font-size: ' . $settings['font_size_h4'] . 'px !important; }';
        
        // Apply colors to Bootstrap classes
        $css .= '.btn-primary { background-color: ' . $settings['primary_color'] . '; border-color: ' . $settings['primary_color'] . '; }';
        $css .= '.btn-success { background-color: ' . $settings['success_color'] . '; border-color: ' . $settings['success_color'] . '; }';
        $css .= '.btn-danger { background-color: ' . $settings['danger_color'] . '; border-color: ' . $settings['danger_color'] . '; }';
        $css .= '.btn-warning { background-color: ' . $settings['warning_color'] . '; border-color: ' . $settings['warning_color'] . '; }';
        $css .= '.btn-info { background-color: ' . $settings['info_color'] . '; border-color: ' . $settings['info_color'] . '; }';
        
        $css .= '.badge.bg-primary { background-color: ' . $settings['primary_color'] . ' !important; }';
        $css .= '.badge.bg-success { background-color: ' . $settings['success_color'] . ' !important; }';
        $css .= '.badge.bg-danger { background-color: ' . $settings['danger_color'] . ' !important; }';
        $css .= '.badge.bg-warning { background-color: ' . $settings['warning_color'] . ' !important; }';
        $css .= '.badge.bg-info { background-color: ' . $settings['info_color'] . ' !important; }';
        
        $css .= '.card { box-shadow: ' . $settings['card_shadow'] . '; border-radius: ' . $settings['border_radius'] . 'px; }';
        $css .= '.btn { border-radius: ' . $settings['border_radius'] . 'px; }';
        
        $css .= '</style>';
        
        return $css;
    }
}
