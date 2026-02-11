<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PropertyRequests extends BaseController
{
    protected $requestModel;
    protected $propertyModel;
    protected $clientModel;
    protected $userModel;

    public function __construct()
    {
        $this->requestModel = model('PropertyRequestModel');
        $this->propertyModel = model('PropertyModel');
        $this->clientModel = model('ClientModel');
        $this->userModel = model('UserModel');
    }

    public function index()
    {
        // Get filters
        $status = $this->request->getGet('status');
        $type = $this->request->getGet('type');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $builder = $this->requestModel
            ->select('property_requests.*, properties.reference, properties.title, properties.price, 
                      clients.first_name, clients.last_name, clients.phone, clients.email,
                      users.first_name as agent_first_name, users.last_name as agent_last_name')
            ->join('properties', 'properties.id = property_requests.property_id', 'left')
            ->join('clients', 'clients.id = property_requests.client_id')
            ->join('users', 'users.id = property_requests.assigned_to', 'left')
            ->orderBy('property_requests.created_at', 'DESC');

        // Apply filters
        if ($status) {
            $builder->where('property_requests.status', $status);
        }
        if ($type) {
            $builder->where('property_requests.request_type', $type);
        }
        if ($dateFrom) {
            $builder->where('property_requests.created_at >=', $dateFrom . ' 00:00:00');
        }
        if ($dateTo) {
            $builder->where('property_requests.created_at <=', $dateTo . ' 23:59:59');
        }

        $requests = $builder->findAll();

        // Get statistics
        $stats = [
            'total' => $this->requestModel->countAll(),
            'pending' => $this->requestModel->where('status', 'pending')->countAllResults(false),
            'contacted' => $this->requestModel->where('status', 'contacted')->countAllResults(false),
            'scheduled' => $this->requestModel->where('status', 'scheduled')->countAllResults(false),
            'completed' => $this->requestModel->where('status', 'completed')->countAllResults(false),
            'visits' => $this->requestModel->where('request_type', 'visit')->countAllResults(false),
            'information' => $this->requestModel->where('request_type', 'information')->countAllResults(false),
            'estimation' => $this->requestModel->where('request_type', 'estimation')->countAllResults(false),
        ];

        // Get agents for assignment
        $agents = $this->userModel->where('role_id', 2)->findAll(); // role_id 2 = agents

        return view('admin/property_requests/index', [
            'title' => 'Demandes Clients',
            'requests' => $requests,
            'stats' => $stats,
            'agents' => $agents,
            'filters' => [
                'status' => $status,
                'type' => $type,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ]);
    }

    public function view($id)
    {
        $request = $this->requestModel
            ->select('property_requests.*, properties.reference, properties.title, properties.price, properties.address, properties.city,
                      clients.first_name, clients.last_name, clients.phone, clients.email, clients.type,
                      users.first_name as agent_first_name, users.last_name as agent_last_name')
            ->join('properties', 'properties.id = property_requests.property_id')
            ->join('clients', 'clients.id = property_requests.client_id')
            ->join('users', 'users.id = property_requests.assigned_to', 'left')
            ->find($id);

        if (!$request) {
            return redirect()->back()->with('error', 'Demande non trouvée');
        }

        // Get agents for assignment
        $agents = $this->userModel->where('role_id', 2)->findAll();

        return view('admin/property_requests/view', [
            'title' => 'Détails de la demande',
            'request' => $request,
            'agents' => $agents
        ]);
    }

    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $response = $this->request->getPost('response');

        $data = ['status' => $status];
        
        if ($response) {
            $data['response'] = $response;
            $data['responded_at'] = date('Y-m-d H:i:s');
        }

        if ($this->requestModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Statut mis à jour avec succès'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour'
        ]);
    }

    public function assign()
    {
        $id = $this->request->getPost('id');
        $agentId = $this->request->getPost('agent_id');

        if ($this->requestModel->update($id, ['assigned_to' => $agentId])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Agent assigné avec succès'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de l\'assignation'
        ]);
    }

    public function delete($id)
    {
        if ($this->requestModel->delete($id)) {
            return redirect()->to('/admin/property-requests')->with('success', 'Demande supprimée avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors de la suppression');
    }
}
