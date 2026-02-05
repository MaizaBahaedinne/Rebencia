<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Clients extends BaseController
{
    protected $clientModel;
    protected $userModel;
    protected $agencyModel;

    public function __construct()
    {
        $this->clientModel = model('ClientModel');
        $this->userModel = model('UserModel');
        $this->agencyModel = model('AgencyModel');
    }

    public function index()
    {
        $data = [
            'title' => 'Gestion des Clients',
            'clients' => $this->clientModel->getAllWithAgencyFilter(20),
            'pager' => $this->clientModel->pager
        ];

        return view('admin/clients/index', $data);
    }

    public function create()
    {
        $zoneModel = model('ZoneModel');
        
        $data = [
            'title' => 'Nouveau Client',
            'agents' => $this->userModel->whereIn('role_id', [6, 7, 8])->where('status', 'active')->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'zones' => $zoneModel->findAll()
        ];

        return view('admin/clients/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'type' => 'required|in_list[buyer,seller,tenant,landlord]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Gérer les zones préférées (array)
        $preferredZones = $this->request->getPost('preferred_zones');
        $preferredZonesJson = $preferredZones ? json_encode($preferredZones) : null;

        $data = [
            'type' => $this->request->getPost('type'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'phone_secondary' => $this->request->getPost('phone_secondary'),
            'cin' => $this->request->getPost('cin'),
            'address' => $this->request->getPost('address'),
            'source' => $this->request->getPost('source') ?? 'website',
            'status' => $this->request->getPost('status') ?? 'active',
            'assigned_to' => $this->request->getPost('assigned_agent_id') ?? session()->get('user_id'),
            'agency_id' => $this->request->getPost('agency_id') ?? session()->get('agency_id'),
            'property_type_preference' => $this->request->getPost('property_type_preference'),
            'transaction_type_preference' => $this->request->getPost('transaction_type_preference'),
            'budget_min' => $this->request->getPost('budget_min'),
            'budget_max' => $this->request->getPost('budget_max'),
            'preferred_zones' => $preferredZonesJson,
            'area_preference' => $this->request->getPost('area_preference'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($clientId = $this->clientModel->insert($data)) {
            // Trigger notification
            $notificationHelper = new \App\Libraries\NotificationHelper();
            $notificationHelper->notifyClientCreated($clientId, $data, session()->get('user_id'));
            
            return redirect()->to('/admin/clients')->with('success', 'Client créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    public function edit($id)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return redirect()->to('/admin/clients')->with('error', 'Client non trouvé');
        }

        $zoneModel = model('ZoneModel');

        $data = [
            'title' => 'Modifier Client',
            'client' => $client,
            'agents' => $this->userModel->whereIn('role_id', [6, 7, 8])->where('status', 'active')->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'zones' => $zoneModel->findAll()
        ];

        return view('admin/clients/edit', $data);
    }

    public function update($id)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return redirect()->to('/admin/clients')->with('error', 'Client non trouvé');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'type' => 'required|in_list[buyer,seller,tenant,landlord]',
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Gérer les zones préférées
        $preferredZones = $this->request->getPost('preferred_zones');
        $preferredZonesJson = $preferredZones ? json_encode($preferredZones) : null;

        $data = [
            'type' => $this->request->getPost('type'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'phone_secondary' => $this->request->getPost('phone_secondary'),
            'cin' => $this->request->getPost('cin'),
            'address' => $this->request->getPost('address'),
            'source' => $this->request->getPost('source'),
            'status' => $this->request->getPost('status'),
            'assigned_to' => $this->request->getPost('assigned_agent_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'property_type_preference' => $this->request->getPost('property_type_preference'),
            'transaction_type_preference' => $this->request->getPost('transaction_type_preference'),
            'budget_min' => $this->request->getPost('budget_min'),
            'budget_max' => $this->request->getPost('budget_max'),
            'preferred_zones' => $preferredZonesJson,
            'area_preference' => $this->request->getPost('area_preference'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->clientModel->update($id, $data)) {
            return redirect()->to('/admin/clients')->with('success', 'Client modifié avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    public function delete($id)
    {
        if ($this->clientModel->delete($id)) {
            return redirect()->to('/admin/clients')->with('success', 'Client supprimé');
        }

        return redirect()->to('/admin/clients')->with('error', 'Erreur lors de la suppression');
    }

    public function view($id)
    {
        $data = [
            'title' => 'Détails Client',
            'client' => $this->clientModel->getClientWithAgent($id)
        ];

        return view('admin/clients/view', $data);
    }
}
