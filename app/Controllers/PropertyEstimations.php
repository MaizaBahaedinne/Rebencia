<?php

namespace App\Controllers;

use App\Models\PropertyEstimationModel;
use App\Models\ClientModel;
use App\Models\ZoneModel;

class PropertyEstimations extends BaseController
{
    protected $estimationModel;
    protected $clientModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->estimationModel = new PropertyEstimationModel();
        $this->clientModel = new ClientModel();
        $this->zoneModel = new ZoneModel();
    }

    public function create()
    {
        $data = [
            'title' => 'Estimer mon bien',
            'zones' => $this->zoneModel->orderBy('name', 'ASC')->findAll()
        ];

        return view('property_estimations/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'property_type' => 'required|in_list[apartment,villa,studio,office,shop,warehouse,land,other]',
            'transaction_type' => 'required|in_list[sale,rent]',
            'area_total' => 'permit_empty|decimal',
            'rooms' => 'permit_empty|integer',
            'bedrooms' => 'permit_empty|integer',
            'bathrooms' => 'permit_empty|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if client exists by email
        $client = $this->clientModel->where('email', $this->request->getPost('email'))->first();
        $clientId = $client ? $client['id'] : null;

        // If client doesn't exist, create one
        if (!$clientId) {
            $clientData = [
                'first_name' => $this->request->getPost('first_name'),
                'last_name' => $this->request->getPost('last_name'),
                'email' => $this->request->getPost('email'),
                'phone' => $this->request->getPost('phone'),
                'type' => 'seller',
                'source' => 'estimation_request',
                'status' => 'lead'
            ];

            $clientId = $this->clientModel->insert($clientData);
        }

        $data = [
            'client_id' => $clientId,
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'property_type' => $this->request->getPost('property_type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'governorate' => $this->request->getPost('governorate'),
            'zone_id' => $this->request->getPost('zone_id') ?: null,
            'area_total' => $this->request->getPost('area_total') ?: null,
            'rooms' => $this->request->getPost('rooms') ?: null,
            'bedrooms' => $this->request->getPost('bedrooms') ?: null,
            'bathrooms' => $this->request->getPost('bathrooms') ?: null,
            'floor' => $this->request->getPost('floor') ?: null,
            'construction_year' => $this->request->getPost('construction_year') ?: null,
            'condition_state' => $this->request->getPost('condition_state') ?: null,
            'has_elevator' => $this->request->getPost('has_elevator') ? 1 : 0,
            'has_parking' => $this->request->getPost('has_parking') ? 1 : 0,
            'has_garden' => $this->request->getPost('has_garden') ? 1 : 0,
            'description' => $this->request->getPost('description'),
            'status' => 'pending'
        ];

        if ($this->estimationModel->insert($data)) {
            return redirect()->to('/estimer-mon-bien/success')
                           ->with('success', 'Votre demande d\'estimation a été envoyée avec succès. Nous vous contacterons bientôt.');
        } else {
            return redirect()->back()->withInput()
                           ->with('error', 'Une erreur s\'est produite. Veuillez réessayer.');
        }
    }

    public function success()
    {
        return view('property_estimations/success', ['title' => 'Demande envoyée']);
    }
}
