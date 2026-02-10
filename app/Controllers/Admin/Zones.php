<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Zones extends BaseController
{
    protected $zoneModel;

    public function __construct()
    {
        $this->zoneModel = model('ZoneModel');
    }

    public function index()
    {
        // Get all governorates with their cities
        $governorates = $this->zoneModel
            ->where('type', 'governorate')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        // Get all cities grouped by parent_id
        $cities = $this->zoneModel
            ->where('type', 'city')
            ->orderBy('popularity_score', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        // Group cities by parent
        $citiesByParent = [];
        foreach ($cities as $city) {
            $parentId = $city['parent_id'] ?? 0;
            if (!isset($citiesByParent[$parentId])) {
                $citiesByParent[$parentId] = [];
            }
            $citiesByParent[$parentId][] = $city;
        }
        
        $data = [
            'title' => 'Gestion des Zones',
            'governorates' => $governorates,
            'citiesByParent' => $citiesByParent,
            'zones' => $this->zoneModel->orderBy('type', 'ASC')->orderBy('name', 'ASC')->findAll()
        ];

        return view('admin/zones/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nouvelle Zone',
            'parentZones' => $this->zoneModel->whereIn('type', ['governorate', 'city'])->findAll()
        ];

        return view('admin/zones/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'type' => 'required|in_list[governorate,city,district,area]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'name_ar' => $this->request->getPost('name_ar'),
            'name_en' => $this->request->getPost('name_en'),
            'type' => $this->request->getPost('type'),
            'parent_id' => $this->request->getPost('parent_id'),
            'country' => $this->request->getPost('country') ?: 'Tunisia',
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'popularity_score' => $this->request->getPost('popularity_score') ?: 0
        ];

        $this->zoneModel->insert($data);

        return redirect()->to(base_url('admin/zones'))->with('success', 'Zone créée avec succès');
    }

    public function edit($id)
    {
        $zone = $this->zoneModel->find($id);
        
        if (!$zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable');
        }

        $data = [
            'title' => 'Modifier Zone',
            'zone' => $zone,
            'parentZones' => $this->zoneModel->whereIn('type', ['governorate', 'city'])->where('id !=', $id)->findAll()
        ];

        return view('admin/zones/edit', $data);
    }

    public function update($id)
    {
        $zone = $this->zoneModel->find($id);
        
        if (!$zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'type' => 'required|in_list[governorate,city,district,area]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'name_ar' => $this->request->getPost('name_ar'),
            'name_en' => $this->request->getPost('name_en'),
            'type' => $this->request->getPost('type'),
            'parent_id' => $this->request->getPost('parent_id'),
            'country' => $this->request->getPost('country') ?: 'Tunisia',
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'popularity_score' => $this->request->getPost('popularity_score') ?: 0
        ];

        $this->zoneModel->update($id, $data);

        return redirect()->to(base_url('admin/zones'))->with('success', 'Zone modifiée avec succès');
    }

    public function delete($id)
    {
        $zone = $this->zoneModel->find($id);
        
        if (!$zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable');
        }

        // Check if zone has properties
        $propertyModel = model('PropertyModel');
        $propertiesCount = $propertyModel->where('zone_id', $id)->countAllResults();
        
        if ($propertiesCount > 0) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Impossible de supprimer: des propriétés sont liées à cette zone');
        }

        $this->zoneModel->delete($id);

        return redirect()->to(base_url('admin/zones'))->with('success', 'Zone supprimée avec succès');
    }
}
