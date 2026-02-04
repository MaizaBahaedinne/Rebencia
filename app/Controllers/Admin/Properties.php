<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Properties extends BaseController
{
    protected $propertyModel;
    protected $zoneModel;
    protected $agencyModel;

    public function __construct()
    {
        $this->propertyModel = model('PropertyModel');
        $this->zoneModel = model('ZoneModel');
        $this->agencyModel = model('AgencyModel');
    }

    public function index()
    {
        $data = [
            'title' => 'Gestion des Propriétés',
            'properties' => $this->propertyModel->select('properties.*, zones.name as zone_name, users.first_name as agent_name')
                ->join('zones', 'zones.id = properties.zone_id', 'left')
                ->join('users', 'users.id = properties.agent_id', 'left')
                ->orderBy('properties.created_at', 'DESC')
                ->paginate(20)
        ];

        return view('admin/properties/index', $data);
    }

    public function create()
    {
        $userModel = model('UserModel');
        
        $data = [
            'title' => 'Nouvelle Propriété',
            'zones' => $this->zoneModel->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->findAll() // Agents immobiliers
        ];

        return view('admin/properties/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'type' => 'required|in_list[apartment,villa,house,land,commercial,office]',
            'transaction_type' => 'required|in_list[sale,rent,both]',
            'area' => 'required|decimal',
            'zone_id' => 'required|is_natural_no_zero',
            'address' => 'required|min_length[5]',
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
            'reference' => $reference,
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'price' => $this->request->getPost('price') ?: null,
            'rental_price' => $this->request->getPost('rent_price') ?: null,
            'area' => $this->request->getPost('area'),
            'bedrooms' => $this->request->getPost('bedrooms') ?: 0,
            'bathrooms' => $this->request->getPost('bathrooms') ?: 0,
            'floor' => $this->request->getPost('floor'),
            'year_built' => $this->request->getPost('year_built'),
            'parking' => $this->request->getPost('parking') ?: 0,
            'furnished' => $this->request->getPost('furnished') ?: 0,
            'zone_id' => $this->request->getPost('zone_id'),
            'address' => $this->request->getPost('address'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'agency_id' => $this->request->getPost('agency_id') ?: null,
            'agent_id' => $this->request->getPost('agent_id') ?: session()->get('user_id'),
            'featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'status' => $this->request->getPost('is_published') ? 'published' : 'draft'
        ];

        // Gestion des images (à implémenter)
        $images = $this->request->getFiles();
        
        if ($propertyId = $this->propertyModel->insert($data)) {
            // Upload des images
            if (!empty($images['images'])) {
                $this->handleImageUpload($propertyId, $images['images']);
            }
            
            // Trigger notifications
            $notificationHelper = new \App\Libraries\NotificationHelper();
            $notificationHelper->notifyPropertyCreated($propertyId, $data, session()->get('user_id'));
            $notificationHelper->checkPropertyClientMatches($propertyId, $data);
            
            return redirect()->to('/admin/properties')->with('success', 'Bien immobilier créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du bien');
    }
    
    private function handleImageUpload($propertyId, $files)
    {
        $propertyMediaModel = model('PropertyMediaModel');
        $uploadPath = WRITEPATH . 'uploads/properties/' . $propertyId;
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $order = 1;
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                
                $propertyMediaModel->insert([
                    'property_id' => $propertyId,
                    'type' => 'image',
                    'file_path' => 'uploads/properties/' . $propertyId . '/' . $newName,
                    'display_order' => $order++,
                    'is_primary' => ($order == 2) ? 1 : 0 // Premier fichier = image principale
                ]);
            }
        }
    }

    public function edit($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Bien non trouvé');
        }

        $userModel = model('UserModel');
        $propertyMediaModel = model('PropertyMediaModel');

        $data = [
            'title' => 'Modifier le Bien',
            'property' => $property,
            'zones' => $this->zoneModel->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'agents' => $userModel->where('role_id >=', 6)->findAll(),
            'property_images' => $propertyMediaModel->getPropertyImages($id)
        ];

        return view('admin/properties/edit', $data);
    }

    public function update($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Bien non trouvé');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'description' => 'required|min_length[10]',
            'type' => 'required|in_list[apartment,villa,house,land,commercial,office]',
            'transaction_type' => 'required|in_list[sale,rent,both]',
            'area' => 'required|decimal',
            'zone_id' => 'required|is_natural_no_zero',
            'address' => 'required|min_length[5]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'transaction_type' => $this->request->getPost('transaction_type'),
            'price' => $this->request->getPost('price') ?: null,
            'rental_price' => $this->request->getPost('rent_price') ?: null,
            'area' => $this->request->getPost('area'),
            'bedrooms' => $this->request->getPost('bedrooms') ?: 0,
            'bathrooms' => $this->request->getPost('bathrooms') ?: 0,
            'floor' => $this->request->getPost('floor'),
            'year_built' => $this->request->getPost('year_built'),
            'parking' => $this->request->getPost('parking') ?: 0,
            'furnished' => $this->request->getPost('furnished') ?: 0,
            'zone_id' => $this->request->getPost('zone_id'),
            'address' => $this->request->getPost('address'),
            'latitude' => $this->request->getPost('latitude'),
            'longitude' => $this->request->getPost('longitude'),
            'agency_id' => $this->request->getPost('agency_id') ?: null,
            'agent_id' => $this->request->getPost('agent_id') ?: session()->get('user_id'),
            'featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'status' => $this->request->getPost('is_published') ? 'published' : 'draft'
        ];

        if ($this->propertyModel->update($id, $data)) {
            // Upload des nouvelles images
            $newImages = $this->request->getFiles();
            if (!empty($newImages['new_images'])) {
                $this->handleImageUpload($id, $newImages['new_images']);
            }
            
            return redirect()->to('/admin/properties')->with('success', 'Bien modifié avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
    }

    public function delete($id)
    {
        $property = $this->propertyModel->find($id);
        
        if (!$property) {
            return redirect()->to('/admin/properties')->with('error', 'Bien non trouvé');
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
        $data = [
            'title' => 'Détails Propriété',
            'property' => $this->propertyModel->getPropertyWithDetails($id)
        ];

        return view('admin/properties/view', $data);
    }
}
