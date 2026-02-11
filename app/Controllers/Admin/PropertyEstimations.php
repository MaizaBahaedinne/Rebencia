<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class PropertyEstimations extends BaseController
{
    protected $estimationModel;
    protected $clientModel;
    protected $userModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->estimationModel = model('PropertyEstimationModel');
        $this->clientModel = model('ClientModel');
        $this->userModel = model('UserModel');
        $this->zoneModel = model('ZoneModel');
    }

    public function index()
    {
        // Get filters
        $filters = [
            'status' => $this->request->getGet('status'),
            'property_type' => $this->request->getGet('property_type'),
            'transaction_type' => $this->request->getGet('transaction_type'),
            'city' => $this->request->getGet('city'),
            'governorate' => $this->request->getGet('governorate'),
            'assigned_to' => $this->request->getGet('assigned_to'),
        ];

        // Get estimations with details
        $estimations = $this->estimationModel->getEstimationsWithDetails($filters);

        // Get statistics
        $stats = $this->estimationModel->getStats();

        // Get agents for filter
        $agents = $this->userModel->where('role', 'agent')->orWhere('role', 'admin')->findAll();

        $data = [
            'title' => 'Gestion des Estimations',
            'estimations' => $estimations,
            'stats' => $stats,
            'agents' => $agents,
            'filters' => $filters
        ];

        return view('admin/property_estimations/index', $data);
    }

    public function view($id)
    {
        $estimation = $this->estimationModel->find($id);

        if (!$estimation) {
            return redirect()->to('/admin/property-estimations')->with('error', 'Estimation non trouvée');
        }

        // Get client details
        $client = $this->clientModel->find($estimation['client_id']);

        // Get assigned agent
        $agent = null;
        if ($estimation['assigned_to']) {
            $agent = $this->userModel->find($estimation['assigned_to']);
        }

        // Get zone
        $zone = null;
        if ($estimation['zone_id']) {
            $zone = $this->zoneModel->find($estimation['zone_id']);
        }

        $data = [
            'title' => 'Détails de l\'Estimation',
            'estimation' => $estimation,
            'client' => $client,
            'agent' => $agent,
            'zone' => $zone
        ];

        return view('admin/property_estimations/view', $data);
    }

    public function update($id)
    {
        $estimation = $this->estimationModel->find($id);

        if (!$estimation) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Estimation non trouvée'
            ]);
        }

        $updateData = [];

        // Update status
        if ($this->request->getPost('status')) {
            $updateData['status'] = $this->request->getPost('status');
        }

        // Update assigned agent
        if ($this->request->getPost('assigned_to')) {
            $updateData['assigned_to'] = $this->request->getPost('assigned_to');
        }

        // Update estimated price
        if ($this->request->getPost('estimated_price')) {
            $updateData['estimated_price'] = $this->request->getPost('estimated_price');
        }

        // Update response
        if ($this->request->getPost('response')) {
            $updateData['response'] = $this->request->getPost('response');
            $updateData['responded_at'] = date('Y-m-d H:i:s');
        }

        if ($this->estimationModel->update($id, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estimation mise à jour avec succès'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour'
        ]);
    }

    public function delete($id)
    {
        if ($this->estimationModel->delete($id)) {
            return redirect()->to('/admin/property-estimations')->with('success', 'Estimation supprimée avec succès');
        }

        return redirect()->to('/admin/property-estimations')->with('error', 'Erreur lors de la suppression');
    }
}
