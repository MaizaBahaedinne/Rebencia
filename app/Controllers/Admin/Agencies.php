<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Agencies extends BaseController
{
    protected $agencyModel;

    public function __construct()
    {
        $this->agencyModel = model('AgencyModel');
    }

    public function index()
    {
        $data = [
            'title' => 'Gestion des Agences',
            'agencies' => $this->agencyModel->getAgenciesWithStats()
        ];

        return view('admin/agencies/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nouvelle Agence',
            'agencies' => $this->agencyModel->where('type', 'siege')->where('status', 'active')->findAll()
        ];

        return view('admin/agencies/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[200]',
            'type' => 'required|in_list[siege,agence]',
            'address' => 'required|min_length[5]',
            'city' => 'required',
            'governorate' => 'required',
            'phone' => 'permit_empty|max_length[50]',
            'email' => 'permit_empty|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'code' => $this->generateAgencyCode(),
            'type' => $this->request->getPost('type'),
            'parent_id' => $this->request->getPost('parent_id'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'governorate' => $this->request->getPost('governorate'),
            'postal_code' => $this->request->getPost('postal_code'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'website' => $this->request->getPost('website'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'status' => 'active'
        ];

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move(ROOTPATH . 'public/uploads/agencies', $newName);
            $data['logo'] = $newName;
        }

        $this->agencyModel->insert($data);

        return redirect()->to(base_url('admin/agencies'))->with('success', 'Agence créée avec succès');
    }

    public function edit($id)
    {
        $agency = $this->agencyModel->find($id);
        
        if (!$agency) {
            return redirect()->to(base_url('admin/agencies'))->with('error', 'Agence introuvable');
        }

        $data = [
            'title' => 'Modifier Agence',
            'agency' => $agency,
            'agencies' => $this->agencyModel->where('type', 'siege')->where('status', 'active')->where('id !=', $id)->findAll()
        ];

        return view('admin/agencies/edit', $data);
    }

    public function update($id)
    {
        $agency = $this->agencyModel->find($id);
        
        if (!$agency) {
            return redirect()->to(base_url('admin/agencies'))->with('error', 'Agence introuvable');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|min_length[3]|max_length[200]',
            'type' => 'required|in_list[siege,agence]',
            'address' => 'required|min_length[5]',
            'city' => 'required',
            'governorate' => 'required',
            'phone' => 'permit_empty|max_length[50]',
            'email' => 'permit_empty|valid_email',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'type' => $this->request->getPost('type'),
            'parent_id' => $this->request->getPost('parent_id'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'governorate' => $this->request->getPost('governorate'),
            'postal_code' => $this->request->getPost('postal_code'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'website' => $this->request->getPost('website'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'status' => $this->request->getPost('status')
        ];

        // Handle logo upload
        $logo = $this->request->getFile('logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            // Delete old logo if exists
            if ($agency['logo'] && file_exists(ROOTPATH . 'public/uploads/agencies/' . $agency['logo'])) {
                unlink(ROOTPATH . 'public/uploads/agencies/' . $agency['logo']);
            }
            
            $newName = $logo->getRandomName();
            $logo->move(ROOTPATH . 'public/uploads/agencies', $newName);
            $data['logo'] = $newName;
        }

        $this->agencyModel->update($id, $data);

        return redirect()->to(base_url('admin/agencies'))->with('success', 'Agence modifiée avec succès');
    }

    public function view($id)
    {
        $agency = $this->agencyModel->find($id);
        
        if (!$agency) {
            return redirect()->to(base_url('admin/agencies'))->with('error', 'Agence introuvable');
        }

        // Get agency data with details
        $userModel = model('UserModel');
        $propertyModel = model('PropertyModel');
        $clientModel = model('ClientModel');
        $transactionModel = model('TransactionModel');

        $data = [
            'title' => 'Détails Agence - ' . $agency['name'],
            'agency' => $agency,
            'users' => $userModel->select('users.*, roles.display_name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->where('users.agency_id', $id)
                ->findAll(),
            'properties' => $propertyModel->select('properties.*, zones.name as zone_name, users.first_name as agent_name, users.last_name as agent_lastname')
                ->join('zones', 'zones.id = properties.zone_id', 'left')
                ->join('users', 'users.id = properties.agent_id', 'left')
                ->where('properties.agency_id', $id)
                ->orderBy('properties.created_at', 'DESC')
                ->findAll(),
            'clients' => $clientModel->select('clients.*, users.first_name as agent_name, users.last_name as agent_lastname')
                ->join('users', 'users.id = clients.assigned_to', 'left')
                ->where('clients.agency_id', $id)
                ->orderBy('clients.created_at', 'DESC')
                ->findAll(),
            'transactions' => $transactionModel->select('transactions.*, properties.title as property_title, properties.reference as property_ref,
                CONCAT(client.first_name, " ", client.last_name) as client_name,
                CONCAT(agent.first_name, " ", agent.last_name) as agent_name')
                ->join('properties', 'properties.id = transactions.property_id', 'left')
                ->join('clients as client', 'client.id = transactions.client_id', 'left')
                ->join('users as agent', 'agent.id = transactions.agent_id', 'left')
                ->where('transactions.agency_id', $id)
                ->orderBy('transactions.created_at', 'DESC')
                ->findAll(),
            'subAgencies' => $this->agencyModel->where('parent_id', $id)->findAll()
        ];

        return view('admin/agencies/view', $data);
    }

    public function delete($id)
    {
        $agency = $this->agencyModel->find($id);
        
        if (!$agency) {
            return redirect()->to(base_url('admin/agencies'))->with('error', 'Agence introuvable');
        }

        // Check if agency has users
        $userModel = model('UserModel');
        $usersCount = $userModel->where('agency_id', $id)->countAllResults();
        
        if ($usersCount > 0) {
            return redirect()->to(base_url('admin/agencies'))->with('error', 'Impossible de supprimer: des utilisateurs sont assignés à cette agence');
        }

        // Delete logo if exists
        if ($agency['logo'] && file_exists(ROOTPATH . 'public/uploads/agencies/' . $agency['logo'])) {
            unlink(ROOTPATH . 'public/uploads/agencies/' . $agency['logo']);
        }

        $this->agencyModel->delete($id);

        return redirect()->to(base_url('admin/agencies'))->with('success', 'Agence supprimée avec succès');
    }

    /**
     * Generate unique agency code
     */
    private function generateAgencyCode()
    {
        $prefix = 'AGC';
        $lastAgency = $this->agencyModel->orderBy('id', 'DESC')->first();
        $nextId = $lastAgency ? $lastAgency['id'] + 1 : 1;
        
        return $prefix . str_pad($nextId, 4, '0', STR_PAD_LEFT);
    }
}
