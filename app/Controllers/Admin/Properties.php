<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\HierarchyHelper;

class Properties extends BaseController
{
    protected $propertyModel;
    protected $zoneModel;
    protected $agencyModel;
    protected $hierarchyHelper;

    public function __construct()
    {
        $this->propertyModel = model('PropertyModel');
        $this->zoneModel = model('ZoneModel');
        $this->agencyModel = model('AgencyModel');
        $this->hierarchyHelper = new HierarchyHelper();
    }

    public function index()
    {
        // Récupérer l'utilisateur connecté
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        if (!$currentUserId) {
            return redirect()->to('/login')->with('error', 'Session expirée');
        }
        
        // Admin voit tout, les autres voient leur agence et sous-agences
        if ($currentRoleLevel == 100) {
            // Admin voit tous les biens
            $properties = $this->propertyModel
                ->select('properties.*, zones.name as zone_name, agencies.name as agency_name, 
                         CONCAT(users.first_name, " ", users.last_name) as agent_name')
                ->join('zones', 'zones.id = properties.zone_id', 'left')
                ->join('agencies', 'agencies.id = properties.agency_id', 'left')
                ->join('users', 'users.id = properties.agent_id', 'left')
                ->orderBy('properties.created_at', 'DESC')
                ->paginate(20);
        } else {
            // Récupérer les IDs des utilisateurs accessibles (self + subordonnés récursifs)
            $accessibleUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
            
            if (empty($accessibleUserIds)) {
                $accessibleUserIds = [$currentUserId];
            }
            
            // Filtrer les propriétés par agence et sous-agences
            $userModel = model('UserModel');
            $currentUser = $userModel->find($currentUserId);
            $currentAgencyId = $currentUser['agency_id'] ?? null;
            
            $properties = $this->propertyModel
                ->select('properties.*, zones.name as zone_name, agencies.name as agency_name, 
                         CONCAT(users.first_name, " ", users.last_name) as agent_name')
                ->join('zones', 'zones.id = properties.zone_id', 'left')
                ->join('agencies', 'agencies.id = properties.agency_id', 'left')
                ->join('users', 'users.id = properties.agent_id', 'left')
                ->where('properties.agency_id', $currentAgencyId)
                ->orderBy('properties.created_at', 'DESC')
                ->paginate(20);
        }
        
        // Récupérer les IDs des utilisateurs modifiables (pour les boutons edit/delete)
        $editableUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
        if (empty($editableUserIds)) {
            $editableUserIds = [$currentUserId];
        }
        
        $data = [
            'title' => 'Gestion des Propriétés',
            'properties' => $properties,
            'pager' => $this->propertyModel->pager,
            'currentUserId' => $currentUserId,
            'currentRoleLevel' => $currentRoleLevel,
            'editableUserIds' => $editableUserIds
        ];

        return view('admin/properties/index', $data);
    }

    public function create()
    {
        $userModel = model('UserModel');
        
        $data = [
            'title' => 'Nouvelle Propriété',
            'property' => [],
            'rooms' => [],
            'proximities' => [],
            'documents' => [],
            'photos' => [],
            'zones' => $this->zoneModel->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->findAll(),
            'isEdit' => false
        ];

        return view('admin/properties/form', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'title_fr' => 'required|min_length[3]|max_length[255]',
            'description_fr' => 'required|min_length[10]',
            'type' => 'required|in_list[apartment,villa,house,land,commercial,office]',
            'transaction_type' => 'required|in_list[sale,rent,both]',
            'area_total' => 'required|decimal',
            'zone_id' => 'required|is_natural_no_zero',
            'address' => 'required|min_length[5]',
            'price' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Génération de la référence unique
        $reference = $this->request->getPost('reference');
        if (empty($reference)) {
            $reference = 'PROP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        }

        $data = [
            // Step 1: General
            'reference' => $reference,
            'type' => $this->request->getPost('type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'title_fr' => $this->request->getPost('title_fr'),
            'title_ar' => $this->request->getPost('title_ar'),
            'title_en' => $this->request->getPost('title_en'),
            'description_fr' => $this->request->getPost('description_fr'),
            'description_ar' => $this->request->getPost('description_ar'),
            'description_en' => $this->request->getPost('description_en'),
            'disponibilite_date' => $this->request->getPost('disponibilite_date'),
            'status' => $this->request->getPost('status') ?: 'available',
            'featured' => $this->request->getPost('featured') ? 1 : 0,
            
            // Step 2: Location
            'zone_id' => $this->request->getPost('zone_id'),
            'governorate' => $this->request->getPost('governorate'),
            'city' => $this->request->getPost('city'),
            'neighborhood' => $this->request->getPost('neighborhood'),
            'postal_code' => $this->request->getPost('postal_code'),
            'address' => $this->request->getPost('address'),
            'hide_address' => $this->request->getPost('hide_address') ? 1 : 0,
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            
            // Step 3: Features
            'area_total' => $this->request->getPost('area_total'),
            'area_living' => $this->request->getPost('area_living'),
            'area_land' => $this->request->getPost('area_land'),
            'rooms' => $this->request->getPost('rooms') ?: 0,
            'bedrooms' => $this->request->getPost('bedrooms') ?: 0,
            'bathrooms' => $this->request->getPost('bathrooms') ?: 0,
            'parking_spaces' => $this->request->getPost('parking_spaces') ?: 0,
            'floor' => $this->request->getPost('floor'),
            'total_floors' => $this->request->getPost('total_floors'),
            'construction_year' => $this->request->getPost('construction_year'),
            'orientation' => $this->request->getPost('orientation'),
            'floor_type' => $this->request->getPost('floor_type'),
            'gas_type' => $this->request->getPost('gas_type') ?: 'aucun',
            'standing' => $this->request->getPost('standing') ?: 'standard',
            'condition_state' => $this->request->getPost('condition_state') ?: 'good',
            'legal_status' => $this->request->getPost('legal_status') ?: 'clear',
            'energy_class' => $this->request->getPost('energy_class'),
            'energy_consumption_kwh' => $this->request->getPost('energy_consumption_kwh'),
            'co2_emission' => $this->request->getPost('co2_emission'),
            'has_elevator' => $this->request->getPost('has_elevator') ? 1 : 0,
            'has_parking' => $this->request->getPost('has_parking') ? 1 : 0,
            'has_garden' => $this->request->getPost('has_garden') ? 1 : 0,
            'has_pool' => $this->request->getPost('has_pool') ? 1 : 0,
            
            // Step 4: Pricing
            'price' => $this->request->getPost('price'),
            'rental_price' => $this->request->getPost('rental_price'),
            'promo_price' => $this->request->getPost('promo_price'),
            'promo_start_date' => $this->request->getPost('promo_start_date'),
            'promo_end_date' => $this->request->getPost('promo_end_date'),
            'charge_syndic' => $this->request->getPost('charge_syndic'),
            'charge_water' => $this->request->getPost('charge_water'),
            'charge_gas' => $this->request->getPost('charge_gas'),
            'charge_electricity' => $this->request->getPost('charge_electricity'),
            'charge_other' => $this->request->getPost('charge_other'),
            
            // Step 6: Notes
            'internal_notes' => $this->request->getPost('internal_notes'),
            
            // Meta
            'agency_id' => $this->request->getPost('agency_id') ?: null,
            'agent_id' => $this->request->getPost('agent_id') ?: session()->get('user_id'),
            'created_by' => session()->get('user_id'),
        ];

        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Insérer la propriété
            $propertyId = $this->propertyModel->insert($data);
            
            if (!$propertyId) {
                throw new \Exception('Erreur lors de la création de la propriété');
            }
            
            // Step 5: Sauvegarder les pièces
            $rooms = $this->request->getPost('rooms');
            if (!empty($rooms)) {
                $propertyRoomModel = model('PropertyRoomModel');
                $propertyRoomModel->saveRooms($propertyId, $rooms);
            }
            
            // Step 5: Sauvegarder les proximités
            $proximities = $this->request->getPost('proximities');
            if (!empty($proximities)) {
                $propertyProximityModel = model('PropertyProximityModel');
                $propertyProximityModel->saveProximities($propertyId, $proximities);
            }
            
            // Step 6: Upload des photos
            $photoFiles = $this->request->getFileMultiple('photos');
            if (!empty($photoFiles)) {
                $this->handlePhotoUpload($propertyId, $photoFiles);
            }
            
            // Step 6: Upload des documents
            $documents = $this->request->getFiles();
            if (!empty($documents['documents'])) {
                $this->handleDocumentUpload($propertyId, $documents['documents']);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la transaction');
            }
            
            return redirect()->to('/admin/properties')->with('success', 'Bien immobilier créé avec succès');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
    
    private function handlePhotoUpload($propertyId, $files)
    {
        $propertyDocumentModel = model('PropertyDocumentModel');
        $uploadPath = FCPATH . 'uploads/properties/' . $propertyId . '/photos';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                
                $propertyDocumentModel->insert([
                    'property_id' => $propertyId,
                    'document_type' => 'photo',
                    'file_name' => $file->getClientName(),
                    'file_path' => 'uploads/properties/' . $propertyId . '/photos/' . $newName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_by' => session()->get('user_id')
                ]);
            }
        }
    }
    
    private function handleDocumentUpload($propertyId, $documentsByType)
    {
        $propertyDocumentModel = model('PropertyDocumentModel');
        $uploadPath = FCPATH . 'uploads/properties/' . $propertyId . '/documents';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        foreach ($documentsByType as $type => $files) {
            if (!is_array($files)) {
                $files = [$files];
            }
            
            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($uploadPath, $newName);
                    
                    $propertyDocumentModel->insert([
                        'property_id' => $propertyId,
                        'document_type' => $type,
                        'file_name' => $file->getClientName(),
                        'file_path' => 'uploads/properties/' . $propertyId . '/documents/' . $newName,
                        'file_size' => $file->getSize(),
                        'mime_type' => $file->getMimeType(),
                        'uploaded_by' => session()->get('user_id')
                    ]);
                }
            }
        }
    }

    public function edit($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Bien non trouvé');
        }
        
        // Vérifier les permissions : admin ou propriétaire du bien ou manager du propriétaire
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        $editableUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
        
        if ($currentRoleLevel != 100 && !in_array($property['agent_id'], $editableUserIds)) {
            return redirect()->to('/admin/properties')->with('error', 'Vous n\'avez pas la permission de modifier ce bien.');
        }

        $userModel = model('UserModel');
        $propertyRoomModel = model('PropertyRoomModel');
        $propertyProximityModel = model('PropertyProximityModel');
        $propertyDocumentModel = model('PropertyDocumentModel');

        $data = [
            'title' => 'Modifier le Bien - ' . $property['reference'],
            'property' => $property,
            'rooms' => $propertyRoomModel->getRoomsByProperty($id),
            'proximities' => $propertyProximityModel->getProximitiesByProperty($id),
            'documents' => $propertyDocumentModel->getDocumentsByProperty($id),
            'photos' => $propertyDocumentModel->getPhotosByProperty($id),
            'zones' => $this->zoneModel->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->findAll(),
            'isEdit' => true
        ];

        return view('admin/properties/form', $data);
    }

    public function update($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Bien non trouvé');
        }
        
        // Vérifier les permissions : admin ou propriétaire du bien ou manager du propriétaire
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        $editableUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
        
        if ($currentRoleLevel != 100 && !in_array($property['agent_id'], $editableUserIds)) {
            return redirect()->to('/admin/properties')->with('error', 'Vous n\'avez pas la permission de modifier ce bien.');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'title_fr' => 'required|min_length[3]|max_length[255]',
            'description_fr' => 'required|min_length[10]',
            'type' => 'required|in_list[apartment,villa,house,land,commercial,office]',
            'transaction_type' => 'required|in_list[sale,rent,both]',
            'area_total' => 'required|decimal',
            'zone_id' => 'required|is_natural_no_zero',
            'address' => 'required|min_length[5]',
            'price' => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            // Step 1: General
            'type' => $this->request->getPost('type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'title_fr' => $this->request->getPost('title_fr'),
            'title_ar' => $this->request->getPost('title_ar'),
            'title_en' => $this->request->getPost('title_en'),
            'description_fr' => $this->request->getPost('description_fr'),
            'description_ar' => $this->request->getPost('description_ar'),
            'description_en' => $this->request->getPost('description_en'),
            'disponibilite_date' => $this->request->getPost('disponibilite_date'),
            'status' => $this->request->getPost('status') ?: 'available',
            'featured' => $this->request->getPost('featured') ? 1 : 0,
            
            // Step 2: Location
            'zone_id' => $this->request->getPost('zone_id'),
            'governorate' => $this->request->getPost('governorate'),
            'city' => $this->request->getPost('city'),
            'neighborhood' => $this->request->getPost('neighborhood'),
            'postal_code' => $this->request->getPost('postal_code'),
            'address' => $this->request->getPost('address'),
            'hide_address' => $this->request->getPost('hide_address') ? 1 : 0,
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            
            // Step 3: Features
            'area_total' => $this->request->getPost('area_total'),
            'area_living' => $this->request->getPost('area_living'),
            'area_land' => $this->request->getPost('area_land'),
            'rooms' => $this->request->getPost('rooms') ?: 0,
            'bedrooms' => $this->request->getPost('bedrooms') ?: 0,
            'bathrooms' => $this->request->getPost('bathrooms') ?: 0,
            'parking_spaces' => $this->request->getPost('parking_spaces') ?: 0,
            'floor' => $this->request->getPost('floor'),
            'total_floors' => $this->request->getPost('total_floors'),
            'construction_year' => $this->request->getPost('construction_year'),
            'orientation' => $this->request->getPost('orientation'),
            'floor_type' => $this->request->getPost('floor_type'),
            'gas_type' => $this->request->getPost('gas_type') ?: 'aucun',
            'standing' => $this->request->getPost('standing') ?: 'standard',
            'condition_state' => $this->request->getPost('condition_state') ?: 'good',
            'legal_status' => $this->request->getPost('legal_status') ?: 'clear',
            'energy_class' => $this->request->getPost('energy_class'),
            'energy_consumption_kwh' => $this->request->getPost('energy_consumption_kwh'),
            'co2_emission' => $this->request->getPost('co2_emission'),
            'has_elevator' => $this->request->getPost('has_elevator') ? 1 : 0,
            'has_parking' => $this->request->getPost('has_parking') ? 1 : 0,
            'has_garden' => $this->request->getPost('has_garden') ? 1 : 0,
            'has_pool' => $this->request->getPost('has_pool') ? 1 : 0,
            
            // Step 4: Pricing
            'price' => $this->request->getPost('price'),
            'rental_price' => $this->request->getPost('rental_price'),
            'promo_price' => $this->request->getPost('promo_price'),
            'promo_start_date' => $this->request->getPost('promo_start_date'),
            'promo_end_date' => $this->request->getPost('promo_end_date'),
            'charge_syndic' => $this->request->getPost('charge_syndic'),
            'charge_water' => $this->request->getPost('charge_water'),
            'charge_gas' => $this->request->getPost('charge_gas'),
            'charge_electricity' => $this->request->getPost('charge_electricity'),
            'charge_other' => $this->request->getPost('charge_other'),
            
            // Step 6: Notes
            'internal_notes' => $this->request->getPost('internal_notes'),
            
            // Meta
            'agency_id' => $this->request->getPost('agency_id') ?: null,
            'agent_id' => $this->request->getPost('agent_id') ?: session()->get('user_id'),
        ];

        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Mettre à jour la propriété
            if (!$this->propertyModel->update($id, $data)) {
                throw new \Exception('Erreur lors de la mise à jour de la propriété');
            }
            
            // Step 5: Mettre à jour les pièces
            $rooms = $this->request->getPost('rooms');
            $propertyRoomModel = model('PropertyRoomModel');
            $propertyRoomModel->where('property_id', $id)->delete();
            if (!empty($rooms)) {
                $propertyRoomModel->saveRooms($id, $rooms);
            }
            
            // Step 5: Mettre à jour les proximités
            $proximities = $this->request->getPost('proximities');
            $propertyProximityModel = model('PropertyProximityModel');
            $propertyProximityModel->where('property_id', $id)->delete();
            if (!empty($proximities)) {
                $propertyProximityModel->saveProximities($id, $proximities);
            }
            
            // Step 6: Upload des nouvelles photos
            $photoFiles = $this->request->getFileMultiple('photos');
            if (!empty($photoFiles)) {
                $this->handlePhotoUpload($id, $photoFiles);
            }
            
            // Step 6: Upload des nouveaux documents
            $documents = $this->request->getFiles();
            if (!empty($documents['documents'])) {
                $this->handleDocumentUpload($id, $documents['documents']);
            }
            
            $db->transComplete();
            
            if ($db->transStatus() === false) {
                throw new \Exception('Erreur lors de la transaction');
            }
            
            return redirect()->to('/admin/properties')->with('success', 'Bien modifié avec succès');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function delete($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Bien non trouvé');
        }
        
        // Vérifier les permissions : admin ou propriétaire du bien ou manager du propriétaire
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        $editableUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
        
        if ($currentRoleLevel != 100 && !in_array($property['agent_id'], $editableUserIds)) {
            return redirect()->to('/admin/properties')->with('error', 'Vous n\'avez pas la permission de supprimer ce bien.');
        }

        // Supprimer les images associées
        $propertyMediaModel = model('PropertyMediaModel');
        $propertyMediaModel->deletePropertyMedia($id);

        if ($this->propertyModel->delete($id)) {
            return redirect()->to('/admin/properties')->with('success', 'Bien supprimé avec succès');
        }

        return redirect()->to('/admin/properties')->with('error', 'Erreur lors de la suppression');
    }

    public function deleteImage($imageId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requête invalide']);
        }

        $propertyMediaModel = model('PropertyMediaModel');
        
        if ($propertyMediaModel->deleteMediaFile($imageId)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Erreur lors de la suppression']);
    }

    public function view($id)
    {
        $property = $this->propertyModel->getPropertyWithDetails($id);
        
        if (!$property) {
            return redirect()->to(base_url('admin/properties'))->with('error', 'Propriété non trouvée');
        }

        $data = [
            'title' => 'Détails Propriété - ' . $property['reference'],
            'property' => $property
        ];

        return view('admin/properties/view', $data);
    }

    /**
     * Page de gestion des affectations
     */
    public function assignments()
    {
        $userModel = model('UserModel');
        
        // Filtres
        $currentAgency = $this->request->getGet('agency_id');
        $currentAgent = $this->request->getGet('agent_id');
        $status = $this->request->getGet('status');

        $builder = $this->propertyModel
            ->select('properties.*, zones.name as zone_name, 
                     agencies.name as agency_name, 
                     CONCAT(users.first_name, " ", users.last_name) as agent_name')
            ->join('zones', 'zones.id = properties.zone_id', 'left')
            ->join('agencies', 'agencies.id = properties.agency_id', 'left')
            ->join('users', 'users.id = properties.agent_id', 'left');

        if ($currentAgency) {
            $builder->where('properties.agency_id', $currentAgency);
        }
        if ($currentAgent) {
            $builder->where('properties.agent_id', $currentAgent);
        }
        if ($status) {
            $builder->where('properties.status', $status);
        }

        $data = [
            'title' => 'Gestion des Affectations',
            'properties' => $builder->orderBy('properties.created_at', 'DESC')->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->where('status', 'active')->findAll(),
            'currentAgency' => $currentAgency,
            'currentAgent' => $currentAgent,
            'currentStatus' => $status
        ];

        return view('admin/properties/assignments', $data);
    }

    /**
     * Réassigner des biens en masse
     */
    public function reassign()
    {
        $propertyIds = $this->request->getPost('property_ids');
        $newAgencyId = $this->request->getPost('new_agency_id');
        $newAgentId = $this->request->getPost('new_agent_id');

        if (!$propertyIds || (!$newAgencyId && !$newAgentId)) {
            return redirect()->back()->with('error', 'Veuillez sélectionner des biens et au moins une nouvelle affectation');
        }

        $updated = 0;
        foreach ($propertyIds as $propertyId) {
            $data = [];
            if ($newAgencyId) {
                $data['agency_id'] = $newAgencyId;
            }
            if ($newAgentId) {
                $data['agent_id'] = $newAgentId;
            }
            
            if ($this->propertyModel->update($propertyId, $data)) {
                $updated++;
            }
        }

        return redirect()->to(base_url('admin/properties/assignments'))
            ->with('success', "$updated bien(s) réaffecté(s) avec succès");
    }
}
