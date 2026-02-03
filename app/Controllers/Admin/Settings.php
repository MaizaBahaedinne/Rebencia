<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Settings extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = model('SettingModel');
    }

    /**
     * Settings page
     */
    public function index()
    {
        $settings = $this->settingModel->findAll();
        
        // Group settings by category
        $groupedSettings = [];
        foreach ($settings as $setting) {
            $groupedSettings[$setting['category']][] = $setting;
        }

        $data = [
            'title' => 'Paramètres Système',
            'page_title' => 'Paramètres',
            'settings' => $groupedSettings
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Update settings
     */
    public function update()
    {
        $postData = $this->request->getPost();
        
        foreach ($postData as $key => $value) {
            if ($key !== 'csrf_test_name') { // Skip CSRF token
                $this->settingModel->set($key, $value);
            }
        }

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès');
    }
}
