<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Extension PropertyModel avec relations et méthodes pour données avancées immobilières
 * Backward compatible: n'ajoute que des méthodes sans modifier le modèle existant
 */
class PropertyExtendedModel extends Model
{
    protected $table = 'properties';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;

    /**
     * Récupérer les options/équipements d'une propriété
     * @param int $propertyId
     * @return array
     */
    public function getOptions($propertyId)
    {
        return $this->db->table('property_option_values pov')
            ->select('po.id, po.code, po.name_fr, po.name_ar, po.name_en, po.icon, po.category, pov.value')
            ->join('property_options po', 'po.id = pov.option_id', 'inner')
            ->where('pov.property_id', $propertyId)
            ->where('po.is_active', 1)
            ->orderBy('po.sort_order')
            ->orderBy('po.id')
            ->get()
            ->getResultArray();
    }

    /**
     * Récupérer les pièces/dimensions d'une propriété
     * @param int $propertyId
     * @return array
     */
    public function getRooms($propertyId)
    {
        return $this->db->table('property_rooms')
            ->where('property_id', $propertyId)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->getResultArray();
    }

    /**
     * Calculer la surface totale des pièces
     * @param int $propertyId
     * @return float
     */
    public function getRoomsTotalSurface($propertyId)
    {
        $result = $this->db->table('property_rooms')
            ->selectSum('surface')
            ->where('property_id', $propertyId)
            ->get()
            ->getRow();
        
        return $result->surface ?? 0;
    }

    /**
     * Récupérer les données de localisation/scoring
     * @param int $propertyId
     * @return array|null
     */
    public function getLocationScoring($propertyId)
    {
        return $this->db->table('property_location_scoring')
            ->where('property_id', $propertyId)
            ->get()
            ->getRowArray();
    }

    /**
     * Récupérer les données financières/investissement
     * @param int $propertyId
     * @return array|null
     */
    public function getFinancialData($propertyId)
    {
        return $this->db->table('property_financial_data')
            ->where('property_id', $propertyId)
            ->get()
            ->getRowArray();
    }

    /**
     * Récupérer les coûts estimés
     * @param int $propertyId
     * @return array|null
     */
    public function getEstimatedCosts($propertyId)
    {
        return $this->db->table('property_estimated_costs')
            ->where('property_id', $propertyId)
            ->get()
            ->getRowArray();
    }

    /**
     * Récupérer les données d'orientation et exposition
     * @param int $propertyId
     * @return array|null
     */
    public function getOrientation($propertyId)
    {
        return $this->db->table('property_orientation')
            ->where('property_id', $propertyId)
            ->get()
            ->getRowArray();
    }

    /**
     * Récupérer les fichiers multimédia avancés (plans, rendus 3D, etc.)
     * @param int $propertyId
     * @param string|null $fileType Filtrer par type (floor_plan, 3d_render, video_tour, etc.)
     * @return array
     */
    public function getMediaExtension($propertyId, $fileType = null)
    {
        $builder = $this->db->table('property_media_extension')
            ->where('property_id', $propertyId)
            ->where('is_published', 1);
        
        if ($fileType !== null) {
            $builder->where('file_type', $fileType);
        }
        
        return $builder->orderBy('is_primary', 'DESC')
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();
    }

    /**
     * Récupérer le plan d'étage principal
     * @param int $propertyId
     * @return array|null
     */
    public function getPrimaryFloorPlan($propertyId)
    {
        return $this->db->table('property_media_extension')
            ->where('property_id', $propertyId)
            ->where('file_type', 'floor_plan')
            ->where('is_primary', 1)
            ->where('is_published', 1)
            ->get()
            ->getRowArray();
    }

    /**
     * Récupérer tous les plans d'étage d'une propriété
     * @param int $propertyId
     * @return array
     */
    public function getFloorPlans($propertyId)
    {
        return $this->db->table('property_media_extension')
            ->where('property_id', $propertyId)
            ->where('file_type', 'floor_plan')
            ->where('is_published', 1)
            ->orderBy('floor_number', 'ASC')
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();
    }

    /**
     * Récupérer tous les rendus 3D d'une propriété
     * @param int $propertyId
     * @return array
     */
    public function get3DRenders($propertyId)
    {
        return $this->db->table('property_media_extension')
            ->where('property_id', $propertyId)
            ->where('file_type', '3d_render')
            ->where('is_published', 1)
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();
    }

    /**
     * Récupérer les visites virtuelles/vidéos
     * @param int $propertyId
     * @return array
     */
    public function getVideoTours($propertyId)
    {
        return $this->db->table('property_media_extension')
            ->where('property_id', $propertyId)
            ->where('file_type', 'video_tour')
            ->where('is_published', 1)
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();
    }

    /**
     * Récupérer la configuration pour un type de propriété
     * @param string $propertyType Type de propriété (apartment, villa, house, etc.)
     * @return array|null
     */
    public function getTypeConfig($propertyType)
    {
        return $this->db->table('property_admin_config')
            ->where('property_type', $propertyType)
            ->first();
    }

    /**
     * Vérifier si une fonctionnalité est activée pour un type de propriété
     * @param string $propertyType
     * @param string $feature Clé de fonctionnalité (enable_rooms, enable_location_scoring, etc.)
     * @return bool
     */
    public function isFeatureEnabled($propertyType, $feature)
    {
        $config = $this->getTypeConfig($propertyType);
        if (!$config) {
            // Pas de config trouvée, activer par défaut
            return true;
        }
        
        return isset($config[$feature]) ? (bool)$config[$feature] : true;
    }

    /**
     * Récupérer les propriétés complètes avec toutes les données avancées
     * @param int $propertyId
     * @return array|null
     */
    public function getPropertyComplete($propertyId)
    {
        $property = $this->find($propertyId);
        
        if (!$property) {
            return null;
        }
        
        // Ajouter les données avancées
        $property['options'] = $this->getOptions($propertyId);
        $property['rooms'] = $this->getRooms($propertyId);
        $property['rooms_total_surface'] = $this->getRoomsTotalSurface($propertyId);
        $property['location_scoring'] = $this->getLocationScoring($propertyId);
        $property['financial_data'] = $this->getFinancialData($propertyId);
        $property['estimated_costs'] = $this->getEstimatedCosts($propertyId);
        $property['orientation'] = $this->getOrientation($propertyId);
        $property['media_extension'] = $this->getMediaExtension($propertyId);
        
        // Config pour ce type de propriété
        $property['config'] = $this->getTypeConfig($property['type']);
        
        return $property;
    }

    /**
     * Rechercher par options/équipements
     * @param array $optionCodes Codes des options à chercher
     * @param string $matchType 'any' (au moins une) ou 'all' (toutes)
     * @return array
     */
    public function findByOptions($optionCodes, $matchType = 'any')
    {
        $builder = $this->db->table('properties p')
            ->select('DISTINCT p.*')
            ->join('property_option_values pov', 'pov.property_id = p.id', 'inner')
            ->join('property_options po', 'po.id = pov.option_id', 'inner')
            ->whereIn('po.code', $optionCodes);
        
        if ($matchType === 'all') {
            // Implémenter group by + having count
            $builder->groupBy('p.id')
                ->having('COUNT(DISTINCT po.code)', count($optionCodes));
        }
        
        return $builder->get()->getResultArray();
    }

    /**
     * Rechercher par scores de localisation
     * @param int $minOverallScore Score minimum requis (0-100)
     * @param array $filters Filtres additionnels sur les scores spécifiques
     * @return array
     */
    public function findByLocationScore($minOverallScore = 0, $filters = [])
    {
        $builder = $this->db->table('properties p')
            ->join('property_location_scoring pls', 'pls.property_id = p.id', 'inner')
            ->where('pls.overall_location_score >=', $minOverallScore);
        
        if (isset($filters['proximity_to_schools'])) {
            $builder->where('pls.proximity_to_schools >=', $filters['proximity_to_schools']);
        }
        
        if (isset($filters['area_safety_score'])) {
            $builder->where('pls.area_safety_score >=', $filters['area_safety_score']);
        }
        
        return $builder->get()->getResultArray();
    }

    /**
     * Rechercher par rentabilité (rendement)
     * @param float $minYield Rendement minimum en %
     * @param string $yieldType 'gross' ou 'net'
     * @return array
     */
    public function findByYield($minYield, $yieldType = 'net')
    {
        $field = $yieldType === 'gross' ? 'pfd.gross_yield' : 'pfd.net_yield';
        
        return $this->db->table('properties p')
            ->join('property_financial_data pfd', 'pfd.property_id = p.id', 'inner')
            ->where($field . ' >=', $minYield)
            ->where('p.transaction_type', 'rent')
            ->get()
            ->getResultArray();
    }

    /**
     * Calculer les coûts mensuels totaux
     * @param int $propertyId
     * @return float
     */
    public function getTotalMonthlyCosts($propertyId)
    {
        $costs = $this->getEstimatedCosts($propertyId);
        if (!$costs) {
            return 0;
        }
        
        $monthly = ($costs['syndic_monthly'] ?? 0)
            + ($costs['electricity_monthly'] ?? 0)
            + ($costs['water_monthly'] ?? 0)
            + ($costs['gas_monthly'] ?? 0)
            + ($costs['heating_monthly'] ?? 0)
            + (($costs['property_tax_annual'] ?? 0) / 12)
            + (($costs['insurance_annual'] ?? 0) / 12)
            + (($costs['maintenance_annual'] ?? 0) / 12)
            + ($costs['hoa_fees_monthly'] ?? 0)
            + ($costs['other_costs_monthly'] ?? 0);
        
        return round($monthly, 2);
    }

    /**
     * Calculer le rendement net approximatif
     * @param int $propertyId
     * @param float $rentalPrice Loyer mensuel
     * @return float Rendement net en %
     */
    public function estimateNetYield($propertyId, $rentalPrice)
    {
        $property = $this->find($propertyId);
        if (!$property || $property['price'] <= 0) {
            return 0;
        }
        
        $monthlyCosts = $this->getTotalMonthlyCosts($propertyId);
        $monthlyRevenue = $rentalPrice - $monthlyCosts;
        $annualRevenue = $monthlyRevenue * 12;
        
        return ($annualRevenue / $property['price']) * 100;
    }

    /**
     * Obtenir résumé complet pour investisseur
     * @param int $propertyId
     * @return array
     */
    public function getInvestorSummary($propertyId)
    {
        $property = $this->find($propertyId);
        $financial = $this->getFinancialData($propertyId);
        $costs = $this->getEstimatedCosts($propertyId);
        $location = $this->getLocationScoring($propertyId);
        
        return [
            'reference' => $property['reference'] ?? null,
            'title' => $property['title'] ?? null,
            'price' => $property['price'] ?? 0,
            'rental_price' => $property['rental_price'] ?? 0,
            'estimated_monthly_costs' => $this->getTotalMonthlyCosts($propertyId),
            'estimated_net_yield' => $financial['net_yield'] ?? null,
            'gross_yield' => $financial['gross_yield'] ?? null,
            'roi_annual' => $financial['roi_annual'] ?? null,
            'cap_rate' => $financial['cap_rate'] ?? null,
            'price_per_sqm' => $financial['price_per_sqm'] ?? null,
            'location_score' => $location['overall_location_score'] ?? 0,
            'area_safety_score' => $location['area_safety_score'] ?? 0,
            'property_type' => $property['type'] ?? null,
            'surface' => $property['area_total'] ?? 0,
            'rooms' => $property['rooms'] ?? 0,
        ];
    }
}
