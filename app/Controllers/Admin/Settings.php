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
     * Settings page (redirect to general)
     */
    public function index()
    {
        return redirect()->to(base_url('admin/settings/general'));
    }

    /**
     * General settings
     */
    public function general()
    {
        $settings = $this->settingModel->where('category', 'general')->findAll();
        
        $data = [
            'title' => 'Paramètres Généraux',
            'page_title' => 'Paramètres Généraux',
            'settings' => $this->formatSettings($settings)
        ];

        return view('admin/settings/general', $data);
    }

    /**
     * Email configuration
     */
    public function email()
    {
        $settings = $this->settingModel->where('category', 'email')->findAll();
        
        $data = [
            'title' => 'Configuration Email',
            'page_title' => 'Configuration Email',
            'settings' => $this->formatSettings($settings)
        ];

        return view('admin/settings/email', $data);
    }

    /**
     * SMS configuration
     */
    public function sms()
    {
        $settings = $this->settingModel->where('category', 'sms')->findAll();
        
        $data = [
            'title' => 'Configuration SMS',
            'page_title' => 'Configuration SMS',
            'settings' => $this->formatSettings($settings)
        ];

        return view('admin/settings/sms', $data);
    }

    /**
     * Payment methods
     */
    public function payment()
    {
        $settings = $this->settingModel->where('category', 'payment')->findAll();
        
        $data = [
            'title' => 'Moyens de Paiement',
            'page_title' => 'Moyens de Paiement',
            'settings' => $this->formatSettings($settings)
        ];

        return view('admin/settings/payment', $data);
    }

    /**
     * Notification settings
     */
    public function notifications()
    {
        $settings = $this->settingModel->where('category', 'notifications')->findAll();
        
        $data = [
            'title' => 'Paramètres des Notifications',
            'page_title' => 'Notifications',
            'settings' => $this->formatSettings($settings)
        ];

        return view('admin/settings/notifications', $data);
    }
    
    /**
     * Footer & Social settings
     */
    public function footer()
    {
        $siteSettingModel = model('SiteSettingModel');
        $settings = $siteSettingModel->getAllSettings();
        
        $data = [
            'title' => 'Paramètres Footer & Réseaux Sociaux',
            'page_title' => 'Paramètres Footer & Réseaux Sociaux',
            'settings' => $settings
        ];

        return view('admin/settings/footer', $data);
    }
    
    /**
     * Update footer settings
     */
    public function updateFooter()
    {
        $siteSettingModel = model('SiteSettingModel');
        $postData = $this->request->getPost();
        
        foreach ($postData as $key => $value) {
            if ($key !== 'csrf_test_name') {
                $siteSettingModel->setSetting($key, $value);
            }
        }

        return redirect()->to(base_url('admin/settings/footer'))->with('success', 'Paramètres footer mis à jour avec succès');
    }

    /**
     * Update settings
     */
    public function update()
    {
        $postData = $this->request->getPost();
        $category = $this->request->getPost('_category');
        
        foreach ($postData as $key => $value) {
            if ($key !== 'csrf_test_name' && $key !== '_category') {
                $this->settingModel->setSetting($key, $value);
            }
        }

        $redirectMap = [
            'general' => 'admin/settings/general',
            'email' => 'admin/settings/email',
            'sms' => 'admin/settings/sms',
            'payment' => 'admin/settings/payment',
            'notifications' => 'admin/settings/notifications',
        ];

        $redirectUrl = $redirectMap[$category] ?? 'admin/settings/general';

        return redirect()->to(base_url($redirectUrl))->with('success', 'Paramètres mis à jour avec succès');
    }

    /**
     * Format settings array
     */
    private function formatSettings($settings)
    {
        $formatted = [];
        if (is_array($settings)) {
            foreach ($settings as $setting) {
                if (isset($setting['key']) && isset($setting['value'])) {
                    $formatted[$setting['key']] = $setting['value'];
                }
            }
        }
        return $formatted;
    }
}
