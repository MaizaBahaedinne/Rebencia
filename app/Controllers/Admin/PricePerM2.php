<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
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
            'transaction_type' => $this->request->getGet('transaction_type'),
        ];

        $data = [
            'title' => 'Prix au m²',
            'prices' => $this->priceModel->getPricesWithZones($filters),
            'zones' => $this->zoneModel->orderBy('name', 'ASC')->findAll(),
            'filters' => $filters
        ];

        return view('admin/price_per_m2/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Ajouter un prix au m²',
            'zones' => $this->zoneModel->orderBy('name', 'ASC')->findAll()
        ];

        return view('admin/price_per_m2/create', $data);
    }

    public function store()
    {
        $rules = [
            'price_average' => 'required|decimal',
            'property_type' => 'required|in_list[apartment,villa,studio,office,shop,warehouse,land]',
            'transaction_type' => 'required|in_list[sale,rent]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'zone_id' => $this->request->getVar('zone_id') ?: null,
            'city' => $this->request->getVar('city'),
            'governorate' => $this->request->getVar('governorate'),
            'property_type' => $this->request->getVar('property_type'),
            'transaction_type' => $this->request->getVar('transaction_type'),
            'price_min' => $this->request->getVar('price_min') ?: null,
            'price_max' => $this->request->getVar('price_max') ?: null,
            'price_average' => $this->request->getVar('price_average'),
            'surface_average' => $this->request->getVar('surface_average') ?: null,
            'properties_count' => $this->request->getVar('properties_count') ?: 0,
            'evolution' => $this->request->getVar('evolution') ?: null,
            'period' => $this->request->getVar('period'),
            'is_active' => $this->request->getVar('is_active') ? 1 : 0,
        ];

        if ($this->priceModel->insert($data)) {
            return redirect()->to('/admin/price-per-m2')->with('success', 'Prix ajouté avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de l\'ajout du prix');
    }

    public function edit($id)
    {
        $price = $this->priceModel->find($id);

        if (!$price) {
            return redirect()->to('/admin/price-per-m2')->with('error', 'Prix introuvable');
        }

        $data = [
            'title' => 'Modifier le prix au m²',
            'price' => $price,
            'zones' => $this->zoneModel->orderBy('name', 'ASC')->findAll()
        ];

        return view('admin/price_per_m2/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'price_average' => 'required|decimal',
            'property_type' => 'required|in_list[apartment,villa,studio,office,shop,warehouse,land]',
            'transaction_type' => 'required|in_list[sale,rent]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'zone_id' => $this->request->getVar('zone_id') ?: null,
            'city' => $this->request->getVar('city'),
            'governorate' => $this->request->getVar('governorate'),
            'property_type' => $this->request->getVar('property_type'),
            'transaction_type' => $this->request->getVar('transaction_type'),
            'price_min' => $this->request->getVar('price_min') ?: null,
            'price_max' => $this->request->getVar('price_max') ?: null,
            'price_average' => $this->request->getVar('price_average'),
            'surface_average' => $this->request->getVar('surface_average') ?: null,
            'properties_count' => $this->request->getVar('properties_count') ?: 0,
            'evolution' => $this->request->getVar('evolution') ?: null,
            'period' => $this->request->getVar('period'),
            'is_active' => $this->request->getVar('is_active') ? 1 : 0,
        ];

        if ($this->priceModel->update($id, $data)) {
            return redirect()->to('/admin/price-per-m2')->with('success', 'Prix modifié avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    public function delete($id)
    {
        if ($this->priceModel->delete($id)) {
            return redirect()->to('/admin/price-per-m2')->with('success', 'Prix supprimé avec succès');
        }

        return redirect()->to('/admin/price-per-m2')->with('error', 'Erreur lors de la suppression');
    }

    public function calculateFromProperties()
    {
        // Calculer automatiquement les prix moyens depuis les biens existants
        $propertyModel = new \App\Models\PropertyModel();
        
        $results = $propertyModel->select('
            zone_id,
            property_type,
            transaction_type,
            MIN(CASE WHEN area_total > 0 THEN price / area_total END) as price_min,
            MAX(CASE WHEN area_total > 0 THEN price / area_total END) as price_max,
            AVG(CASE WHEN area_total > 0 THEN price / area_total END) as price_average,
            AVG(area_total) as surface_average,
            COUNT(*) as properties_count
        ')
        ->where('status', 'available')
        ->where('area_total >', 0)
        ->groupBy(['zone_id', 'property_type', 'transaction_type'])
        ->findAll();

        $inserted = 0;
        $period = date('Y-m');

        foreach ($results as $result) {
            if ($result['zone_id'] && $result['price_average']) {
                // Vérifier si existe déjà
                $existing = $this->priceModel->where([
                    'zone_id' => $result['zone_id'],
                    'property_type' => $result['property_type'],
                    'transaction_type' => $result['transaction_type'],
                    'period' => $period
                ])->first();

                if (!$existing) {
                    $this->priceModel->insert([
                        'zone_id' => $result['zone_id'],
                        'property_type' => $result['property_type'],
                        'transaction_type' => $result['transaction_type'],
                        'price_min' => round($result['price_min'], 2),
                        'price_max' => round($result['price_max'], 2),
                        'price_average' => round($result['price_average'], 2),
                        'surface_average' => round($result['surface_average'], 2),
                        'properties_count' => $result['properties_count'],
                        'period' => $period,
                        'is_active' => 1
                    ]);
                    $inserted++;
                }
            }
        }

        return redirect()->to('/admin/price-per-m2')
            ->with('success', "$inserted prix calculés et ajoutés depuis les biens existants");
    }
}
