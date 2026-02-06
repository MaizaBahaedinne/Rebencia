<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyExtendedModel;
use App\Services\PropertyFinancialService;
use App\Services\PropertyConfigService;
use App\Services\PropertyCalculationService;

class PropertyTest extends BaseController
{
    /**
     * Page de test du module Property Extension
     * URL: /admin/properties/test
     */
    public function index()
    {
        $results = [];
        
        // Test 1: Model chargement
        try {
            $extended = model(PropertyExtendedModel::class);
            $results['model'] = '✅ PropertyExtendedModel chargé';
        } catch (\Exception $e) {
            $results['model'] = '❌ Erreur: ' . $e->getMessage();
        }
        
        // Test 2: Services
        try {
            $financial = service(PropertyFinancialService::class);
            $config = service(PropertyConfigService::class);
            $calc = service(PropertyCalculationService::class);
            $results['services'] = '✅ 3 Services chargés';
        } catch (\Exception $e) {
            $results['services'] = '❌ Erreur: ' . $e->getMessage();
        }
        
        // Test 3: Tables BD
        $db = \Config\Database::connect();
        $tables = [
            'property_options',
            'property_option_values',
            'property_rooms',
            'property_location_scoring',
            'property_financial_data',
            'property_estimated_costs',
            'property_media_extension',
            'property_orientation',
            'property_admin_config'
        ];
        
        $missingTables = [];
        foreach ($tables as $table) {
            if (!$db->tableExists($table)) {
                $missingTables[] = $table;
            }
        }
        
        if (empty($missingTables)) {
            $results['tables'] = '✅ 9 tables créées';
        } else {
            $results['tables'] = '❌ Tables manquantes: ' . implode(', ', $missingTables);
        }
        
        // Test 4: Configuration par défaut
        try {
            $config = service(PropertyConfigService::class);
            $apartmentConfig = $config->getTypeConfiguration('apartment');
            $results['config'] = '✅ Configuration chargée (apartment: ' . count($apartmentConfig) . ' options)';
        } catch (\Exception $e) {
            $results['config'] = '❌ Erreur config: ' . $e->getMessage();
        }
        
        // Test 5: Calculs financiers
        try {
            $financial = service(PropertyFinancialService::class);
            $grossYield = $financial->calculateGrossYield(200000, 1000);
            $results['calculations'] = '✅ Calculs OK (Rendement brut: ' . round($grossYield, 2) . '%)';
        } catch (\Exception $e) {
            $results['calculations'] = '❌ Erreur calculs: ' . $e->getMessage();
        }
        
        // Test 6: Options disponibles
        try {
            $extended = model(PropertyExtendedModel::class);
            $options = $db->table('property_options')->countAllResults();
            $results['options'] = $options > 0 
                ? "✅ {$options} options disponibles" 
                : '⚠️ Aucune option (insérer données manuellement)';
        } catch (\Exception $e) {
            $results['options'] = '❌ Erreur options: ' . $e->getMessage();
        }
        
        // Test 7: Language files
        $langFiles = [
            APPPATH . 'Language/fr/PropertyExtended.php',
            APPPATH . 'Language/ar/PropertyExtended.php',
            APPPATH . 'Language/en/PropertyExtended.php'
        ];
        
        $existingLang = array_filter($langFiles, 'file_exists');
        $results['i18n'] = '✅ ' . count($existingLang) . '/3 fichiers langue';
        
        // Test 8: Views
        $viewFiles = [
            APPPATH . 'Views/admin/properties/extended_tabs.php',
            APPPATH . 'Views/admin/properties/config/index.php',
            APPPATH . 'Views/admin/properties/config/edit.php',
            APPPATH . 'Views/admin/properties/analysis/dashboard.php'
        ];
        
        $existingViews = array_filter($viewFiles, 'file_exists');
        $results['views'] = '✅ ' . count($existingViews) . '/4 vues créées';
        
        // Test 9: JavaScript
        $jsFile = FCPATH . 'assets/js/property-extended.js';
        $results['javascript'] = file_exists($jsFile) 
            ? '✅ JavaScript présent (' . round(filesize($jsFile)/1024, 1) . ' KB)' 
            : '❌ property-extended.js manquant';
        
        // Test 10: Filter RBAC
        $filterFile = APPPATH . 'Filters/PropertyExtendedPermissionFilter.php';
        $results['rbac'] = file_exists($filterFile) 
            ? '✅ RBAC Filter présent' 
            : '❌ Filter RBAC manquant';
        
        return view('admin/properties/test_results', [
            'results' => $results,
            'title' => 'Test Module Property Extension'
        ]);
    }
    
    /**
     * Test avec une vraie propriété
     * URL: /admin/properties/test/property/{id}
     */
    public function testProperty($propertyId)
    {
        $extended = model(PropertyExtendedModel::class);
        $financial = service(PropertyFinancialService::class);
        $calc = service(PropertyCalculationService::class);
        
        $results = [];
        
        // Charger propriété complète
        try {
            $property = $extended->getPropertyComplete($propertyId);
            $results['property_loaded'] = $property 
                ? '✅ Propriété #' . $propertyId . ' chargée' 
                : '❌ Propriété introuvable';
                
            if ($property) {
                // Test données étendues
                $results['options'] = !empty($property['options']) 
                    ? '✅ ' . count($property['options']) . ' options'
                    : '⚠️ Aucune option';
                    
                $results['rooms'] = !empty($property['rooms']) 
                    ? '✅ ' . count($property['rooms']) . ' pièces'
                    : '⚠️ Aucune pièce';
                    
                $results['location'] = !empty($property['location_scoring']) 
                    ? '✅ Score localisation: ' . ($property['location_scoring']['overall_location_score'] ?? 'N/A')
                    : '⚠️ Pas de scoring localisation';
                    
                $results['financial'] = !empty($property['financial_data']) 
                    ? '✅ Données financières présentes'
                    : '⚠️ Pas de données financières';
                
                // Score attractivité
                try {
                    $score = $calc->calculatePropertyAttractionScore($propertyId);
                    $results['attraction_score'] = '✅ Score attractivité: ' . $score . '/100';
                } catch (\Exception $e) {
                    $results['attraction_score'] = '⚠️ Score non calculable: ' . $e->getMessage();
                }
                
                // Analyse financière
                if (!empty($property['financial_data'])) {
                    try {
                        $analysis = $financial->analyzeProperty($propertyId);
                        $results['financial_analysis'] = '✅ Analyse OK - Rendement net: ' . 
                            round($analysis['metrics']['net_yield'] ?? 0, 2) . '%';
                    } catch (\Exception $e) {
                        $results['financial_analysis'] = '⚠️ Analyse impossible: ' . $e->getMessage();
                    }
                }
            }
        } catch (\Exception $e) {
            $results['property_loaded'] = '❌ Erreur: ' . $e->getMessage();
        }
        
        return view('admin/properties/test_results', [
            'results' => $results,
            'title' => 'Test Propriété #' . $propertyId,
            'property' => $property ?? null
        ]);
    }
}
