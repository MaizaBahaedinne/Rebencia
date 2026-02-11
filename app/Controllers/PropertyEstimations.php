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
        $client = $this->clientModel->where('email', $this->request->getVar('email'))->first();
        $clientId = $client ? $client['id'] : null;

        // If client doesn't exist, create one
        if (!$clientId) {
            $clientData = [
                'first_name' => $this->request->getVar('first_name'),
                'last_name' => $this->request->getVar('last_name'),
                'email' => $this->request->getVar('email'),
                'phone' => $this->request->getVar('phone'),
                'type' => 'seller',
                'source' => 'estimation_request',
                'status' => 'lead'
            ];

            $clientId = $this->clientModel->insert($clientData);
        }

        $data = [
            'client_id' => $clientId,
            'first_name' => $this->request->getVar('first_name'),
            'last_name' => $this->request->getVar('last_name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'property_type' => $this->request->getVar('property_type'),
            'transaction_type' => $this->request->getVar('transaction_type'),
            'address' => $this->request->getVar('address'),
            'city' => $this->request->getVar('city'),
            'governorate' => $this->request->getVar('governorate'),
            'zone_id' => $this->request->getVar('zone_id') ?: null,
            'area_total' => $this->request->getVar('area_total') ?: null,
            'rooms' => $this->request->getVar('rooms') ?: null,
            'bedrooms' => $this->request->getVar('bedrooms') ?: null,
            'bathrooms' => $this->request->getVar('bathrooms') ?: null,
            'floor' => $this->request->getVar('floor') ?: null,
            'construction_year' => $this->request->getVar('construction_year') ?: null,
            'condition_state' => $this->request->getVar('condition_state') ?: null,
            'has_elevator' => $this->request->getVar('has_elevator') ? 1 : 0,
            'has_parking' => $this->request->getVar('has_parking') ? 1 : 0,
            'has_garden' => $this->request->getVar('has_garden') ? 1 : 0,
            'description' => $this->request->getVar('description'),
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
