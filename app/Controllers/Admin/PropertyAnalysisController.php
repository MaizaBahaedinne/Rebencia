<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyExtendedModel;
use App\Services\PropertyFinancialService;
use App\Services\PropertyCalculationService;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PropertyAnalysisController
 * 
 * Gère les analyses, dashboards et rapports pour les propriétés
 * - Dashboard investisseur
 * - Rapports financiers
 * - Comparaisons de propriétés
 * - Analyse de portefeuille
 */
class PropertyAnalysisController extends BaseController
{
    protected $extendedModel;
    protected $financialService;
    protected $calculationService;

    public function __construct()
    {
        $this->extendedModel = model(PropertyExtendedModel::class);
        $this->financialService = service(PropertyFinancialService::class);
        $this->calculationService = service(PropertyCalculationService::class);
    }

    /**
     * Dashboard d'analyse pour une propriété
     * 
     * GET /admin/properties/{id}/analysis
     */
    public function dashboard($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Propriété non trouvée');
        }

        // Récupérer toutes les données
        $propertyComplete = $this->extendedModel->getPropertyComplete($propertyId);
        
        // Analyse financière
        $financialAnalysis = $this->financialService->analyzeProperty($propertyId);
        
        // Stats complètes
        $stats = $this->calculationService->getCompleteDashboardStats($propertyId);
        
        // Score d'attractivité
        $attractionScore = $this->calculationService->calculatePropertyAttractionScore($propertyId);
        
        // Comparaison marché
        $marketComparison = $this->calculationService->compareWithMarketAverage($propertyId);

        return view('admin/properties/analysis/dashboard', [
            'property' => $propertyComplete,
            'financial' => $financialAnalysis,
            'stats' => $stats,
            'attractionScore' => $attractionScore,
            'marketComparison' => $marketComparison
        ]);
    }

    /**
     * Rapport financier détaillé
     * 
     * GET /admin/properties/{id}/financial-report
     */
    public function financialReport($propertyId)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        try {
            // Analyse complète
            $analysis = $this->financialService->analyzeProperty($propertyId);
            
            // Projection 10 ans
            $projection = $this->financialService->calculateInvestmentProjection($propertyId, 10);
            
            // Coûts détaillés
            $costsBreakdown = $this->calculationService->getMonthlyExpensesBreakdown($propertyId);
            
            // Loyer minimum recommandé
            $minRental = $this->calculationService->calculateMinimumRental($propertyId, 20);

            $report = [
                'property' => $property,
                'analysis' => $analysis,
                'projection' => $projection,
                'costs_breakdown' => $costsBreakdown,
                'recommended_min_rental' => $minRental,
                'generated_at' => date('Y-m-d H:i:s')
            ];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'report' => $report
                ]);
            }

            return view('admin/properties/analysis/financial_report', $report);

        } catch (\Exception $e) {
            log_message('error', 'Erreur financialReport: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Comparer deux propriétés
     * 
     * GET /admin/properties/compare/{id1}/{id2}
     */
    public function comparison($propertyId1, $propertyId2)
    {
        $propertyModel = model(\App\Models\PropertyModel::class);
        
        $property1 = $propertyModel->find($propertyId1);
        $property2 = $propertyModel->find($propertyId2);
        
        if (!$property1 || !$property2) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Une ou plusieurs propriétés non trouvées'
                ])->setStatusCode(404);
            }
            
            return redirect()->to('/admin/properties')->with('error', 'Propriétés non trouvées');
        }

        try {
            // Comparaison financière
            $comparison = $this->financialService->compareProperties($propertyId1, $propertyId2);
            
            // Données complètes
            $property1Complete = $this->extendedModel->getPropertyComplete($propertyId1);
            $property2Complete = $this->extendedModel->getPropertyComplete($propertyId2);
            
            // Scores
            $score1 = $this->calculationService->calculatePropertyAttractionScore($propertyId1);
            $score2 = $this->calculationService->calculatePropertyAttractionScore($propertyId2);
            
            // Location scores
            $locationScore1 = $this->calculationService->getLocationOverallScore($propertyId1);
            $locationScore2 = $this->calculationService->getLocationOverallScore($propertyId2);

            $comparisonData = [
                'property1' => $property1Complete,
                'property2' => $property2Complete,
                'financial_comparison' => $comparison,
                'scores' => [
                    'property1' => [
                        'attraction' => $score1,
                        'location' => $locationScore1
                    ],
                    'property2' => [
                        'attraction' => $score2,
                        'location' => $locationScore2
                    ]
                ]
            ];

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'comparison' => $comparisonData
                ]);
            }

            return view('admin/properties/analysis/comparison', $comparisonData);

        } catch (\Exception $e) {
            log_message('error', 'Erreur comparison: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ])->setStatusCode(500);
            }
            
            return redirect()->back()->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Analyse de portefeuille (toutes les propriétés)
     * 
     * GET /admin/properties/portfolio
     */
    public function portfolio()
    {
        try {
            $propertyModel = model(\App\Models\PropertyModel::class);
            $properties = $propertyModel->findAll();

            $portfolioStats = [
                'total_properties' => count($properties),
                'total_value' => 0,
                'total_rental_income' => 0,
                'average_yield' => 0,
                'properties' => []
            ];

            foreach ($properties as $property) {
                $financialData = $this->extendedModel->getFinancialData($property['id']);
                $attractionScore = $this->calculationService->calculatePropertyAttractionScore($property['id']);
                
                $propertyStats = [
                    'id' => $property['id'],
                    'reference' => $property['reference'],
                    'title' => $property['title'],
                    'type' => $property['type'],
                    'price' => $property['price'],
                    'rental_price' => $property['rental_price'],
                    'yield' => $financialData['net_yield'] ?? 0,
                    'attraction_score' => $attractionScore
                ];

                $portfolioStats['properties'][] = $propertyStats;
                $portfolioStats['total_value'] += $property['price'] ?? 0;
                $portfolioStats['total_rental_income'] += ($property['rental_price'] ?? 0) * 12;
            }

            // Calculer rendement moyen
            if ($portfolioStats['total_value'] > 0) {
                $portfolioStats['average_yield'] = ($portfolioStats['total_rental_income'] / $portfolioStats['total_value']) * 100;
            }

            // Trier par score d'attractivité
            usort($portfolioStats['properties'], function($a, $b) {
                return $b['attraction_score'] <=> $a['attraction_score'];
            });

            // Top performers
            $portfolioStats['top_performers'] = array_slice($portfolioStats['properties'], 0, 5);
            
            // Classement par rendement
            $rankedByYield = $this->financialService->getRankedByPerformance('net_yield', 10);
            $portfolioStats['best_yields'] = $rankedByYield;

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'portfolio' => $portfolioStats
                ]);
            }

            return view('admin/properties/analysis/portfolio', [
                'portfolio' => $portfolioStats
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur portfolio: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur: ' . $e->getMessage()
                ])->setStatusCode(500);
            }
            
            return redirect()->to('/admin/properties')->with('error', 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Exporter un rapport (PDF ou Excel)
     * 
     * POST /admin/properties/{id}/export-report
     */
    public function exportReport($propertyId)
    {
        $format = $this->request->getPost('format') ?? 'pdf'; // pdf ou excel
        
        $propertyModel = model(\App\Models\PropertyModel::class);
        $property = $propertyModel->find($propertyId);
        
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété non trouvée'
            ])->setStatusCode(404);
        }

        try {
            // Générer les données du rapport
            $reportData = [
                'property' => $this->extendedModel->getPropertyComplete($propertyId),
                'financial' => $this->financialService->analyzeProperty($propertyId),
                'projection' => $this->financialService->calculateInvestmentProjection($propertyId, 10),
                'stats' => $this->calculationService->getCompleteDashboardStats($propertyId),
                'attraction_score' => $this->calculationService->calculatePropertyAttractionScore($propertyId),
                'market_comparison' => $this->calculationService->compareWithMarketAverage($propertyId)
            ];

            if ($format === 'pdf') {
                // Générer PDF (nécessite une bibliothèque comme mPDF ou TCPDF)
                // Pour l'instant, retourner JSON avec les données
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Export PDF sera implémenté avec mPDF',
                    'data' => $reportData
                ]);
            } else {
                // Export Excel (nécessite PhpSpreadsheet)
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Export Excel sera implémenté avec PhpSpreadsheet',
                    'data' => $reportData
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Erreur exportReport: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Obtenir les métriques d'une propriété
     * 
     * GET /admin/properties/{id}/metrics
     */
    public function getMetrics($propertyId)
    {
        try {
            $stats = $this->calculationService->getCompleteDashboardStats($propertyId);

            return $this->response->setJSON([
                'success' => true,
                'metrics' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * API: Calculer projection d'investissement
     * 
     * GET /admin/properties/{id}/projection/{years}
     */
    public function getProjection($propertyId, $years = 10)
    {
        try {
            $projection = $this->financialService->calculateInvestmentProjection($propertyId, (int)$years);

            return $this->response->setJSON([
                'success' => true,
                'projection' => $projection
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
