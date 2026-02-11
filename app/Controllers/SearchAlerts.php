<?php

namespace App\Controllers;

use App\Models\SearchAlertModel;
use App\Models\ClientModel;
use App\Models\ZoneModel;

class SearchAlerts extends BaseController
{
    protected $alertModel;
    protected $clientModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->alertModel = new SearchAlertModel();
        $this->clientModel = new ClientModel();
        $this->zoneModel = new ZoneModel();
    }

    public function create()
    {
        $data = [
            'title' => 'Créer une alerte',
            'zones' => $this->zoneModel->orderBy('name', 'ASC')->findAll()
        ];

        return view('search_alerts/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'first_name' => 'required|max_length[100]',
            'last_name' => 'required|max_length[100]',
            'email' => 'required|valid_email|max_length[255]',
            'phone' => 'permit_empty|max_length[20]',
            'transaction_type' => 'required|in_list[sale,rent,both]',
            'frequency' => 'required|in_list[instant,daily,weekly]',
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
                'type' => 'individual',
                'source' => 'search_alert',
                'status' => 'lead'
            ];

            $clientId = $this->clientModel->insert($clientData);
            
            if (!$clientId) {
                log_message('error', 'Failed to create client for alert: ' . json_encode($this->clientModel->errors()));
                return redirect()->back()->withInput()
                               ->with('error', 'Erreur lors de la création du compte client. Veuillez réessayer.');
            }
        }

        // Prepare JSON fields
        $propertyTypes = $this->request->getVar('property_type');
        $zones = $this->request->getVar('zones');
        $cities = $this->request->getVar('cities');
        $governorates = $this->request->getVar('governorates');

        $data = [
            'client_id' => $clientId,
            'first_name' => $this->request->getVar('first_name'),
            'last_name' => $this->request->getVar('last_name'),
            'email' => $this->request->getVar('email'),
            'phone' => $this->request->getVar('phone'),
            'property_type' => $propertyTypes ? json_encode($propertyTypes) : null,
            'transaction_type' => $this->request->getVar('transaction_type'),
            'price_min' => $this->request->getVar('price_min') ?: null,
            'price_max' => $this->request->getVar('price_max') ?: null,
            'area_min' => $this->request->getVar('area_min') ?: null,
            'area_max' => $this->request->getVar('area_max') ?: null,
            'rooms_min' => $this->request->getVar('rooms_min') ?: null,
            'bedrooms_min' => $this->request->getVar('bedrooms_min') ?: null,
            'bathrooms_min' => $this->request->getVar('bathrooms_min') ?: null,
            'zones' => $zones ? json_encode($zones) : null,
            'cities' => $cities ? json_encode($cities) : null,
            'governorates' => $governorates ? json_encode($governorates) : null,
            'has_elevator' => $this->request->getVar('has_elevator') ? 1 : null,
            'has_parking' => $this->request->getVar('has_parking') ? 1 : null,
            'has_garden' => $this->request->getVar('has_garden') ? 1 : null,
            'has_pool' => $this->request->getVar('has_pool') ? 1 : null,
            'frequency' => $this->request->getVar('frequency'),
            'is_active' => 1
        ];

        if ($this->alertModel->insert($data)) {
            return redirect()->to('/creer-une-alerte/success')
                           ->with('success', 'Votre alerte a été créée avec succès. Vous recevrez des notifications selon votre fréquence choisie.');
        } else {
            return redirect()->back()->withInput()
                           ->with('error', 'Une erreur s\'est produite. Veuillez réessayer.');
        }
    }

    public function success()
    {
        return view('search_alerts/success', ['title' => 'Alerte créée']);
    }

    public function unsubscribe($id, $token)
    {
        // Verify token (you should implement a proper token system)
        $alert = $this->alertModel->find($id);

        if ($alert && md5($alert['email'] . $alert['created_at']) === $token) {
            if ($this->alertModel->update($id, ['is_active' => 0])) {
                return view('search_alerts/unsubscribed', ['title' => 'Désabonnement réussi']);
            }
        }

        return redirect()->to('/')->with('error', 'Lien de désabonnement invalide');
    }
}
