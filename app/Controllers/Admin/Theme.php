<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ThemeSettingModel;

class Theme extends BaseController
{
    protected $themeModel;

    public function __construct()
    {
        $this->themeModel = new ThemeSettingModel();
    }

    /**
     * Affiche la page de gestion du thème
     */
    public function index()
    {
        $data = [
            'page_title' => 'Personnalisation du Thème',
            'theme' => $this->themeModel->getCurrentTheme()
        ];

        return view('admin/theme/index', $data);
    }

    /**
     * Met à jour les paramètres du thème
     */
    public function update()
    {
        $validation = $this->validate([
            'primary_color' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'secondary_color' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'accent_color' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'text_dark' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'text_light' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'background_light' => 'required|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'font_family_primary' => 'required|max_length[100]',
            'font_family_secondary' => 'required|max_length[100]',
            'font_size_base' => 'required|max_length[20]',
            'border_radius' => 'required|max_length[20]'
        ]);

        if (!$validation) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'primary_color' => $this->request->getPost('primary_color'),
            'secondary_color' => $this->request->getPost('secondary_color'),
            'accent_color' => $this->request->getPost('accent_color'),
            'text_dark' => $this->request->getPost('text_dark'),
            'text_light' => $this->request->getPost('text_light'),
            'background_light' => $this->request->getPost('background_light'),
            'font_family_primary' => $this->request->getPost('font_family_primary'),
            'font_family_secondary' => $this->request->getPost('font_family_secondary'),
            'font_size_base' => $this->request->getPost('font_size_base'),
            'border_radius' => $this->request->getPost('border_radius'),
            // Boutons primaires
            'button_bg_color' => $this->request->getPost('button_bg_color'),
            'button_text_color' => $this->request->getPost('button_text_color'),
            'button_hover_bg_color' => $this->request->getPost('button_hover_bg_color'),
            'button_hover_text_color' => $this->request->getPost('button_hover_text_color'),
            'button_border_width' => $this->request->getPost('button_border_width'),
            'button_border_color' => $this->request->getPost('button_border_color'),
            'button_padding' => $this->request->getPost('button_padding'),
            'button_font_size' => $this->request->getPost('button_font_size'),
            'button_font_weight' => $this->request->getPost('button_font_weight'),
            // Boutons secondaires
            'button_secondary_bg_color' => $this->request->getPost('button_secondary_bg_color'),
            'button_secondary_text_color' => $this->request->getPost('button_secondary_text_color'),
            'button_secondary_hover_bg_color' => $this->request->getPost('button_secondary_hover_bg_color'),
            'button_secondary_hover_text_color' => $this->request->getPost('button_secondary_hover_text_color'),
            // Liens
            'link_color' => $this->request->getPost('link_color'),
            'link_hover_color' => $this->request->getPost('link_hover_color'),
            'link_decoration' => $this->request->getPost('link_decoration'),
            // Mise en page
            'page_max_width' => $this->request->getPost('page_max_width')
        ];

        // Mettre à jour le thème (il n'y a qu'une seule ligne)
        $this->themeModel->where('id', 1)->set($data)->update();

        // Générer le fichier CSS
        $this->generateThemeCSS();

        return redirect()->to('admin/theme')->with('success', 'Thème mis à jour avec succès');
    }

    /**
     * Génère le fichier CSS du thème
     */
    private function generateThemeCSS()
    {
        $css = $this->themeModel->generateCSS();

        // Créer le dossier si nécessaire
        $cssPath = FCPATH . 'assets/css';
        if (!is_dir($cssPath)) {
            mkdir($cssPath, 0755, true);
        }

        // Écrire le fichier CSS
        file_put_contents($cssPath . '/theme.css', $css);
    }

    /**
     * Réinitialise le thème aux valeurs par défaut
     */
    public function reset()
    {
        $defaultTheme = [
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
            // Boutons primaires
            'button_bg_color' => '#667eea',
            'button_text_color' => '#ffffff',
            'button_hover_bg_color' => '#764ba2',
            'button_hover_text_color' => '#ffffff',
            'button_border_width' => '0px',
            'button_border_color' => '#667eea',
            'button_padding' => '12px 30px',
            'button_font_size' => '16px',
            'button_font_weight' => '500',
            // Boutons secondaires
            'button_secondary_bg_color' => '#6c757d',
            'button_secondary_text_color' => '#ffffff',
            'button_secondary_hover_bg_color' => '#5a6268',
            'button_secondary_hover_text_color' => '#ffffff',
            // Liens
            'link_color' => '#667eea',
            'link_hover_color' => '#764ba2',
            'link_decoration' => 'none',
            // Mise en page
            'page_max_width' => '1200px'
        ];

        $this->themeModel->where('id', 1)->set($defaultTheme)->update();
        $this->generateThemeCSS();

        return redirect()->to('admin/theme')->with('success', 'Thème réinitialisé aux valeurs par défaut');
    }

    /**
     * Retourne un aperçu du CSS en AJAX
     */
    public function preview()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $theme = $this->request->getJSON(true);
        $css = $this->themeModel->generateCSS($theme);

        return $this->response->setJSON([
            'success' => true,
            'css' => $css
        ]);
    }
}
