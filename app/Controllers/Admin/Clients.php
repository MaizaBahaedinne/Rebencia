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
            'clients' => $this->clientModel->select('clients.*, users.first_name as agent_name, users.last_name as agent_lastname')
                ->join('users', 'users.id = clients.assigned_to', 'left')
                ->orderBy('clients.created_at', 'DESC')
                ->paginate(20)
        ];

        return view('admin/clients/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nouveau Client',
            'agents' => $this->userModel->where('status', 'active')->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll()
        ];

        return view('admin/clients/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'type' => 'required|in_list[individual,company]',
            'phone' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'type' => $this->request->getPost('type'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'company_name' => $this->request->getPost('company_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'phone_secondary' => $this->request->getPost('phone_secondary'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'governorate' => $this->request->getPost('governorate'),
            'source' => $this->request->getPost('source'),
            'status' => $this->request->getPost('status') ?? 'lead',
            'assigned_to' => $this->request->getPost('assigned_to') ?? session()->get('user_id'),
            'agency_id' => session()->get('agency_id'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->clientModel->insert($data)) {
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

        $data = [
            'title' => 'Modifier Client',
            'client' => $client,
            'agents' => $this->userModel->where('status', 'active')->findAll()
        ];

        return view('admin/clients/edit', $data);
    }

    public function update($id)
    {
        $client = $this->clientModel->find($id);
        
        if (!$client) {
            return redirect()->to('/admin/clients')->with('error', 'Client non trouvé');
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'status' => $this->request->getPost('status'),
            'notes' => $this->request->getPost('notes')
        ];

        if ($this->clientModel->update($id, $data)) {
            return redirect()->to('/admin/clients')->with('success', 'Client mis à jour');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
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
