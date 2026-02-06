<?php

namespace App\Services;

use App\Models\PropertyExtendedModel;

/**
 * Service de configuration et de visibilité des fonctionnalités de propriété
 * Contrôle quelle données sont affichées selon type de propriété et paramètres admin
 */
class PropertyConfigService
{
    protected PropertyExtendedModel $propertyModel;
    protected $config;
    protected $db;

    public function __construct()
    {
        $this->propertyModel = model(PropertyExtendedModel::class);
        $this->db = \Config\Database::connect();
        $this->loadDefaultConfig();
    }

    /**
     * Charger configuration par défaut pour tous les types de propriété
     */
    protected function loadDefaultConfig()
    {
        // Configuration par défaut (tout activé)
        $this->config = [
            'apartment' => $this->getDefaultTypeConfig(),
            'villa' => $this->getDefaultTypeConfig(),
            'house' => $this->getDefaultTypeConfig(),
            'land' => array_merge($this->getDefaultTypeConfig(), ['enable_rooms' => 0, 'enable_location_scoring' => 0]),
            'office' => $this->getDefaultTypeConfig(),
            'commercial' => array_merge($this->getDefaultTypeConfig(), ['enable_rooms' => 0]),
            'warehouse' => array_merge($this->getDefaultTypeConfig(), ['enable_rooms' => 0]),
            'other' => $this->getDefaultTypeConfig(),
        ];
    }

    /**
     * Obtenir configuration par défaut pour un type
     * @return array
     */
    protected function getDefaultTypeConfig()
    {
        return [
            'enable_rooms' => true,
            'enable_location_scoring' => true,
            'enable_financial_data' => true,
            'enable_estimated_costs' => true,
            'enable_orientation' => true,
            'enable_media_extension' => true,
            'enable_options' => true,
            'required_rooms' => false,
            'required_location_scoring' => false,
            'required_financial_data' => false,
            'required_estimated_costs' => false,
            'required_orientation' => false,
            'max_rooms_allowed' => 20,
            'show_roi_metrics' => true,
            'show_on_listings' => true,
        ];
    }

    /**
     * Obtenir la configuration pour un type de propriété
     * Combine configuration BD + défaut
     * @param string $propertyType
     * @return array
     */
    public function getConfig($propertyType)
    {
        // Charger depuis BD si existe
        $dbConfig = $this->propertyModel->getTypeConfig($propertyType);
        
        if ($dbConfig) {
            // Fusionner avec défaut
            return array_merge($this->getDefaultTypeConfig(), [
                'enable_rooms' => (bool)$dbConfig['enable_rooms'],
                'enable_location_scoring' => (bool)$dbConfig['enable_location_scoring'],
                'enable_financial_data' => (bool)$dbConfig['enable_financial_data'],
                'enable_estimated_costs' => (bool)$dbConfig['enable_estimated_costs'],
                'enable_orientation' => (bool)$dbConfig['enable_orientation'],
                'enable_media_extension' => (bool)$dbConfig['enable_media_extension'],
                'enable_options' => (bool)$dbConfig['enable_options'],
                'required_rooms' => (bool)$dbConfig['required_rooms'],
                'required_location_scoring' => (bool)$dbConfig['required_location_scoring'],
                'required_financial_data' => (bool)$dbConfig['required_financial_data'],
                'required_estimated_costs' => (bool)$dbConfig['required_estimated_costs'],
                'required_orientation' => (bool)$dbConfig['required_orientation'],
                'max_rooms_allowed' => $dbConfig['max_rooms_allowed'] ?? 20,
                'show_roi_metrics' => (bool)$dbConfig['show_roi_metrics'],
                'show_on_listings' => (bool)$dbConfig['show_on_listings'],
                'allowed_option_categories' => $this->parseJsonField($dbConfig['allowed_option_categories']),
            ]);
        }
        
        return $this->getDefaultTypeConfig();
    }

    /**
     * Parser un champ JSON en array
     * @param mixed $value
     * @return array
     */
    protected function parseJsonField($value)
    {
        if (is_null($value) || $value === '') {
            return [];
        }
        
        if (is_array($value)) {
            return $value;
        }
        
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Vérifier si une feature est activée pour un type
     * @param string $propertyType
     * @param string $feature
     * @return bool
     */
    public function isFeatureEnabled($propertyType, $feature)
    {
        $config = $this->getConfig($propertyType);
        return isset($config[$feature]) ? (bool)$config[$feature] : false;
    }

    /**
     * Vérifier si une feature est obligatoire pour un type
     * @param string $propertyType
     * @param string $feature
     * @return bool
     */
    public function isFeatureRequired($propertyType, $feature)
    {
        $requiredKey = 'required_' . str_replace('enable_', '', $feature);
        $config = $this->getConfig($propertyType);
        return isset($config[$requiredKey]) ? (bool)$config[$requiredKey] : false;
    }

    /**
     * Obtenir liste des catégories d'options autorisées pour un type
     * @param string $propertyType
     * @return array
     */
    public function getAllowedOptionCategories($propertyType)
    {
        $config = $this->getConfig($propertyType);
        
        $allowed = $config['allowed_option_categories'] ?? [];
        
        // Si vide, retourner toutes les catégories
        if (empty($allowed)) {
            return ['comfort', 'outdoor', 'parking', 'security', 'amenities', 'other'];
        }
        
        return $allowed;
    }

    /**
     * Obtenir les sections de propriété à afficher pour un type
     * Utile pour construire UI dynamiquement
     * @param string $propertyType
     * @return array Sections avec leur statut (enabled, required)
     */
    public function getVisibleSections($propertyType)
    {
        $config = $this->getConfig($propertyType);
        
        $sections = [];
        
        $features = [
            'rooms' => 'enable_rooms',
            'location_scoring' => 'enable_location_scoring',
            'financial_data' => 'enable_financial_data',
            'estimated_costs' => 'enable_estimated_costs',
            'orientation' => 'enable_orientation',
            'media_extension' => 'enable_media_extension',
            'options' => 'enable_options',
        ];
        
        foreach ($features as $name => $enableKey) {
            if ($config[$enableKey]) {
                $requiredKey = 'required_' . $name;
                $sections[$name] = [
                    'enabled' => true,
                    'required' => $config[$requiredKey] ?? false,
                    'label' => $this->getSectionLabel($name),
                    'icon' => $this->getSectionIcon($name),
                ];
            }
        }
        
        return $sections;
    }

    /**
     * Obtenir le label pour une section
     * @param string $section
     * @return string
     */
    protected function getSectionLabel($section)
    {
        $labels = [
            'rooms' => 'Dimensions des pièces',
            'location_scoring' => 'Localisation et proximités',
            'financial_data' => 'Données financières',
            'estimated_costs' => 'Coûts estimés',
            'orientation' => 'Orientation et exposition',
            'media_extension' => 'Multimédia avancé',
            'options' => 'Options et équipements',
        ];
        
        return $labels[$section] ?? ucfirst(str_replace('_', ' ', $section));
    }

    /**
     * Obtenir l'icône Font Awesome pour une section
     * @param string $section
     * @return string
     */
    protected function getSectionIcon($section)
    {
        $icons = [
            'rooms' => 'fa-door-open',
            'location_scoring' => 'fa-map-marker-alt',
            'financial_data' => 'fa-chart-line',
            'estimated_costs' => 'fa-calculator',
            'orientation' => 'fa-compass',
            'media_extension' => 'fa-images',
            'options' => 'fa-check-square',
        ];
        
        return $icons[$section] ?? 'fa-star';
    }

    /**
     * Obtenir liste des sections avec données pour une propriété
     * @param int $propertyId
     * @return array
     */
    public function getSectionsWithData($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return [];
        }
        
        $sections = $this->getVisibleSections($property['type']);
        
        // Ajouter information si données existent
        foreach ($sections as &$section) {
            $hasData = false;
            
            switch ($section['name'] ?? key($sections)) {
                case 'rooms':
                    $hasData = !empty($this->propertyModel->getRooms($propertyId));
                    break;
                case 'location_scoring':
                    $hasData = $this->propertyModel->getLocationScoring($propertyId) !== null;
                    break;
                case 'financial_data':
                    $hasData = $this->propertyModel->getFinancialData($propertyId) !== null;
                    break;
                case 'estimated_costs':
                    $hasData = $this->propertyModel->getEstimatedCosts($propertyId) !== null;
                    break;
                case 'orientation':
                    $hasData = $this->propertyModel->getOrientation($propertyId) !== null;
                    break;
                case 'media_extension':
                    $hasData = !empty($this->propertyModel->getMediaExtension($propertyId));
                    break;
                case 'options':
                    $hasData = !empty($this->propertyModel->getOptions($propertyId));
                    break;
            }
            
            $section['has_data'] = $hasData;
        }
        
        return $sections;
    }

    /**
     * Enregistrer configuration pour un type de propriété
     * @param string $propertyType
     * @param array $config
     * @return bool
     */
    public function saveConfig($propertyType, $config)
    {
        // Convertir arrays en JSON
        if (isset($config['allowed_option_categories']) && is_array($config['allowed_option_categories'])) {
            $config['allowed_option_categories'] = json_encode($config['allowed_option_categories']);
        }
        
        // Vérifier si existe déjà
        $existing = $this->propertyModel->getTypeConfig($propertyType);
        
        if ($existing) {
            return $this->db->table('property_admin_config')
                ->where('property_type', $propertyType)
                ->update($config);
        } else {
            $config['property_type'] = $propertyType;
            return $this->db->table('property_admin_config')
                ->insert($config);
        }
    }

    /**
     * Activer/désactiver une fonctionnalité pour un type
     * @param string $propertyType
     * @param string $feature Clé feature (enable_rooms, etc.)
     * @param bool $enabled
     * @return bool
     */
    public function toggleFeature($propertyType, $feature, $enabled = true)
    {
        $config = $this->getConfig($propertyType);
        $config[$feature] = $enabled;
        
        return $this->saveConfig($propertyType, $config);
    }

    /**
     * Obtenir toutes les options disponibles
     * Groupées par catégorie
     * @param string|null $propertyType Filtrer pour type spécifique
     * @return array
     */
    public function getAvailableOptions($propertyType = null)
    {
        $builder = $this->db->table('property_options')
            ->where('is_active', 1)
            ->orderBy('category')
            ->orderBy('sort_order');
        
        $options = $builder->get()->getResultArray();
        
        // Regrouper par catégorie
        $grouped = [];
        foreach ($options as $option) {
            $category = $option['category'];
            if (!isset($grouped[$category])) {
                $grouped[$category] = [];
            }
            $grouped[$category][] = $option;
        }
        
        // Filtrer par types autorisés si spécifié
        if ($propertyType) {
            $allowedCategories = $this->getAllowedOptionCategories($propertyType);
            $grouped = array_filter($grouped, function($cat) use ($allowedCategories) {
                return in_array($cat, $allowedCategories);
            }, ARRAY_KEY_FILTER_FLAG);
        }
        
        return $grouped;
    }

    /**
     * Valider données d'une propriété selon sa configuration
     * @param int $propertyId
     * @return array ['valid' => bool, 'errors' => array]
     */
    public function validatePropertyData($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return ['valid' => false, 'errors' => ['Propriété introuvable']];
        }
        
        $config = $this->getConfig($property['type']);
        $errors = [];
        
        // Vérifier données obligatoires
        if ($config['required_rooms'] && empty($this->propertyModel->getRooms($propertyId))) {
            $errors[] = 'Les dimensions des pièces sont obligatoires pour ce type';
        }
        
        if ($config['required_location_scoring'] && $this->propertyModel->getLocationScoring($propertyId) === null) {
            $errors[] = 'L\'évaluation de localisation est obligatoire';
        }
        
        if ($config['required_financial_data'] && $this->propertyModel->getFinancialData($propertyId) === null) {
            $errors[] = 'Les données financières sont obligatoires';
        }
        
        if ($config['required_estimated_costs'] && $this->propertyModel->getEstimatedCosts($propertyId) === null) {
            $errors[] = 'Les coûts estimés sont obligatoires';
        }
        
        if ($config['required_orientation'] && $this->propertyModel->getOrientation($propertyId) === null) {
            $errors[] = 'L\'orientation et exposition sont obligatoires';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }
}
