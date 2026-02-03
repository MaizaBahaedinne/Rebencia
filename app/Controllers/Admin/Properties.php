<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Properties extends BaseController
{
    protected $propertyModel;
    protected $zoneModel;
    protected $agencyModel;

    public function __construct()
    {
        $this->propertyModel = model('PropertyModel');
        $this->zoneModel = model('ZoneModel');
        $this->agencyModel = model('AgencyModel');
    }

    public function index()
    {
        $data = [
            'title' => 'Gestion des Propriétés',
            'properties' => $this->propertyModel->select('properties.*, zones.name as zone_name, users.first_name as agent_name')
                ->join('zones', 'zones.id = properties.zone_id', 'left')
                ->join('users', 'users.id = properties.agent_id', 'left')
                ->orderBy('properties.created_at', 'DESC')
                ->paginate(20)
        ];

        return view('admin/properties/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nouvelle Propriété',
            'zones' => $this->zoneModel->getGovernorates(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll()
        ];

        return view('admin/properties/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|min_length[3]',
            'type' => 'required',
            'transaction_type' => 'required',
            'price' => 'required|decimal',
            'area_total' => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'reference' => 'PROP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6)),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'price' => $this->request->getPost('price'),
            'rental_price' => $this->request->getPost('rental_price'),
            'area_total' => $this->request->getPost('area_total'),
            'area_living' => $this->request->getPost('area_living'),
            'rooms' => $this->request->getPost('rooms'),
            'bedrooms' => $this->request->getPost('bedrooms'),
            'bathrooms' => $this->request->getPost('bathrooms'),
            'floor' => $this->request->getPost('floor'),
            'has_elevator' => $this->request->getPost('has_elevator') ? 1 : 0,
            'has_parking' => $this->request->getPost('has_parking') ? 1 : 0,
            'has_garden' => $this->request->getPost('has_garden') ? 1 : 0,
            'has_pool' => $this->request->getPost('has_pool') ? 1 : 0,
            'zone_id' => $this->request->getPost('zone_id'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'governorate' => $this->request->getPost('governorate'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'agency_id' => session()->get('agency_id'),
            'agent_id' => session()->get('user_id'),
            'status' => 'draft'
        ];

        if ($this->propertyModel->insert($data)) {
            return redirect()->to('/admin/properties')->with('success', 'Propriété créée avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    public function edit($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Propriété non trouvée');
        }

        $data = [
            'title' => 'Modifier Propriété',
            'property' => $property,
            'zones' => $this->zoneModel->getGovernorates(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll()
        ];

        return view('admin/properties/edit', $data);
    }

    public function update($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Propriété non trouvée');
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'price' => $this->request->getPost('price'),
            'rental_price' => $this->request->getPost('rental_price'),
            'area_total' => $this->request->getPost('area_total'),
            'rooms' => $this->request->getPost('rooms'),
            'bedrooms' => $this->request->getPost('bedrooms'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->propertyModel->update($id, $data)) {
            return redirect()->to('/admin/properties')->with('success', 'Propriété mise à jour');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
    }

    public function delete($id)
    {
        if ($this->propertyModel->delete($id)) {
            return redirect()->to('/admin/properties')->with('success', 'Propriété supprimée');
        }

        return redirect()->to('/admin/properties')->with('error', 'Erreur lors de la suppression');
    }

    public function view($id)
    {
        $data = [
            'title' => 'Détails Propriété',
            'property' => $this->propertyModel->getPropertyWithDetails($id)
        ];

        return view('admin/properties/view', $data);
    }
}
