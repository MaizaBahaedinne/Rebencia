<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SearchAlertModel;
use App\Models\ClientModel;

class SearchAlerts extends BaseController
{
    protected $alertModel;
    protected $clientModel;

    public function __construct()
    {
        $this->alertModel = new SearchAlertModel();
        $this->clientModel = new ClientModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Alertes de Recherche',
            'alerts' => $this->alertModel->getAlertsWithDetails()
        ];

        return view('admin/search_alerts/index', $data);
    }

    public function view($id)
    {
        $alert = $this->alertModel->find($id);

        if (!$alert) {
            return redirect()->to('/admin/search-alerts')->with('error', 'Alerte introuvable');
        }

        // Get client if exists
        $client = null;
        if ($alert['client_id']) {
            $client = $this->clientModel->find($alert['client_id']);
        }

        // Decode JSON fields
        if ($alert['property_type']) {
            $alert['property_types_array'] = json_decode($alert['property_type'], true);
        }
        if ($alert['zones']) {
            $alert['zones_array'] = json_decode($alert['zones'], true);
        }
        if ($alert['cities']) {
            $alert['cities_array'] = json_decode($alert['cities'], true);
        }
        if ($alert['governorates']) {
            $alert['governorates_array'] = json_decode($alert['governorates'], true);
        }

        $data = [
            'title' => 'Détails de l\'Alerte',
            'alert' => $alert,
            'client' => $client
        ];

        return view('admin/search_alerts/view', $data);
    }

    public function toggleActive($id)
    {
        $alert = $this->alertModel->find($id);

        if (!$alert) {
            return redirect()->back()->with('error', 'Alerte introuvable');
        }

        $newStatus = $alert['is_active'] ? 0 : 1;

        if ($this->alertModel->update($id, ['is_active' => $newStatus])) {
            $message = $newStatus ? 'Alerte activée' : 'Alerte désactivée';
            return redirect()->back()->with('success', $message);
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    public function delete($id)
    {
        if ($this->alertModel->delete($id)) {
            return redirect()->to('/admin/search-alerts')
                           ->with('success', 'Alerte supprimée avec succès');
        } else {
            return redirect()->back()->with('error', 'Erreur lors de la suppression');
        }
    }
}
