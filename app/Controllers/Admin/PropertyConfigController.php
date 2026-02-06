<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\PropertyConfigService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PropertyConfigController
 * 
 * Gère la configuration des features par type de propriété
 * - Activer/désactiver les features
 * - Définir les champs requis
 * - Gérer les catégories d'options autorisées
 */
class PropertyConfigController extends BaseController
{
    protected $configService;

    public function __construct()
    {
        $this->configService = service(PropertyConfigService::class);
    }

    /**
     * Liste des configurations par type de propriété
     * 
     * GET /admin/properties/config
     */
    public function index()
    {
        $propertyTypes = [
            'apartment' => 'Appartement',
            'villa' => 'Villa',
            'house' => 'Maison',
            'land' => 'Terrain',
            'office' => 'Bureau',
            'commercial' => 'Commercial',
            'warehouse' => 'Entrepôt',
            'other' => 'Autre'
        ];

        $configs = [];
        foreach ($propertyTypes as $type => $label) {
            $configs[$type] = [
                'label' => $label,
                'config' => $this->configService->getConfig($type)
            ];
        }

        return view('admin/properties/config/index', [
            'configs' => $configs,
            'propertyTypes' => $propertyTypes
        ]);
    }

    /**
     * Éditer la configuration d'un type
     * 
     * GET /admin/properties/config/{type}
     */
    public function edit($type)
    {
        $validTypes = ['apartment', 'villa', 'house', 'land', 'office', 'commercial', 'warehouse', 'other'];
        
        if (!in_array($type, $validTypes)) {
            return redirect()->to('/admin/properties/config')->with('error', 'Type de propriété invalide');
        }

        $config = $this->configService->getConfig($type);
        
        $availableFeatures = [
            'rooms' => 'Gestion des pièces et dimensions',
            'location_scoring' => 'Scoring de localisation',
            'financial_data' => 'Données financières et investissement',
            'estimated_costs' => 'Coûts estimés (charges)',
            'orientation' => 'Orientation et exposition',
            'media_extension' => 'Média avancé (plans, 3D, vidéos)',
            'options' => 'Options et équipements'
        ];

        $optionCategories = [
            'comfort' => 'Confort',
            'outdoor' => 'Extérieur',
            'parking' => 'Parking',
            'security' => 'Sécurité',
            'amenities' => 'Équipements',
            'other' => 'Autre'
        ];

        return view('admin/properties/config/edit', [
            'type' => $type,
            'config' => $config,
            'availableFeatures' => $availableFeatures,
            'optionCategories' => $optionCategories
        ]);
    }

    /**
     * Mettre à jour la configuration
     * 
     * POST /admin/properties/config/{type}
     */
    public function update($type)
    {
        $validTypes = ['apartment', 'villa', 'house', 'land', 'office', 'commercial', 'warehouse', 'other'];
        
        if (!in_array($type, $validTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Type de propriété invalide'
            ])->setStatusCode(400);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        $configData = [
            'enable_rooms' => isset($data['enable_rooms']) ? (int)$data['enable_rooms'] : 0,
            'enable_location_scoring' => isset($data['enable_location_scoring']) ? (int)$data['enable_location_scoring'] : 0,
            'enable_financial_data' => isset($data['enable_financial_data']) ? (int)$data['enable_financial_data'] : 0,
            'enable_estimated_costs' => isset($data['enable_estimated_costs']) ? (int)$data['enable_estimated_costs'] : 0,
            'enable_orientation' => isset($data['enable_orientation']) ? (int)$data['enable_orientation'] : 0,
            'enable_media_extension' => isset($data['enable_media_extension']) ? (int)$data['enable_media_extension'] : 0,
            'enable_options' => isset($data['enable_options']) ? (int)$data['enable_options'] : 0,
            
            'required_rooms' => isset($data['required_rooms']) ? (int)$data['required_rooms'] : 0,
            'required_location_scoring' => isset($data['required_location_scoring']) ? (int)$data['required_location_scoring'] : 0,
            'required_financial_data' => isset($data['required_financial_data']) ? (int)$data['required_financial_data'] : 0,
            'required_estimated_costs' => isset($data['required_estimated_costs']) ? (int)$data['required_estimated_costs'] : 0,
            'required_orientation' => isset($data['required_orientation']) ? (int)$data['required_orientation'] : 0,
            
            'max_rooms_allowed' => isset($data['max_rooms_allowed']) ? (int)$data['max_rooms_allowed'] : 50,
            'allowed_option_categories' => isset($data['allowed_option_categories']) ? $data['allowed_option_categories'] : null,
            'default_valuation_method' => $data['default_valuation_method'] ?? 'market_comparison',
            'show_roi_metrics' => isset($data['show_roi_metrics']) ? (int)$data['show_roi_metrics'] : 1,
            'show_on_listings' => isset($data['show_on_listings']) ? (int)$data['show_on_listings'] : 1,
        ];

        try {
            $result = $this->configService->saveConfig($type, $configData);

            if ($result) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Configuration mise à jour avec succès'
                    ]);
                }
                
                return redirect()->to('/admin/properties/config')
                    ->with('success', 'Configuration mise à jour avec succès');
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ])->setStatusCode(500);

        } catch (\Exception $e) {
            log_message('error', 'Erreur update config: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ])->setStatusCode(500);
            }
            
            return redirect()->back()
                ->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Toggle une feature via AJAX
     * 
     * POST /admin/properties/config/{type}/toggle/{feature}
     */
    public function toggleFeature($type, $feature)
    {
        $validTypes = ['apartment', 'villa', 'house', 'land', 'office', 'commercial', 'warehouse', 'other'];
        $validFeatures = ['rooms', 'location_scoring', 'financial_data', 'estimated_costs', 'orientation', 'media_extension', 'options'];
        
        if (!in_array($type, $validTypes) || !in_array($feature, $validFeatures)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Type ou feature invalide'
            ])->setStatusCode(400);
        }

        try {
            $result = $this->configService->toggleFeature($type, $feature);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Feature ' . ($result ? 'activée' : 'désactivée'),
                'enabled' => $result
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur toggleFeature: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Obtenir les sections visibles pour un type (API)
     * 
     * GET /admin/properties/config/{type}/sections
     */
    public function getSections($type)
    {
        try {
            $sections = $this->configService->getVisibleSections($type);

            return $this->response->setJSON([
                'success' => true,
                'sections' => $sections
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Valider une propriété (API)
     * 
     * POST /admin/properties/config/validate/{propertyId}
     */
    public function validateProperty($propertyId)
    {
        try {
            $validation = $this->configService->validatePropertyData($propertyId);

            return $this->response->setJSON([
                'success' => $validation['valid'],
                'valid' => $validation['valid'],
                'errors' => $validation['errors'],
                'missing_required' => $validation['missing_required']
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Réinitialiser la configuration aux valeurs par défaut
     * 
     * POST /admin/properties/config/{type}/reset
     */
    public function reset($type)
    {
        $validTypes = ['apartment', 'villa', 'house', 'land', 'office', 'commercial', 'warehouse', 'other'];
        
        if (!in_array($type, $validTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Type de propriété invalide'
            ])->setStatusCode(400);
        }

        try {
            $db = \Config\Database::connect();
            
            // Supprimer la config existante
            $db->table('property_admin_config')
               ->where('property_type', $type)
               ->delete();

            // Recharger la config par défaut
            $config = $this->configService->getConfig($type);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Configuration réinitialisée aux valeurs par défaut',
                'config' => $config
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur reset config: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
