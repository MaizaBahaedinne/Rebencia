<?php

namespace App\Controllers;

use App\Models\PricePerM2Model;
use App\Models\ZoneModel;

class PricePerM2 extends BaseController
{
    protected $priceModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->priceModel = new PricePerM2Model();
        $this->zoneModel = new ZoneModel();
    }

    public function index()
    {
        $filters = [
            'governorate' => $this->request->getGet('governorate'),
            'city' => $this->request->getGet('city'),
            'zone_id' => $this->request->getGet('zone_id'),
            'property_type' => $this->request->getGet('property_type'),
            'transaction_type' => $this->request->getGet('transaction_type') ?: 'sale',
        ];

        $prices = $this->priceModel->getPricesWithZones($filters);
        
        // Grouper par gouvernorat et ville
        $groupedPrices = [];
        foreach ($prices as $price) {
            $gov = $price['governorate'] ?: 'Non spécifié';
            $city = $price['city'] ?: ($price['zone_name'] ?: 'Non spécifié');
            
            if (!isset($groupedPrices[$gov])) {
                $groupedPrices[$gov] = [];
            }
            if (!isset($groupedPrices[$gov][$city])) {
                $groupedPrices[$gov][$city] = [];
            }
            $groupedPrices[$gov][$city][] = $price;
        }

        // Obtenir les gouvernorats et villes pour les filtres
        $governorates = $this->priceModel->select('governorate')
            ->where('governorate IS NOT NULL')
            ->where('governorate !=', '')
            ->groupBy('governorate')
            ->findAll();

        $cities = [];
        if (!empty($filters['governorate'])) {
            $cities = $this->priceModel->select('city')
                ->where('governorate', $filters['governorate'])
                ->where('city IS NOT NULL')
                ->where('city !=', '')
                ->groupBy('city')
                ->findAll();
        }

        $data = [
            'title' => 'Prix de l\'immobilier au m²',
            'meta_description' => 'Découvrez les prix de l\'immobilier au m² en Tunisie. Consultez les prix moyens par zone, ville et type de bien.',
            'prices' => $prices,
            'groupedPrices' => $groupedPrices,
            'zones' => $this->zoneModel->orderBy('name', 'ASC')->findAll(),
            'governorates' => $governorates,
            'cities' => $cities,
            'filters' => $filters
        ];

        return view('price_per_m2/index', $data);
    }

    public function search()
    {
        $search = $this->request->getGet('q');
        $type = $this->request->getGet('type') ?: 'sale';

        $builder = $this->priceModel->select('price_per_m2.*, zones.name as zone_name')
            ->join('zones', 'zones.id = price_per_m2.zone_id', 'left')
            ->where('price_per_m2.is_active', 1)
            ->where('price_per_m2.transaction_type', $type);

        if ($search) {
            $builder->groupStart()
                ->like('zones.name', $search)
                ->orLike('price_per_m2.city', $search)
                ->orLike('price_per_m2.governorate', $search)
                ->groupEnd();
        }

        $results = $builder->findAll();

        return $this->response->setJSON($results);
    }
}
