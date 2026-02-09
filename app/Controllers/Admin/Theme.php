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
            'border_radius' => $this->request->getPost('border_radius')
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
        $theme = $this->themeModel->getCurrentTheme();
        $css = $this->themeModel->generateCSS($theme);

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
            'border_radius' => '8px'
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
