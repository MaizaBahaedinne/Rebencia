<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyEstimationModel;
use App\Models\ClientModel;
use App\Models\UserModel;
use App\Models\ZoneModel;

class PropertyEstimations extends BaseController
{
    protected $estimationModel;
    protected $clientModel;
    protected $userModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->estimationModel = new PropertyEstimationModel();
        $this->clientModel = new ClientModel();
        $this->userModel = new UserModel();
        $this->zoneModel = new ZoneModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Demandes d\'Estimation',
            'estimations' => $this->estimationModel->getEstimationsWithDetails(),
            'stats' => $this->estimationModel->getStatsByStatus()
        ];

        return view('admin/property_estimations/index', $data);
    }

    public function view($id)
    {
        $estimation = $this->estimationModel->find($id);

        if (!$estimation) {
            return redirect()->to('/admin/property-estimations')->with('error', 'Demande d\'estimation introuvable');
        }

        // Get client if exists
        $client = null;
        if ($estimation['client_id']) {
            $client = $this->clientModel->find($estimation['client_id']);
        }

        // Get agent if assigned
        $agent = null;
        if ($estimation['agent_id']) {
            $agent = $this->userModel->find($estimation['agent_id']);
        }

        // Get zone if specified
        $zone = null;
        if ($estimation['zone_id']) {
            $zone = $this->zoneModel->find($estimation['zone_id']);
        }

        $data = [
            'title' => 'Détails de la Demande d\'Estimation',
            'estimation' => $estimation,
            'client' => $client,
            'agent' => $agent,
            'zone' => $zone,
            'agents' => $this->userModel->where('status', 'active')->findAll()
        ];

        return view('admin/property_estimations/view', $data);
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $agentId = $this->request->getPost('agent_id');
        $estimatedPrice = $this->request->getPost('estimated_price');
        $notes = $this->request->getPost('notes');

        $data = ['status' => $status];

        if ($agentId) {
            $data['agent_id'] = $agentId;
        }

        if ($estimatedPrice) {
            $data['estimated_price'] = $estimatedPrice;
        }

        if ($notes) {
            $data['notes'] = $notes;
        }

        if ($this->estimationModel->update($id, $data)) {
            return redirect()->to('/admin/property-estimations/view/' . $id)
                           ->with('success', 'Statut mis à jour avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    public function assignAgent($id)
    {
        $agentId = $this->request->getPost('agent_id');

        if ($this->estimationModel->update($id, ['agent_id' => $agentId, 'status' => 'in_progress'])) {
            return redirect()->to('/admin/property-estimations/view/' . $id)
                           ->with('success', 'Agent assigné avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de l\'assignation');
        }
    }

    public function delete($id)
    {
        if ($this->estimationModel->delete($id)) {
            return redirect()->to('/admin/property-estimations')
                           ->with('success', 'Demande supprimée avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la suppression');
        }
    }
}
