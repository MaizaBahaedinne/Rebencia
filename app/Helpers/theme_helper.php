<?php

if (!function_exists('load_theme_css')) {
    /**
     * Charge le CSS du thème personnalisé depuis la base de données
     * 
     * @return string CSS généré
     */
    function load_theme_css(): string
    {
        try {
            $themeModel = model('ThemeSettingModel');
            return $themeModel->generateCSS();
        } catch (\Exception $e) {
            // En cas d'erreur, retourner le thème par défaut
            log_message('error', 'Erreur lors du chargement du thème: ' . $e->getMessage());
            return get_default_theme_css();
        }
    }
}

if (!function_exists('get_default_theme_css')) {
    /**
     * Retourne le CSS du thème par défaut
     * 
     * @return string CSS par défaut
     */
    function get_default_theme_css(): string
    {
        return ":root {
    --theme-primary: #667eea;
    --theme-secondary: #764ba2;
    --theme-accent: #f5576c;
    --theme-text-dark: #2d3748;
    --theme-text-light: #ffffff;
    --theme-bg-light: #f7fafc;
    --primary-color: #667eea;
    --secondary-color: #764ba2;
    --accent-color: #f5576c;
    --text-dark: #2d3748;
    --text-light: #ffffff;
    --bg-light: #f7fafc;
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --secondary-gradient: linear-gradient(135deg, #f5576c 0%, #764ba2 100%);
    --font-primary: 'Poppins', sans-serif;
    --font-secondary: 'Roboto', sans-serif;
    --font-size-base: 16px;
    --border-radius: 8px;
    --border-color: rgba(0, 0, 0, 0.1);
    --button-bg-color: #667eea;
    --button-text-color: #ffffff;
    --button-hover-bg-color: #764ba2;
    --button-hover-text-color: #ffffff;
    --button-border-width: 0px;
    --button-border-color: #667eea;
    --button-padding: 12px 30px;
    --button-font-size: 16px;
    --button-font-weight: 500;
}

body {
    font-family: var(--font-secondary);
    font-size: var(--font-size-base);
    color: var(--text-dark);
}

h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6 {
    font-family: var(--font-primary);
}

a {
    color: var(--primary-color);
}

a:hover {
    color: var(--secondary-color);
}

.btn, .button, button.btn-primary, a.btn-primary {
    background-color: var(--button-bg-color);
    color: var(--button-text-color);
    padding: var(--button-padding);
    font-size: var(--button-font-size);
    font-weight: var(--button-font-weight);
    border-width: var(--button-border-width);
    border-color: var(--button-border-color);
    border-style: solid;
    border-radius: var(--border-radius);
    transition: all 0.3s ease;
}

.btn:hover, .button:hover, button.btn-primary:hover, a.btn-primary:hover {
    background-color: var(--button-hover-bg-color);
    color: var(--button-hover-text-color);
}

.btn-primary {
    background: var(--primary-gradient);
    border: none;
    border-radius: var(--border-radius);
}

.card {
    border-radius: var(--border-radius);
}";
    }
}

if (!function_exists('get_theme_setting')) {
    /**
     * Récupère une valeur spécifique du thème
     * 
     * @param string $key Clé du paramètre
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    function get_theme_setting(string $key, $default = null)
    {
        try {
            $themeModel = model('ThemeSettingModel');
            $theme = $themeModel->getCurrentTheme();
            return $theme[$key] ?? $default;
        } catch (\Exception $e) {
            return $default;
        }
    }
}

if (!function_exists('get_theme_fonts')) {
    /**
     * Retourne les polices Google Fonts à charger
     * 
     * @return string URL des fonts Google
     */
    function get_theme_fonts(): string
    {
        $fonts = [
            'Poppins:wght@300;400;500;600;700',
            'Roboto:wght@300;400;500;700',
            'Open+Sans:wght@300;400;600;700',
            'Montserrat:wght@300;400;500;600;700',
            'Lato:wght@300;400;700',
            'Raleway:wght@300;400;500;600;700',
            'Inter:wght@300;400;500;600;700',
            'Nunito:wght@300;400;600;700',
            'Merriweather:wght@300;400;700'
        ];
        
        return 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $fonts) . '&display=swap';
    }
}
