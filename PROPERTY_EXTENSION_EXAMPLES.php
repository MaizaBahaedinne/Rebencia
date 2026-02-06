<?php

/**
 * EXEMPLES PRATIQUES D'UTILISATION
 * Extension Property Module - Cas d'usage réels
 */

// ============================================================================
// 1. DASHBOARD INVESTISSEUR - Vue complète d'une propriété
// ============================================================================

/**
 * Afficher un dashboard complet pour un investisseur
 * Toutes les données financières et d'analyse
 */
class InvestorDashboardExample
{
    public function showPropertyDashboard($propertyId)
    {
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        $financialService = service(\App\Services\PropertyFinancialService::class);
        $calcService = service(\App\Services\PropertyCalculationService::class);
        $configService = service(\App\Services\PropertyConfigService::class);
        
        // Récupérer tous les données
        $property = $extendedModel->getPropertyComplete($propertyId);
        
        if (!$property) {
            return "Propriété non trouvée";
        }
        
        // Analyses financières
        $analysis = $financialService->analyzeProperty($propertyId);
        $comparison = $calcService->compareWithMarketAverage($propertyId);
        $attraction = $calcService->calculatePropertyAttractionScore($propertyId);
        
        // Projections sur 10 ans
        $projection = $financialService->calculateInvestmentProjection($propertyId, 10);
        
        // Stats complètes pour dashboard
        $stats = $calcService->getCompleteDashboardStats($propertyId);
        
        return [
            'property' => $property,
            'financial_analysis' => $analysis,
            'market_comparison' => $comparison,
            'attraction_score' => $attraction,
            'investment_projection' => $projection,
            'dashboard_stats' => $stats,
        ];
    }
}

// ============================================================================
// 2. MOTEUR DE RECHERCHE AVANCÉ - Chercher avec critères multiples
// ============================================================================

/**
 * Recherche avancée par plusieurs critères combinés
 */
class AdvancedSearchExample
{
    public function advancedSearch($criteria)
    {
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        $calcService = service(\App\Services\PropertyCalculationService::class);
        
        $results = [];
        
        // 1. Chercher par localisation
        if (!empty($criteria['min_location_score'])) {
            $byLocation = $extendedModel->findByLocationScore(
                $criteria['min_location_score'],
                [
                    'proximity_to_schools' => $criteria['schools_score'] ?? 0,
                    'area_safety_score' => $criteria['safety_score'] ?? 0,
                ]
            );
            $results['by_location'] = $byLocation;
        }
        
        // 2. Chercher par options/équipements requis
        if (!empty($criteria['required_options'])) {
            $byOptions = $extendedModel->findByOptions(
                $criteria['required_options'],
                'all' // Toutes les options requises
            );
            $results['by_options'] = $byOptions;
        }
        
        // 3. Chercher par rendement (pour investisseurs)
        if (!empty($criteria['min_yield'])) {
            $byYield = $extendedModel->findByYield(
                $criteria['min_yield'],
                'net'
            );
            $results['by_yield'] = $byYield;
        }
        
        // 4. Filtrer et classer par attractivité
        if (!empty($results['by_location'])) {
            // Calculer score d'attractivité pour chaque propriété
            foreach ($results['by_location'] as &$prop) {
                $prop['attraction_score'] = $calcService->calculatePropertyAttractionScore($prop['id']);
            }
            
            // Trier par score d'attractivité (meilleur d'abord)
            usort($results['by_location'], function($a, $b) {
                return $b['attraction_score'] <=> $a['attraction_score'];
            });
        }
        
        return $results;
    }
}

// ============================================================================
// 3. GESTION PROPRIÉTAIRES - Liste avec coûts et rentabilité
// ============================================================================

/**
 * Afficher liste propriétés avec calculs rapides de rentabilité
 */
class OwnerPropertyListExample
{
    public function listOwnedProperties($ownerId)
    {
        $db = \Config\Database::connect();
        $calcService = service(\App\Services\PropertyCalculationService::class);
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        
        // Récupérer propriétés du propriétaire
        $properties = $db->table('properties')
            ->where('owner_id', $ownerId)
            ->get()
            ->getResultArray();
        
        $enriched = [];
        
        foreach ($properties as $prop) {
            $enriched[] = [
                'id' => $prop['id'],
                'reference' => $prop['reference'],
                'title' => $prop['title'],
                'type' => $prop['type'],
                'price' => $prop['price'],
                'rental_price' => $prop['rental_price'],
                
                // Calculs
                'monthly_costs' => $calcService->calculateMonthlyExpenses($prop['id']),
                'minimum_rental' => $calcService->calculateMinimumRental($prop['id'], 15), // 15% marge
                'estimated_yield' => $extendedModel->estimateNetYield($prop['id'], $prop['rental_price']),
                'attraction_score' => $calcService->calculatePropertyAttractionScore($prop['id']),
                'location_score' => $calcService->getLocationOverallScore($prop['id']),
            ];
        }
        
        // Trier par rendement
        usort($enriched, fn($a, $b) => $b['estimated_yield'] <=> $a['estimated_yield']);
        
        return $enriched;
    }
}

// ============================================================================
// 4. COMPARATIF - Comparer deux propriétés côte à côte
// ============================================================================

/**
 * Comparaison détaillée entre deux propriétés
 */
class PropertyComparisonExample
{
    public function compareProperties($propertyId1, $propertyId2)
    {
        $financialService = service(\App\Services\PropertyFinancialService::class);
        $calcService = service(\App\Services\PropertyCalculationService::class);
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        
        // Analyses financières des deux
        $comp = $financialService->compareProperties($propertyId1, $propertyId2);
        
        // Ajouter comparaisons supplémentaires
        $comp['location_comparison'] = [
            'property1_score' => $calcService->getLocationOverallScore($propertyId1),
            'property2_score' => $calcService->getLocationOverallScore($propertyId2),
        ];
        
        $comp['attraction_comparison'] = [
            'property1_score' => $calcService->calculatePropertyAttractionScore($propertyId1),
            'property2_score' => $calcService->calculatePropertyAttractionScore($propertyId2),
        ];
        
        $comp['expenses_comparison'] = [
            'property1_monthly' => $calcService->calculateMonthlyExpenses($propertyId1),
            'property2_monthly' => $calcService->calculateMonthlyExpenses($propertyId2),
        ];
        
        return $comp;
    }
}

// ============================================================================
// 5. PORTFOLIO ANALYSIS - Analyser portefeuille d'investissement
// ============================================================================

/**
 * Analyser un portefeuille de plusieurs propriétés
 */
class PortfolioAnalysisExample
{
    public function analyzePortfolio($propertyIds)
    {
        $financialService = service(\App\Services\PropertyFinancialService::class);
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        
        $portfolio = [
            'properties' => [],
            'totals' => [
                'total_investment' => 0,
                'total_monthly_rental' => 0,
                'total_monthly_expenses' => 0,
                'average_yield' => 0,
                'average_cap_rate' => 0,
            ]
        ];
        
        $yields = [];
        $capRates = [];
        
        foreach ($propertyIds as $id) {
            $analysis = $financialService->analyzeProperty($id);
            
            if ($analysis) {
                $portfolio['properties'][] = $analysis;
                
                // Accumuler totaux
                $portfolio['totals']['total_investment'] += $analysis['price'];
                $portfolio['totals']['total_monthly_rental'] += ($analysis['monthly_rental'] ?? 0);
                $portfolio['totals']['total_monthly_expenses'] += $analysis['annual_expenses'] / 12;
                
                if (!empty($analysis['metrics']['net_yield'])) {
                    $yields[] = $analysis['metrics']['net_yield'];
                }
                if (!empty($analysis['metrics']['cap_rate'])) {
                    $capRates[] = $analysis['metrics']['cap_rate'];
                }
            }
        }
        
        // Moyennes
        $portfolio['totals']['average_yield'] = !empty($yields) 
            ? round(array_sum($yields) / count($yields), 2)
            : 0;
        
        $portfolio['totals']['average_cap_rate'] = !empty($capRates)
            ? round(array_sum($capRates) / count($capRates), 2)
            : 0;
        
        // Portfolio yield global
        $portfolio['totals']['portfolio_yield'] = $portfolio['totals']['total_investment'] > 0
            ? round((($portfolio['totals']['total_monthly_rental'] * 12) / $portfolio['totals']['total_investment']) * 100, 2)
            : 0;
        
        return $portfolio;
    }
}

// ============================================================================
// 6. PLANNING D'INVESTISSEMENT - Projections sur plusieurs années
// ============================================================================

/**
 * Créer un plan d'investissement avec projections
 */
class InvestmentPlanningExample
{
    public function createInvestmentPlan($propertyId, $investmentHorizonYears = 10)
    {
        $financialService = service(\App\Services\PropertyFinancialService::class);
        
        // Projection détaillée
        $projection = $financialService->calculateInvestmentProjection($propertyId, $investmentHorizonYears);
        
        // Ajouter analyse année par année
        $yearly_analysis = [];
        
        foreach ($projection['projections'] as $year_data) {
            $year = $year_data['year'];
            $prev = $year > 1 ? $yearly_analysis[$year - 1] : null;
            
            $yearly_analysis[$year] = [
                'year' => $year,
                'property_value' => $year_data['estimated_property_value'],
                'year_appreciation' => $prev 
                    ? $year_data['estimated_property_value'] - $prev['property_value']
                    : $year_data['appreciation_gain'],
                'cumulative_rental_income' => $year_data['cumulative_rental_income'],
                'total_wealth_created' => $year_data['total_profit'],
                'roi_cumulative' => $this->calculateCumulativeROI(
                    $projection['initial_price'],
                    $year_data['total_profit']
                ),
            ];
        }
        
        return [
            'investment_plan' => $projection,
            'yearly_breakdown' => $yearly_analysis,
            'summary' => [
                'initial_investment' => $projection['initial_price'],
                'estimated_final_value' => $projection['projections'][count($projection['projections']) - 1]['estimated_property_value'],
                'total_wealth_created' => $projection['projections'][count($projection['projections']) - 1]['total_profit'],
            ]
        ];
    }
    
    private function calculateCumulativeROI($initialInvestment, $totalProfit)
    {
        return $initialInvestment > 0 
            ? round(($totalProfit / $initialInvestment) * 100, 2)
            : 0;
    }
}

// ============================================================================
// 7. VALIDATION DONNÉES - Vérifier complétude données avant publication
// ============================================================================

/**
 * Valider propriété avant publication dans listings
 */
class PropertyValidationExample
{
    public function validateBeforePublishing($propertyId)
    {
        $configService = service(\App\Services\PropertyConfigService::class);
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        
        $property = $extendedModel->find($propertyId);
        
        if (!$property) {
            return ['valid' => false, 'errors' => ['Propriété non trouvée']];
        }
        
        $validation = $configService->validatePropertyData($propertyId);
        $errors = $validation['errors'];
        
        // Validations supplémentaires
        if (empty($property['photos'])) {
            $errors[] = 'Au moins une photo est requise';
        }
        
        if ($property['price'] <= 0) {
            $errors[] = 'Le prix doit être supérieur à 0';
        }
        
        if (empty($property['description'])) {
            $errors[] = 'Une description est requise';
        }
        
        // Vérifier au moins un média avancé pour meilleure présentation
        $mediaCount = count($extendedModel->getMediaExtension($propertyId));
        if ($mediaCount == 0) {
            $errors[] = '[Recommandé] Ajoutez au moins un plan ou rendu 3D pour meilleure présentation';
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => [],
        ];
    }
}

// ============================================================================
// 8. RAPPORTS - Générer rapports pour clients/investisseurs
// ============================================================================

/**
 * Générer rapport d'analyse pour propriété
 */
class PropertyReportExample
{
    public function generateAnalysisReport($propertyId)
    {
        $financialService = service(\App\Services\PropertyFinancialService::class);
        $calcService = service(\App\Services\PropertyCalculationService::class);
        $extendedModel = model(\App\Models\PropertyExtendedModel::class);
        
        $property = $extendedModel->find($propertyId);
        
        $report = [
            'date_generated' => date('Y-m-d H:i:s'),
            'property' => [
                'reference' => $property['reference'],
                'title' => $property['title'],
                'type' => $property['type'],
                'location' => $property['address'],
            ],
            'physical_characteristics' => [
                'surface_total' => $property['area_total'],
                'surface_living' => $property['area_living'],
                'rooms' => $property['rooms'],
                'bedrooms' => $property['bedrooms'],
                'bathrooms' => $property['bathrooms'],
                'rooms_details' => $calcService->getRoomStats($propertyId),
            ],
            'location_analysis' => [
                'location_score' => $calcService->getLocationOverallScore($propertyId),
                'market_comparison' => $calcService->compareWithMarketAverage($propertyId),
                'proximity_scores' => [
                    'schools' => $calcService->getProximityScore($propertyId, 'schools'),
                    'transport' => $calcService->getProximityScore($propertyId, 'transport'),
                    'shopping' => $calcService->getProximityScore($propertyId, 'shopping'),
                ],
            ],
            'financial_analysis' => $financialService->analyzeProperty($propertyId),
            'investment_projection' => $financialService->calculateInvestmentProjection($propertyId, 5),
            'estimated_costs' => [
                'monthly' => $calcService->getMonthlyExpensesBreakdown($propertyId),
                'annual_total' => $calcService->calculateMonthlyExpenses($propertyId) * 12,
            ],
            'recommendations' => $this->getRecommendations($propertyId, $calcService),
        ];
        
        return $report;
    }
    
    private function getRecommendations($propertyId, $calcService)
    {
        $recommendations = [];
        
        $attraction = $calcService->calculatePropertyAttractionScore($propertyId);
        if ($attraction < 50) {
            $recommendations[] = 'Score d\'attractivité faible - considérer rénovations';
        }
        
        $location = $calcService->getLocationOverallScore($propertyId);
        if ($location < 40) {
            $recommendations[] = 'Localisation moins attractive - réviser pricing';
        }
        
        return $recommendations;
    }
}

// ============================================================================
// 9. CONFIGURATION TYPE PROPRIÉTÉ
// ============================================================================

/**
 * Configurer features pour un type de propriété
 */
class PropertyTypeConfigExample
{
    public function configurePropertyType($propertyType)
    {
        $configService = service(\App\Services\PropertyConfigService::class);
        
        // Exemple: Configure pour 'apartment'
        if ($propertyType === 'apartment') {
            $configService->saveConfig('apartment', [
                'enable_rooms' => true,              // Pièces requises
                'enable_location_scoring' => true,  // Localisation importante
                'enable_financial_data' => true,    // Rendement locatif
                'enable_estimated_costs' => true,   // Charges à payer
                'enable_orientation' => true,       // Exposition importante
                'enable_media_extension' => true,   // Plans d'étage
                'enable_options' => true,           // Équipements (AC, gym, etc.)
                
                'required_rooms' => true,           // Pièces OBLIGATOIRES
                'required_location_scoring' => false,
                'required_financial_data' => false,
                
                'max_rooms_allowed' => 10,
                'allowed_option_categories' => json_encode(['comfort', 'parking', 'amenities']),
                'show_roi_metrics' => true,
                'show_on_listings' => true,
            ]);
        }
        
        // Exemple: Configure pour 'land' (terrain)
        if ($propertyType === 'land') {
            $configService->saveConfig('land', [
                'enable_rooms' => false,            // Pas de pièces
                'enable_location_scoring' => true,  // Localisation critique
                'enable_financial_data' => true,    // Potentiel développement
                'enable_estimated_costs' => false,  // Pas applicable
                'enable_orientation' => false,      // Pas applicable
                'enable_media_extension' => true,   // Plans de zonage
                'enable_options' => false,          // Pas d'équipements
                
                'required_rooms' => false,
                'required_location_scoring' => true, // OBLIGATOIRE pour terrain
                'max_rooms_allowed' => 0,
            ]);
        }
    }
}

// ============================================================================
// 10. API ENDPOINTS EXAMPLES
// ============================================================================

/**
 * Exemple controller pour API
 */
class PropertyApiExample
{
    /**
     * GET /api/properties/{id}/financial-analysis
     */
    public function getFinancialAnalysis($propertyId)
    {
        $financialService = service(\App\Services\PropertyFinancialService::class);
        
        try {
            $analysis = $financialService->analyzeProperty($propertyId);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $analysis
            ]);
        } catch (\Throwable $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * GET /api/properties/{id}/ranking
     */
    public function getPropertyRanking($propertyId)
    {
        $calcService = service(\App\Services\PropertyCalculationService::class);
        
        $score = $calcService->calculatePropertyAttractionScore($propertyId);
        $location = $calcService->getLocationOverallScore($propertyId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'property_id' => $propertyId,
                'attraction_score' => $score,
                'location_score' => $location,
                'quality' => $this->getScoreQuality($score),
            ]
        ]);
    }
    
    /**
     * POST /api/properties/search
     */
    public function advancedSearch()
    {
        $criteria = $this->request->getJSON();
        $searchExample = new AdvancedSearchExample();
        
        $results = $searchExample->advancedSearch((array)$criteria);
        
        return $this->response->setJSON([
            'success' => true,
            'results' => $results,
            'count' => count($results)
        ]);
    }
    
    private function getScoreQuality($score)
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Average';
        return 'Poor';
    }
}

// Fin des exemples
