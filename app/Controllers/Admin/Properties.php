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
            'title' => $this->request->getPost('title_fr'),
            'title_ar' => $this->request->getPost('title_ar'),
            'title_en' => $this->request->getPost('title_en'),
            'description' => $this->request->getPost('description_fr'),
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
        $propertyMediaModel = model('PropertyMediaModel');
        $uploadPath = FCPATH . 'uploads/properties';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                
                $propertyMediaModel->insert([
                    'property_id' => $propertyId,
                    'type' => 'image',
                    'file_name' => $file->getClientName(),
                    'file_path' => $newName,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }
    }
    
    private function handleDocumentUpload($propertyId, $documentsByType)
    {
        $propertyDocumentModel = model('PropertyDocumentModel');
        $uploadPath = FCPATH . 'uploads/documents';
        
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
                        'file_path' => $newName,
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
        $propertyMediaModel = model('PropertyMediaModel');

        $data = [
            'title' => 'Modifier le Bien - ' . $property['reference'],
            'property' => $property,
            'rooms' => $propertyRoomModel->getRoomsByProperty($id),
            'proximities' => $propertyProximityModel->getProximitiesByProperty($id),
            'documents' => $propertyDocumentModel->getDocumentsByProperty($id),
            'photos' => $propertyMediaModel->getPropertyImages($id),
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
            'title' => $this->request->getPost('title_fr'),
            'title_ar' => $this->request->getPost('title_ar'),
            'title_en' => $this->request->getPost('title_en'),
            'description' => $this->request->getPost('description_fr'),
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
            $updateResult = $this->propertyModel->update($id, $data);
            
            if ($updateResult === false) {
                $errors = $this->propertyModel->errors();
                log_message('error', 'Property update failed: ' . json_encode($errors));
                throw new \Exception('Erreur lors de la mise à jour: ' . json_encode($errors));
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

    public function deleteDocument($documentId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requête invalide']);
        }

        $propertyDocumentModel = model('PropertyDocumentModel');
        $document = $propertyDocumentModel->find($documentId);
        
        if (!$document) {
            return $this->response->setJSON(['success' => false, 'message' => 'Document non trouvé']);
        }
        
        // Supprimer le fichier physique
        $filePath = FCPATH . 'uploads/documents/' . $document['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        // Supprimer de la base de données
        if ($propertyDocumentModel->delete($documentId)) {
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

        // Vérifier les permissions d'édition
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        // Récupérer les IDs des utilisateurs modifiables (pour vérifier les permissions)
        $editableUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
        if (empty($editableUserIds)) {
            $editableUserIds = [$currentUserId];
        }
        
        // L'utilisateur peut modifier si admin ou si c'est son bien ou le bien d'un subordonné
        $canEdit = ($currentRoleLevel == 100 || in_array($property['agent_id'], $editableUserIds));

        // Récupérer les documents
        $documentModel = model('PropertyDocumentModel');
        $documents = $documentModel->where('property_id', $id)->findAll();

        $data = [
            'title' => 'Détails Propriété - ' . $property['reference'],
            'property' => $property,
            'documents' => $documents,
            'canEdit' => $canEdit,
            'currentRoleLevel' => $currentRoleLevel
        ];

        return view('admin/properties/view', $data);
    }

    /**
     * Page de gestion des affectations
     */
    /**
     * ANCIENNE MÉTHODE - SUPPRIMÉE
     * Remplacée par bulkManage()
     */
    /*
    public function assignments()
    {
        return redirect()->to('/admin/properties/bulk-manage');
    }
    */

    /**
     * ANCIENNE MÉTHODE - SUPPRIMÉE
     * Remplacée par bulkAction()
     */
    /*
    public function reassign()
    {
        return redirect()->to('/admin/properties/bulk-manage');
    }
    */

    /**
     * Page de gestion en masse des biens
     */
    public function bulkManage()
    {
        // Seulement l'admin peut accéder
        if (session()->get('role_level') != 100) {
            return redirect()->to('/admin/properties')->with('error', 'Accès non autorisé.');
        }

        $userModel = model('UserModel');
        
        $properties = $this->propertyModel
            ->select('properties.*, zones.name as zone_name, 
                     agencies.name as agency_name, 
                     CONCAT(users.first_name, " ", users.last_name) as agent_name,
                     users.id as user_id')
            ->join('zones', 'zones.id = properties.zone_id', 'left')
            ->join('agencies', 'agencies.id = properties.agency_id', 'left')
            ->join('users', 'users.id = properties.agent_id', 'left')
            ->orderBy('properties.created_at', 'DESC')
            ->findAll();

        $data = [
            'title' => 'Gestion en masse des biens',
            'properties' => $properties,
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->where('status', 'active')->findAll()
        ];

        return view('admin/properties/bulk_manage', $data);
    }

    /**
     * Exécuter une action en masse
     */
    public function bulkAction()
    {
        // Seulement l'admin peut exécuter
        if (session()->get('role_level') != 100) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé.'
            ]);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/properties/bulk-manage');
        }

        $json = $this->request->getJSON();
        $propertyIds = $json->property_ids ?? [];
        $action = $json->action ?? '';
        $value = $json->value ?? null;

        if (empty($propertyIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucun bien sélectionné'
            ]);
        }

        try {
            $affected = 0;
            
            switch ($action) {
                case 'change_status':
                    foreach ($propertyIds as $id) {
                        if ($this->propertyModel->update($id, ['status' => $value])) {
                            $affected++;
                        }
                    }
                    $message = "$affected bien(s) - Statut changé en '$value'";
                    break;

                case 'change_agency':
                    foreach ($propertyIds as $id) {
                        if ($this->propertyModel->update($id, ['agency_id' => $value])) {
                            $affected++;
                        }
                    }
                    $message = "$affected bien(s) réaffecté(s) à la nouvelle agence";
                    break;

                case 'change_agent':
                    foreach ($propertyIds as $id) {
                        if ($this->propertyModel->update($id, ['agent_id' => $value])) {
                            $affected++;
                        }
                    }
                    $message = "$affected bien(s) réaffecté(s) au nouvel agent";
                    break;

                case 'set_featured':
                    foreach ($propertyIds as $id) {
                        if ($this->propertyModel->update($id, ['featured' => 1])) {
                            $affected++;
                        }
                    }
                    $message = "$affected bien(s) mis en vedette";
                    break;

                case 'unset_featured':
                    foreach ($propertyIds as $id) {
                        if ($this->propertyModel->update($id, ['featured' => 0])) {
                            $affected++;
                        }
                    }
                    $message = "$affected bien(s) retirés de la vedette";
                    break;

                case 'delete':
                    foreach ($propertyIds as $id) {
                        if ($this->propertyModel->delete($id)) {
                            $affected++;
                        }
                    }
                    $message = "$affected bien(s) supprimé(s)";
                    break;

                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Action non reconnue'
                    ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Recherche de propriétaires (clients)
     */
    public function searchOwners()
    {
        $query = $this->request->getGet('q');
        
        if (!$query || strlen($query) < 3) {
            return $this->response->setJSON([
                'success' => false,
                'owners' => []
            ]);
        }

        // Rechercher dans la table clients
        $clientModel = model('ClientModel');
        $clients = $clientModel
            ->select('id, type, first_name, last_name, company_name, email, phone, phone_secondary')
            ->groupStart()
                ->like('first_name', $query)
                ->orLike('last_name', $query)
                ->orLike('company_name', $query)
                ->orLike('email', $query)
                ->orLike('phone', $query)
                ->orLike('phone_secondary', $query)
            ->groupEnd()
            ->limit(10)
            ->findAll();

        // Formater les résultats
        $owners = [];
        foreach ($clients as $client) {
            $name = $client['type'] === 'company' 
                ? $client['company_name'] 
                : trim($client['first_name'] . ' ' . $client['last_name']);
            
            $owners[] = [
                'id' => $client['id'],
                'type' => $client['type'],
                'name' => $name,
                'phone' => $client['phone'],
                'phone_secondary' => $client['phone_secondary'],
                'email' => $client['email'] ?: ''
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'owners' => $owners
        ]);
    }

    /**
     * Mise à jour des informations propriétaire
     */
    public function updateOwner($id = null)
    {
        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID du bien manquant'
            ]);
        }

        // Vérifier les permissions
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        $property = $this->propertyModel->find($id);
        if (!$property) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bien non trouvé'
            ]);
        }

        // Vérifier les droits d'édition
        if ($currentRoleLevel != 100) {
            $editableUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
            if (!in_array($property['agent_id'], $editableUserIds)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Vous n\'avez pas l\'autorisation de modifier ce bien'
                ]);
            }
        }

        $clientId = $this->request->getPost('client_id');
        
        // Si un client existant est sélectionné
        if ($clientId) {
            $clientModel = model('ClientModel');
            $client = $clientModel->find($clientId);
            
            if (!$client) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Client non trouvé'
                ]);
            }
            
            // Utiliser les infos du client
            $ownerName = $client['type'] === 'company' 
                ? $client['company_name'] 
                : trim($client['first_name'] . ' ' . $client['last_name']);
            $ownerPhone = $client['phone'];
            $ownerEmail = $client['email'];
            
        } else {
            // Nouveau client à créer
            $ownerName = $this->request->getPost('owner_name');
            $ownerPhone = $this->request->getPost('owner_phone');
            $ownerEmail = $this->request->getPost('owner_email');
            $clientType = $this->request->getPost('client_type') ?: 'individual';

            if (!$ownerName || !$ownerPhone) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Le nom et le téléphone du propriétaire sont obligatoires'
                ]);
            }
            
            // Créer le client
            $clientModel = model('ClientModel');
            $clientData = [
                'type' => $clientType,
                'phone' => $ownerPhone,
                'email' => $ownerEmail,
                'status' => 'owner',
                'assigned_to' => $property['agent_id'],
                'agency_id' => $property['agency_id']
            ];
            
            if ($clientType === 'company') {
                $clientData['company_name'] = $ownerName;
            } else {
                // Séparer prénom et nom
                $nameParts = explode(' ', $ownerName, 2);
                $clientData['first_name'] = $nameParts[0];
                $clientData['last_name'] = $nameParts[1] ?? '';
            }
            
            $clientId = $clientModel->insert($clientData);
            
            if (!$clientId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de la création du client'
                ]);
            }
        }

        // Mettre à jour le bien avec les infos du propriétaire
        $updated = $this->propertyModel->update($id, [
            'owner_name' => $ownerName,
            'owner_phone' => $ownerPhone,
            'owner_email' => $ownerEmail
        ]);

        if ($updated) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Propriétaire mis à jour avec succès'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ]);
        }
    }
}

