<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class AgencyZones extends BaseController
{
    protected $agencyZoneModel;
    protected $agencyModel;
    protected $zoneModel;

    public function __construct()
    {
        $this->agencyZoneModel = model('AgencyZoneModel');
        $this->agencyModel = model('AgencyModel');
        $this->zoneModel = model('ZoneModel');
    }

    /**
     * Page principale d'affectation des zones aux agences
     */
    public function index()
    {
        $data = [
            'title' => 'Affectation des Zones aux Agences',
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'zones' => $this->zoneModel->orderBy('type')->orderBy('name')->findAll(),
            'assignments' => $this->agencyZoneModel->getAllAssignments()
        ];

        return view('admin/agency_zones/index', $data);
    }

    /**
     * Page de gestion des zones pour une agence spécifique avec carte
     */
    public function manage($agencyId)
    {
        $agency = $this->agencyModel->find($agencyId);
        
        if (!$agency) {
            return redirect()->to(base_url('admin/agency-zones'))
                ->with('error', 'Agence introuvable');
        }

        $assignedZones = $this->agencyZoneModel->getAgencyZones($agencyId);
        $allZones = $this->zoneModel->orderBy('type')->orderBy('name')->findAll();

        $data = [
            'title' => 'Gestion des Zones - ' . $agency['name'],
            'agency' => $agency,
            'assignedZones' => $assignedZones,
            'allZones' => $allZones
        ];

        return view('admin/agency_zones/manage', $data);
    }

    /**
     * Sauvegarder les affectations de zones pour une agence
     */
    public function save($agencyId)
    {
        $zones = $this->request->getPost('zones');
        
        if (!$zones) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucune zone sélectionnée'
            ]);
        }

        $zonesData = [];
        foreach ($zones as $zoneData) {
            $zonesData[] = [
                'zone_id' => $zoneData['zone_id'],
                'coordinates' => $zoneData['coordinates'] ?? null,
                'is_primary' => $zoneData['is_primary'] ?? 0
            ];
        }

        try {
            $this->agencyZoneModel->assignZonesToAgency($agencyId, $zonesData);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Zones affectées avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de l\'affectation: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtenir les zones d'une agence (AJAX)
     */
    public function getAgencyZones($agencyId)
    {
        $zones = $this->agencyZoneModel->getAgencyZones($agencyId);
        
        return $this->response->setJSON([
            'success' => true,
            'zones' => $zones
        ]);
    }

    /**
     * Supprimer une affectation
     */
    public function delete($id)
    {
        try {
            $this->agencyZoneModel->delete($id);
            
            return redirect()->back()
                ->with('success', 'Affectation supprimée avec succès');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}
