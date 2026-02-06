<?php

namespace App\Services;

use App\Models\PropertyExtendedModel;

/**
 * Service de calculs complexes pour propriétés
 * Calcule surfaces, coûts, scores de localisation, projetions
 */
class PropertyCalculationService
{
    protected PropertyExtendedModel $propertyModel;

    public function __construct()
    {
        $this->propertyModel = model(PropertyExtendedModel::class);
    }

    /**
     * Calculer la surface habitable totale des pièces
     * @param int $propertyId
     * @return float Surface en m²
     */
    public function calculateRoomsTotalSurface($propertyId)
    {
        $rooms = $this->propertyModel->getRooms($propertyId);
        
        $total = 0;
        foreach ($rooms as $room) {
            if (isset($room['surface']) && $room['surface']) {
                $total += $room['surface'];
            }
        }
        
        return round($total, 2);
    }

    /**
     * Calculer surface par type de pièce
     * @param int $propertyId
     * @return array Surfaces par type
     */
    public function calculateSurfaceByRoomType($propertyId)
    {
        $rooms = $this->propertyModel->getRooms($propertyId);
        
        $surfaces = [];
        foreach ($rooms as $room) {
            $type = $room['room_type'] ?? 'other';
            if (!isset($surfaces[$type])) {
                $surfaces[$type] = 0;
            }
            $surfaces[$type] += $room['surface'] ?? 0;
        }
        
        return array_map(fn($s) => round($s, 2), $surfaces);
    }

    /**
     * Calculer nombre de pièces par type
     * @param int $propertyId
     * @return array
     */
    public function countRoomsByType($propertyId)
    {
        $rooms = $this->propertyModel->getRooms($propertyId);
        
        $counts = [];
        foreach ($rooms as $room) {
            $type = $room['room_type'] ?? 'other';
            $counts[$type] = ($counts[$type] ?? 0) + 1;
        }
        
        return $counts;
    }

    /**
     * Récupérer le score global de localisation
     * @param int $propertyId
     * @return float Score 0-100
     */
    public function getLocationOverallScore($propertyId)
    {
        $location = $this->propertyModel->getLocationScoring($propertyId);
        
        if (!$location || !$location['overall_location_score']) {
            // Calculer à partir des scores individuels
            return $this->calculateLocationScore($propertyId);
        }
        
        return $location['overall_location_score'];
    }

    /**
     * Calculer le score de localisation à partir composantes
     * @param int $propertyId
     * @return float
     */
    public function calculateLocationScore($propertyId)
    {
        $location = $this->propertyModel->getLocationScoring($propertyId);
        
        if (!$location) {
            return 0;
        }
        
        $components = [
            'proximity_to_schools' => $location['proximity_to_schools'] ?? 0,
            'proximity_to_transport' => $location['proximity_to_transport'] ?? 0,
            'proximity_to_shopping' => $location['proximity_to_shopping'] ?? 0,
            'proximity_to_parks' => $location['proximity_to_parks'] ?? 0,
            'proximity_to_healthcare' => $location['proximity_to_healthcare'] ?? 0,
            'area_safety_score' => $location['area_safety_score'] ?? 0,
        ];
        
        // Moyenne pondérée
        $weights = [
            'proximity_to_schools' => 0.20,
            'proximity_to_transport' => 0.20,
            'proximity_to_shopping' => 0.15,
            'proximity_to_parks' => 0.10,
            'proximity_to_healthcare' => 0.15,
            'area_safety_score' => 0.20,
        ];
        
        $score = 0;
        foreach ($components as $key => $value) {
            $score += $value * $weights[$key];
        }
        
        return round($score, 2);
    }

    /**
     * Obtenir le "best score" pour un type de proximité
     * @param int $propertyId
     * @param string $type schools, transport, shopping, parks, healthcare, restaurants, entertainment
     * @return int Score 0-100
     */
    public function getProximityScore($propertyId, $type)
    {
        $location = $this->propertyModel->getLocationScoring($propertyId);
        
        if (!$location) {
            return 0;
        }
        
        $field = 'proximity_to_' . $type;
        return $location[$field] ?? 0;
    }

    /**
     * Déterminer qualité d'un score de proximité
     * @param int $score
     * @return string excellent|good|average|poor
     */
    public function getScoreQuality($score)
    {
        if ($score >= 80) return 'excellent';
        if ($score >= 60) return 'good';
        if ($score >= 40) return 'average';
        return 'poor';
    }

    /**
     * Calculer les coûts mensuels totaux
     * @param int $propertyId
     * @return float
     */
    public function calculateMonthlyExpenses($propertyId)
    {
        return $this->propertyModel->getTotalMonthlyCosts($propertyId);
    }

    /**
     * Détailler les coûts mensuels
     * @param int $propertyId
     * @return array
     */
    public function getMonthlyExpensesBreakdown($propertyId)
    {
        $costs = $this->propertyModel->getEstimatedCosts($propertyId);
        
        if (!$costs) {
            return [];
        }
        
        return [
            'syndic' => $costs['syndic_monthly'] ?? 0,
            'electricity' => $costs['electricity_monthly'] ?? 0,
            'water' => $costs['water_monthly'] ?? 0,
            'gas' => $costs['gas_monthly'] ?? 0,
            'heating' => $costs['heating_monthly'] ?? 0,
            'property_tax_monthly' => (($costs['property_tax_annual'] ?? 0) / 12),
            'hoa_fees' => $costs['hoa_fees_monthly'] ?? 0,
            'insurance_monthly' => (($costs['insurance_annual'] ?? 0) / 12),
            'maintenance_monthly' => (($costs['maintenance_annual'] ?? 0) / 12),
            'other' => $costs['other_costs_monthly'] ?? 0,
        ];
    }

    /**
     * Obtenir le coût d'exploitation mensuel majoré d'une marge
     * @param int $propertyId
     * @param float $margin Marge en % (ex: 1.10 = +10%)
     * @return float
     */
    public function getExpensesWithMargin($propertyId, $margin = 1.0)
    {
        $total = $this->calculateMonthlyExpenses($propertyId);
        return round($total * $margin, 2);
    }

    /**
     * Calculer loyer minimum recommandé basé sur coûts
     * @param int $propertyId
     * @param float $targetMarginPercent Marge bénéficiaire ciblée en %
     * @return float
     */
    public function calculateMinimumRental($propertyId, $targetMarginPercent = 20)
    {
        $expenses = $this->calculateMonthlyExpenses($propertyId);
        $margin = 1 + ($targetMarginPercent / 100);
        return round($expenses * $margin, 2);
    }

    /**
     * Analyser rapport surface/coûts
     * @param int $propertyId
     * @return array
     */
    public function analyzeSurfaceCostRatio($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return null;
        }
        
        $surface = $property['area_total'] ?? 0;
        $expenses = $this->calculateMonthlyExpenses($propertyId);
        
        return [
            'total_surface' => $surface,
            'monthly_expenses' => $expenses,
            'cost_per_sqm_monthly' => $surface > 0 ? round($expenses / $surface, 2) : 0,
            'cost_per_sqm_annual' => $surface > 0 ? round(($expenses * 12) / $surface, 2) : 0,
        ];
    }

    /**
     * Obtenir nombre de pièces avec surface moyenne
     * @param int $propertyId
     * @return array
     */
    public function getRoomStats($propertyId)
    {
        $rooms = $this->propertyModel->getRooms($propertyId);
        
        if (empty($rooms)) {
            return [
                'total_rooms' => 0,
                'average_room_surface' => 0,
                'rooms_by_type' => [],
            ];
        }
        
        $totalSurface = 0;
        $typeCount = [];
        
        foreach ($rooms as $room) {
            $totalSurface += $room['surface'] ?? 0;
            $type = $room['room_type'] ?? 'other';
            $typeCount[$type] = ($typeCount[$type] ?? 0) + 1;
        }
        
        return [
            'total_rooms' => count($rooms),
            'average_room_surface' => round($totalSurface / count($rooms), 2),
            'total_surface' => round($totalSurface, 2),
            'rooms_by_type' => $typeCount,
        ];
    }

    /**
     * Comparer propriété vs moyenne du marché
     * @param int $propertyId
     * @param int|null $zoneId Zone pour comparaison (sinon zone du property)
     * @return array
     */
    public function compareWithMarketAverage($propertyId, $zoneId = null)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return null;
        }
        
        if (!$zoneId) {
            $zoneId = $property['zone_id'];
        }
        
        // Récupérer propriétés similaires
        $db = \Config\Database::connect();
        $similar = $db->table('properties')
            ->select('AVG(price) as avg_price, AVG(area_total) as avg_surface, AVG(rental_price) as avg_rental')
            ->where('zone_id', $zoneId)
            ->where('type', $property['type'])
            ->where('status', 'published')
            ->where('id !=', $propertyId)
            ->limit(50)
            ->get()
            ->getRow();
        
        if (!$similar) {
            return null;
        }
        
        return [
            'property_price' => $property['price'],
            'market_avg_price' => round($similar->avg_price ?? 0, 2),
            'price_difference_pct' => $similar->avg_price ? round((($property['price'] - $similar->avg_price) / $similar->avg_price) * 100, 2) : 0,
            
            'property_surface' => $property['area_total'],
            'market_avg_surface' => round($similar->avg_surface ?? 0, 2),
            
            'property_rental' => $property['rental_price'],
            'market_avg_rental' => round($similar->avg_rental ?? 0, 2),
            'rental_difference_pct' => $similar->avg_rental ? round((($property['rental_price'] - $similar->avg_rental) / $similar->avg_rental) * 100, 2) : 0,
            
            'status' => $this->determineMarketPosition($property['price'], $similar->avg_price),
        ];
    }

    /**
     * Déterminer position marché (overpriced, fair, underpriced)
     * @param float $price
     * @param float $avgPrice
     * @return string
     */
    protected function determineMarketPosition($price, $avgPrice)
    {
        if (!$avgPrice) return 'unknown';
        
        $diff = (($price - $avgPrice) / $avgPrice) * 100;
        
        if ($diff > 10) return 'overpriced';
        if ($diff < -10) return 'underpriced';
        return 'fair_value';
    }

    /**
     * Obtenir statistiques complètes pour tableaux de bord
     * @param int $propertyId
     * @return array
     */
    public function getCompleteDashboardStats($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return null;
        }
        
        return [
            'property_reference' => $property['reference'],
            'property_type' => $property['type'],
            'property_status' => $property['status'],
            
            'surface_stats' => $this->getRoomStats($propertyId),
            'location_score' => $this->getLocationOverallScore($propertyId),
            'monthly_expenses' => $this->calculateMonthlyExpenses($propertyId),
            'minimum_rental' => $this->calculateMinimumRental($propertyId, 20),
            
            'financial_summary' => [
                'purchase_price' => $property['price'],
                'current_rental' => $property['rental_price'],
                'estimated_yield' => $this->propertyModel->estimateNetYield($propertyId, $property['rental_price']),
            ],
        ];
    }

    /**
     * Évaluer attraction d'une propriété (0-100)
     * Basé sur: localisation, conditions, surface, coûts, rendement
     * @param int $propertyId
     * @return int Score 0-100
     */
    public function calculatePropertyAttractionScore($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property) {
            return 0;
        }
        
        $scores = [
            'location' => min(100, $this->getLocationOverallScore($propertyId)),
            'condition' => $this->mapConditionToScore($property['condition_state'] ?? 'average'),
            'value' => $this->evaluateValueScore($propertyId),
            'rental_potential' => $this->evaluateRentalPotential($propertyId),
        ];
        
        // Moyenne pondérée
        $weights = [
            'location' => 0.30,
            'condition' => 0.20,
            'value' => 0.25,
            'rental_potential' => 0.25,
        ];
        
        $total = 0;
        foreach ($scores as $key => $score) {
            $total += $score * ($weights[$key] ?? 0.25);
        }
        
        return (int)round($total);
    }

    /**
     * Converter état condition en score
     * @param string $condition
     * @return int
     */
    protected function mapConditionToScore($condition)
    {
        $map = [
            'excellent' => 95,
            'very_good' => 85,
            'good' => 75,
            'average' => 50,
            'fair' => 35,
            'poor' => 15,
        ];
        
        return $map[$condition] ?? 50;
    }

    /**
     * Évaluer ratio prix/surface
     * @param int $propertyId
     * @return int
     */
    protected function evaluateValueScore($propertyId)
    {
        $comparison = $this->compareWithMarketAverage($propertyId);
        
        if (!$comparison) {
            return 50;
        }
        
        $position = $comparison['status'];
        return match($position) {
            'underpriced' => 85,
            'fair_value' => 70,
            'overpriced' => 35,
            default => 50,
        };
    }

    /**
     * Évaluer potentiel locatif
     * @param int $propertyId
     * @return int
     */
    protected function evaluateRentalPotential($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        if (!$property || !$property['rental_price']) {
            return 0;
        }
        
        $yield = $this->propertyModel->estimateNetYield($propertyId, $property['rental_price']);
        
        if ($yield >= 5) return 90;
        if ($yield >= 4) return 75;
        if ($yield >= 3) return 60;
        if ($yield >= 2) return 40;
        return 20;
    }
}
