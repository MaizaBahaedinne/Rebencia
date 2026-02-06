<?php

namespace App\Services;

use App\Models\PropertyExtendedModel;

/**
 * Service de calcul et d'analyse financière des propriétés
 * Calcule rendements, ROI, appréciation, cap rate, etc.
 */
class PropertyFinancialService
{
    protected PropertyExtendedModel $propertyModel;
    protected $db;

    public function __construct()
    {
        $this->propertyModel = model(PropertyExtendedModel::class);
        $this->db = \Config\Database::connect();
    }

    /**
     * Calculer le rendement brut (Gross Yield)
     * Formula: (Loyer annuel / Prix) * 100
     * @param float $annualRental Loyer annuel
     * @param float $propertyPrice Prix d'achat
     * @return float Rendement brut en %
     */
    public function calculateGrossYield($annualRental, $propertyPrice)
    {
        if ($propertyPrice <= 0) {
            return 0;
        }
        
        return ($annualRental / $propertyPrice) * 100;
    }

    /**
     * Calculer le rendement net (Net Yield)
     * Formula: ((Loyer annuel - Charges annuelles) / Prix) * 100
     * @param float $annualRental
     * @param float $propertyPrice
     * @param float $annualExpenses
     * @return float Rendement net en %
     */
    public function calculateNetYield($annualRental, $propertyPrice, $annualExpenses = 0)
    {
        if ($propertyPrice <= 0) {
            return 0;
        }
        
        $netIncome = $annualRental - $annualExpenses;
        return ($netIncome / $propertyPrice) * 100;
    }

    /**
     * Calculer le Cap Rate (Capitalization Rate)
     * Formula: (NOI / Valeur propriété) * 100
     * @param float $noi Net Operating Income (revenu net d'exploitation)
     * @param float $propertyValue Valeur/prix de la propriété
     * @return float Cap Rate en %
     */
    public function calculateCapRate($noi, $propertyValue)
    {
        if ($propertyValue <= 0) {
            return 0;
        }
        
        return ($noi / $propertyValue) * 100;
    }

    /**
     * Calculer le Prix par m²
     * @param float $propertyPrice
     * @param float $surface Surface totale en m²
     * @return float Prix par m² arrondi
     */
    public function calculatePricePerSqm($propertyPrice, $surface)
    {
        if ($surface <= 0) {
            return 0;
        }
        
        return round($propertyPrice / $surface, 2);
    }

    /**
     * Calculer le ROI (Return on Investment) annuel
     * Formula: ((Loyer net annuel / Investissement initial) - 1) * 100
     * @param float $annualNetIncome Revenu net annuel
     * @param float $initialInvestment Investissement initial
     * @return float ROI annuel en %
     */
    public function calculateAnnualROI($annualNetIncome, $initialInvestment)
    {
        if ($initialInvestment <= 0) {
            return 0;
        }
        
        return (($annualNetIncome / $initialInvestment) - 1) * 100;
    }

    /**
     * Calculer la période d'amortissement (Payback Period)
     * En combien de temps le loyer couvre-t-il l'investissement?
     * @param float $propertyPrice
     * @param float $monthlyRental
     * @param float $monthlyExpenses
     * @return float Années avant retour sur investissement
     */
    public function calculatePaybackPeriod($propertyPrice, $monthlyRental, $monthlyExpenses = 0)
    {
        $monthlyNetIncome = $monthlyRental - $monthlyExpenses;
        
        if ($monthlyNetIncome <= 0) {
            return 0; // Pas de retour possible
        }
        
        $months = $propertyPrice / $monthlyNetIncome;
        return round($months / 12, 1); // Convertir en années
    }

    /**
     * Calculer le Cash-on-Cash Return
     * Formula: ((Cashflow annuel avant emprunt / Cashflow initial) * 100)
     * @param float $monthlyNetIncome Revenu net mensuel
     * @param float $downPayment Apport initial
     * @return float Cash-on-Cash return en %
     */
    public function calculateCashOnCashReturn($monthlyNetIncome, $downPayment)
    {
        if ($downPayment <= 0) {
            return 0;
        }
        
        $annualCashflow = $monthlyNetIncome * 12;
        return ($annualCashflow / $downPayment) * 100;
    }

    /**
     * Analyser la rentabilité globale d'une propriété
     * @param int $propertyId
     * @return array Analyse complète avec tous les métriques
     */
    public function analyzeProperty($propertyId)
    {
        $property = $this->propertyModel->find($propertyId);
        $financial = $this->propertyModel->getFinancialData($propertyId);
        $costs = $this->propertyModel->getEstimatedCosts($propertyId);
        
        if (!$property) {
            return null;
        }
        
        // Récupérer valeurs par défaut si pas de données entrées
        $annualRental = ($property['rental_price'] ?? 0) * 12;
        $annualExpenses = ($costs['total_annual_costs'] ?? 0);
        $propertyPrice = $property['price'] ?? 0;
        $surface = $property['area_total'] ?? 1;
        
        return [
            'property_reference' => $property['reference'],
            'property_type' => $property['type'],
            'price' => $propertyPrice,
            'surface' => $surface,
            'monthly_rental' => $property['rental_price'] ?? 0,
            'annual_rental' => $annualRental,
            'annual_expenses' => $annualExpenses,
            'annual_net_income' => $annualRental - $annualExpenses,
            'metrics' => [
                'gross_yield' => round($this->calculateGrossYield($annualRental, $propertyPrice), 2),
                'net_yield' => round($this->calculateNetYield($annualRental, $propertyPrice, $annualExpenses), 2),
                'cap_rate' => round($this->calculateCapRate($annualRental - $annualExpenses, $propertyPrice), 2),
                'price_per_sqm' => $this->calculatePricePerSqm($propertyPrice, $surface),
                'payback_period_years' => $this->calculatePaybackPeriod($propertyPrice, $property['rental_price'] ?? 0, $annualExpenses / 12),
            ],
            'financial_data' => $financial,
        ];
    }

    /**
     * Créer un rapport comparatif entre deux propriétés
     * @param int $property1Id
     * @param int $property2Id
     * @return array
     */
    public function compareProperties($property1Id, $property2Id)
    {
        $analysis1 = $this->analyzeProperty($property1Id);
        $analysis2 = $this->analyzeProperty($property2Id);
        
        if (!$analysis1 || !$analysis2) {
            return null;
        }
        
        return [
            'property1' => $analysis1,
            'property2' => $analysis2,
            'comparison' => [
                'price_difference' => $analysis2['price'] - $analysis1['price'],
                'yield_difference' => $analysis2['metrics']['net_yield'] - $analysis1['metrics']['net_yield'],
                'price_per_sqm_difference' => $analysis2['metrics']['price_per_sqm'] - $analysis1['metrics']['price_per_sqm'],
                'better_value' => $analysis1['metrics']['net_yield'] > $analysis2['metrics']['net_yield'] ? $analysis1['property_reference'] : $analysis2['property_reference'],
            ]
        ];
    }

    /**
     * Calculer l'appréciation future estimée
     * @param float $currentPrice Prix actuel
     * @param float $appreciationRate Taux d'appréciation annuelle en %
     * @param int $years Nombre d'années
     * @return float Prix estimé après appréciation
     */
    public function calculateFutureValue($currentPrice, $appreciationRate, $years)
    {
        return $currentPrice * pow(1 + ($appreciationRate / 100), $years);
    }

    /**
     * Calculer le profit potentiel avec appréciation et loyers
     * @param int $propertyId
     * @param int $years Horizont d'investissement en années
     * @return array
     */
    public function calculateInvestmentProjection($propertyId, $years)
    {
        $property = $this->propertyModel->find($propertyId);
        $financial = $this->propertyModel->getFinancialData($propertyId);
        
        if (!$property || !$financial) {
            return null;
        }
        
        $currentPrice = $property['price'] ?? 0;
        $annualRental = ($property['rental_price'] ?? 0) * 12;
        $appreciationRate = $financial['appreciation_rate'] ?? 2; // Par défaut 2% annuel
        $annualExpenses = ($this->propertyModel->getEstimatedCosts($propertyId)['total_annual_costs'] ?? 0);
        
        $projections = [];
        $totalNetIncome = 0;
        
        for ($year = 1; $year <= $years; $year++) {
            $estimatedValue = $this->calculateFutureValue($currentPrice, $appreciationRate, $year);
            $totalNetIncome += ($annualRental - $annualExpenses);
            
            $projections[] = [
                'year' => $year,
                'estimated_property_value' => round($estimatedValue, 2),
                'appreciation_gain' => round($estimatedValue - $currentPrice, 2),
                'cumulative_rental_income' => round($totalNetIncome, 2),
                'total_profit' => round(($estimatedValue - $currentPrice) + $totalNetIncome, 2),
            ];
        }
        
        return [
            'property_reference' => $property['reference'],
            'investment_period_years' => $years,
            'initial_price' => $currentPrice,
            'appreciation_rate_annual' => $appreciationRate,
            'projections' => $projections,
        ];
    }

    /**
     * Lister les propriétés par performance financière
     * @param string $sortBy Colonne de tri (gross_yield, net_yield, cap_rate, roi)
     * @param int $limit Nombre de résultats
     * @return array
     */
    public function getRankedByPerformance($sortBy = 'net_yield', $limit = 10)
    {
        $validColumns = ['gross_yield', 'net_yield', 'cap_rate', 'roi_annual'];
        if (!in_array($sortBy, $validColumns)) {
            $sortBy = 'net_yield';
        }
        
        return $this->db->table('properties p')
            ->join('property_financial_data pfd', 'pfd.property_id = p.id', 'inner')
            ->select('p.reference, p.title, p.type, p.price, p.rental_price, p.area_total, 
                     pfd.gross_yield, pfd.net_yield, pfd.cap_rate, pfd.roi_annual')
            ->where('p.status', 'published')
            ->orderBy('pfd.' . $sortBy, 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
