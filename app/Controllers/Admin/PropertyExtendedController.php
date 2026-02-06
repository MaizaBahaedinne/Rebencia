<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyExtendedModel;
use App\Services\PropertyConfigService;
use App\Services\PropertyCalculationService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PropertyExtendedController
 * 
 * Gère les données étendues des propriétés :
 * - Pièces et dimensions
 * - Options et équipements
 * - Scoring de localisation
 * - Données financières
 * - Coûts estimés
 * - Orientation et exposition
 * - Média avancé (plans, 3D, vidéos)
 */
class PropertyExtendedController extends BaseController
{
    protected $extendedModel;
    protected $configService;
    protected $calculationService;

    public function __construct()
    {
        $this->extendedModel = model(PropertyExtendedModel::class);
        $this->configService = service(\App\Services\PropertyConfigService::class);
        $this->calculationService = service(\App\Services\PropertyCalculationService::class);
    }

    /**
     * Sauvegarder les pièces d'une propriété
     * 
     * POST /admin/properties/{id}/rooms/save
     * 
     * Données attendues:
     * {
     *   "rooms": [
     *     {
     *       "name_fr": "Salon",
     *       "room_type": "living_room",
     *       "surface": 25.5,
     *       "width": 5.0,
     *       "length": 5.1,
     *       "height": 2.8,
     *       "has_window": 1,
     *       "window_type": "double",
     *       "orientation": "south"
     *     }
     *   ]
     * }
     */
    public function saveRooms($propertyId)
    {
        // Vérifier que la propriété existe
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        // Vérifier que la feature est activée pour ce type
        if (!$this->configService->isFeatureEnabled($property['type'], 'rooms')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La gestion des pièces n\'est pas activée pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $rooms = $this->request->getJSON(true)['rooms'] ?? [];

        if (empty($rooms)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucune pièce fournie'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Supprimer anciennes pièces
            $db->table('property_rooms')
               ->where('property_id', $propertyId)
               ->delete();

            // Insérer nouvelles pièces
            $sortOrder = 0;
            foreach ($rooms as $room) {
                $roomData = [
                    'property_id' => $propertyId,
                    'name_fr' => $room['name_fr'] ?? '',
                    'name_ar' => $room['name_ar'] ?? null,
                    'room_type' => $room['room_type'] ?? 'other',
                    'surface' => $room['surface'] ?? 0,
                    'width' => $room['width'] ?? null,
                    'length' => $room['length'] ?? null,
                    'height' => $room['height'] ?? null,
                    'has_window' => $room['has_window'] ?? 0,
                    'window_type' => $room['window_type'] ?? null,
                    'orientation' => $room['orientation'] ?? null,
                    'sort_order' => $sortOrder++,
                ];

                $db->table('property_rooms')->insert($roomData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la sauvegarde des pièces');
            }

            // Recalculer surface totale
            $totalSurface = $this->extendedModel->getRoomsTotalSurface($propertyId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pièces sauvegardées avec succès',
                'data' => [
                    'total_rooms' => count($rooms),
                    'total_surface' => $totalSurface
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveRooms: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Supprimer une pièce
     * 
     * DELETE /admin/properties/rooms/{roomId}
     */
    public function deleteRoom($roomId)
    {
        $db = \Config\Database::connect();
        
        try {
            $result = $db->table('property_rooms')
                        ->where('id', $roomId)
                        ->delete();

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pièce supprimée avec succès'
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pièce non trouvée'
            ])->setStatusCode(404);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Sauvegarder les options/équipements d'une propriété
     * 
     * POST /admin/properties/{id}/options/save
     * 
     * Données attendues:
     * {
     *   "options": [
     *     {"option_id": 1, "value": "Grande piscine"},
     *     {"option_id": 5, "value": null}
     *   ]
     * }
     */
    public function saveOptions($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        if (!$this->configService->isFeatureEnabled($property['type'], 'options')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La gestion des options n\'est pas activée pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $options = $this->request->getJSON(true)['options'] ?? [];
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Supprimer anciennes associations
            $db->table('property_option_values')
               ->where('property_id', $propertyId)
               ->delete();

            // Insérer nouvelles associations
            foreach ($options as $option) {
                if (!isset($option['option_id'])) {
                    continue;
                }

                $optionData = [
                    'property_id' => $propertyId,
                    'option_id' => $option['option_id'],
                    'value' => $option['value'] ?? null,
                ];

                $db->table('property_option_values')->insert($optionData);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la sauvegarde des options');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Options sauvegardées avec succès',
                'data' => [
                    'total_options' => count($options)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveOptions: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Sauvegarder le scoring de localisation
     * 
     * POST /admin/properties/{id}/location/save
     * 
     * Données attendues:
     * {
     *   "proximity_to_schools": 85,
     *   "proximity_to_transport": 90,
     *   "proximity_to_shopping": 75,
     *   ...
     *   "latitude": 36.8065,
     *   "longitude": 10.1815
     * }
     */
    public function saveLocationScoring($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        if (!$this->configService->isFeatureEnabled($property['type'], 'location_scoring')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Le scoring de localisation n\'est pas activé pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        try {
            // Calculer score global
            $overallScore = $this->calculationService->calculateLocationScore([
                'proximity_to_schools' => $data['proximity_to_schools'] ?? 0,
                'proximity_to_transport' => $data['proximity_to_transport'] ?? 0,
                'proximity_to_shopping' => $data['proximity_to_shopping'] ?? 0,
                'proximity_to_parks' => $data['proximity_to_parks'] ?? 0,
                'proximity_to_healthcare' => $data['proximity_to_healthcare'] ?? 0,
                'proximity_to_restaurants' => $data['proximity_to_restaurants'] ?? 0,
                'proximity_to_entertainment' => $data['proximity_to_entertainment'] ?? 0,
                'area_safety_score' => $data['area_safety_score'] ?? 0,
                'noise_level_score' => $data['noise_level_score'] ?? 0,
                'area_cleanliness_score' => $data['area_cleanliness_score'] ?? 0,
            ]);

            $locationData = [
                'property_id' => $propertyId,
                'proximity_to_schools' => $data['proximity_to_schools'] ?? 0,
                'proximity_to_transport' => $data['proximity_to_transport'] ?? 0,
                'proximity_to_shopping' => $data['proximity_to_shopping'] ?? 0,
                'proximity_to_parks' => $data['proximity_to_parks'] ?? 0,
                'proximity_to_healthcare' => $data['proximity_to_healthcare'] ?? 0,
                'proximity_to_restaurants' => $data['proximity_to_restaurants'] ?? 0,
                'proximity_to_entertainment' => $data['proximity_to_entertainment'] ?? 0,
                'area_safety_score' => $data['area_safety_score'] ?? 0,
                'noise_level_score' => $data['noise_level_score'] ?? 0,
                'area_cleanliness_score' => $data['area_cleanliness_score'] ?? 0,
                'overall_location_score' => $overallScore,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
            ];

            // Vérifier si existe déjà
            $existing = $db->table('property_location_scoring')
                          ->where('property_id', $propertyId)
                          ->get()
                          ->getRowArray();

            if ($existing) {
                $db->table('property_location_scoring')
                   ->where('property_id', $propertyId)
                   ->update($locationData);
            } else {
                $db->table('property_location_scoring')->insert($locationData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Scoring de localisation sauvegardé avec succès',
                'data' => [
                    'overall_score' => $overallScore
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveLocationScoring: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Sauvegarder les données financières
     * 
     * POST /admin/properties/{id}/financial/save
     */
    public function saveFinancialData($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        if (!$this->configService->isFeatureEnabled($property['type'], 'financial_data')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Les données financières ne sont pas activées pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        try {
            // Utiliser le service financier pour calculer les métriques
            $financialService = service(\App\Services\PropertyFinancialService::class);
            
            $marketPrice = $data['estimated_market_price'] ?? $property['price'];
            $rentalPrice = $data['estimated_rental_price'] ?? $property['rental_price'];
            
            // Calculer rendements et métriques
            $grossYield = $financialService->calculateGrossYield($marketPrice, $rentalPrice);
            $netYield = $financialService->calculateNetYield($propertyId, $rentalPrice);
            $capRate = $financialService->calculateCapRate($propertyId, $rentalPrice);
            $pricePerSqm = $financialService->calculatePricePerSqm($marketPrice, $property['surface'] ?? 0);

            $financialData = [
                'property_id' => $propertyId,
                'estimated_market_price' => $marketPrice,
                'estimated_rental_price' => $rentalPrice,
                'gross_yield' => $grossYield,
                'net_yield' => $netYield,
                'price_per_sqm' => $pricePerSqm,
                'cap_rate' => $capRate,
                'cash_on_cash_return' => $data['cash_on_cash_return'] ?? null,
                'roi_annual' => $data['roi_annual'] ?? null,
                'payback_period_years' => $data['payback_period_years'] ?? null,
                'appreciation_rate' => $data['appreciation_rate'] ?? null,
                'annual_appreciation_value' => $data['annual_appreciation_value'] ?? null,
                'debt_service_ratio' => $data['debt_service_ratio'] ?? null,
                'investor_notes' => $data['investor_notes'] ?? null,
                'last_valuation_date' => $data['last_valuation_date'] ?? date('Y-m-d'),
                'valuation_method' => $data['valuation_method'] ?? 'market_comparison',
            ];

            // Vérifier si existe déjà
            $existing = $db->table('property_financial_data')
                          ->where('property_id', $propertyId)
                          ->get()
                          ->getRowArray();

            if ($existing) {
                $db->table('property_financial_data')
                   ->where('property_id', $propertyId)
                   ->update($financialData);
            } else {
                $db->table('property_financial_data')->insert($financialData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Données financières sauvegardées avec succès',
                'data' => [
                    'gross_yield' => round($grossYield, 2),
                    'net_yield' => round($netYield, 2),
                    'cap_rate' => round($capRate, 2),
                    'price_per_sqm' => round($pricePerSqm, 2)
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveFinancialData: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Sauvegarder les coûts estimés
     * 
     * POST /admin/properties/{id}/costs/save
     */
    public function saveEstimatedCosts($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        if (!$this->configService->isFeatureEnabled($property['type'], 'estimated_costs')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Les coûts estimés ne sont pas activés pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        try {
            // Calculer totaux
            $totalMonthly = 
                ($data['monthly_syndic'] ?? 0) +
                ($data['monthly_electricity'] ?? 0) +
                ($data['monthly_water'] ?? 0) +
                ($data['monthly_gas'] ?? 0) +
                ($data['monthly_heating'] ?? 0) +
                ($data['monthly_hoa_fees'] ?? 0) +
                ($data['monthly_other'] ?? 0);

            $totalAnnual = 
                ($data['annual_property_tax'] ?? 0) +
                ($data['annual_income_tax'] ?? 0) +
                ($data['annual_insurance'] ?? 0) +
                ($data['annual_maintenance'] ?? 0);

            $costsData = [
                'property_id' => $propertyId,
                'monthly_syndic' => $data['monthly_syndic'] ?? 0,
                'monthly_electricity' => $data['monthly_electricity'] ?? 0,
                'monthly_water' => $data['monthly_water'] ?? 0,
                'monthly_gas' => $data['monthly_gas'] ?? 0,
                'monthly_heating' => $data['monthly_heating'] ?? 0,
                'monthly_hoa_fees' => $data['monthly_hoa_fees'] ?? 0,
                'monthly_other' => $data['monthly_other'] ?? 0,
                'total_monthly' => $totalMonthly,
                'annual_property_tax' => $data['annual_property_tax'] ?? 0,
                'annual_income_tax' => $data['annual_income_tax'] ?? 0,
                'annual_insurance' => $data['annual_insurance'] ?? 0,
                'annual_maintenance' => $data['annual_maintenance'] ?? 0,
                'total_annual' => $totalAnnual,
                'total_annual_with_monthly' => $totalAnnual + ($totalMonthly * 12),
            ];

            // Vérifier si existe déjà
            $existing = $db->table('property_estimated_costs')
                          ->where('property_id', $propertyId)
                          ->get()
                          ->getRowArray();

            if ($existing) {
                $db->table('property_estimated_costs')
                   ->where('property_id', $propertyId)
                   ->update($costsData);
            } else {
                $db->table('property_estimated_costs')->insert($costsData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Coûts estimés sauvegardés avec succès',
                'data' => [
                    'total_monthly' => $totalMonthly,
                    'total_annual' => $totalAnnual,
                    'total_annual_with_monthly' => $costsData['total_annual_with_monthly']
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveEstimatedCosts: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Sauvegarder l'orientation et l'exposition
     * 
     * POST /admin/properties/{id}/orientation/save
     */
    public function saveOrientation($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        if (!$this->configService->isFeatureEnabled($property['type'], 'orientation')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'L\'orientation n\'est pas activée pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $data = $this->request->getJSON(true);
        $db = \Config\Database::connect();

        try {
            $orientationData = [
                'property_id' => $propertyId,
                'primary_orientation' => $data['primary_orientation'] ?? null,
                'secondary_orientation' => $data['secondary_orientation'] ?? null,
                'sun_exposure' => $data['sun_exposure'] ?? null,
                'morning_sun' => $data['morning_sun'] ?? 0,
                'afternoon_sun' => $data['afternoon_sun'] ?? 0,
                'evening_sun' => $data['evening_sun'] ?? 0,
                'view_type' => $data['view_type'] ?? null,
                'view_quality' => $data['view_quality'] ?? null,
                'natural_light_level' => $data['natural_light_level'] ?? null,
                'balcony_orientation' => $data['balcony_orientation'] ?? null,
                'balcony_surface' => $data['balcony_surface'] ?? null,
                'wind_exposure' => $data['wind_exposure'] ?? null,
                'privacy_level' => $data['privacy_level'] ?? null,
            ];

            // Vérifier si existe déjà
            $existing = $db->table('property_orientation')
                          ->where('property_id', $propertyId)
                          ->get()
                          ->getRowArray();

            if ($existing) {
                $db->table('property_orientation')
                   ->where('property_id', $propertyId)
                   ->update($orientationData);
            } else {
                $db->table('property_orientation')->insert($orientationData);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Orientation sauvegardée avec succès'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveOrientation: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la sauvegarde: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Upload et sauvegarder média étendu (plans, 3D, vidéos)
     * 
     * POST /admin/properties/{id}/media/upload
     */
    public function saveMediaExtension($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        if (!$this->configService->isFeatureEnabled($property['type'], 'media_extension')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Les médias étendus ne sont pas activés pour ce type de propriété'
            ])->setStatusCode(403);
        }

        $file = $this->request->getFile('file');
        $fileType = $this->request->getPost('file_type');
        $description = $this->request->getPost('description');
        
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Fichier invalide'
            ])->setStatusCode(400);
        }

        $db = \Config\Database::connect();

        try {
            // Upload du fichier
            $uploadPath = WRITEPATH . 'uploads/properties/' . $propertyId . '/media/';
            
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);

            // Générer thumbnail pour images
            $thumbnail = null;
            if (in_array($file->getExtension(), ['jpg', 'jpeg', 'png'])) {
                // Logique génération thumbnail à implémenter
                $thumbnail = 'thumb_' . $newName;
            }

            $mediaData = [
                'property_id' => $propertyId,
                'file_type' => $fileType ?? 'document',
                'file_path' => '/uploads/properties/' . $propertyId . '/media/' . $newName,
                'file_name' => $file->getName(),
                'thumbnail' => $thumbnail,
                'description' => $description,
                'uploaded_by' => session()->get('user_id'),
                'is_published' => 1,
            ];

            $db->table('property_media_extension')->insert($mediaData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fichier uploadé avec succès',
                'data' => $mediaData
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur saveMediaExtension: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de l\'upload: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Supprimer un fichier média
     * 
     * DELETE /admin/properties/media/{mediaId}
     */
    public function deleteMediaFile($mediaId)
    {
        $db = \Config\Database::connect();
        
        try {
            // Récupérer info fichier
            $media = $db->table('property_media_extension')
                       ->where('id', $mediaId)
                       ->get()
                       ->getRowArray();

            if (!$media) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Fichier non trouvé'
                ])->setStatusCode(404);
            }

            // Supprimer fichier physique
            $filePath = FCPATH . $media['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Supprimer de BD
            $db->table('property_media_extension')
               ->where('id', $mediaId)
               ->delete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Fichier supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur deleteMediaFile: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
