<?php

namespace App\Controllers;

class Properties extends BaseController
{
    public function view($reference)
    {
        $propertyModel = model('PropertyModel');
        $mediaModel = model('PropertyMediaModel');
        $roomModel = model('PropertyRoomModel');
        $proximityModel = model('PropertyProximityModel');
        $userModel = model('UserModel');
        $agencyModel = model('AgencyModel');
        
        // Get property by reference
        $property = $propertyModel->where('reference', $reference)
                                  ->where('status', 'published')
                                  ->first();
        
        if (!$property) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Propriété non trouvée');
        }
        
        // Get property images
        $images = $mediaModel->where('property_id', $property['id'])
                            ->where('type', 'photo')
                            ->orderBy('is_main', 'DESC')
                            ->orderBy('display_order', 'ASC')
                            ->findAll();
        
        // Get property rooms
        $rooms = $roomModel->where('property_id', $property['id'])->findAll();
        
        // Get property proximities
        $proximities = $proximityModel->where('property_id', $property['id'])->findAll();
        
        // Get assigned agent information
        $agent = null;
        if (!empty($property['assigned_to'])) {
            $agent = $userModel->find($property['assigned_to']);
        }
        
        // Get agency information
        $agency = null;
        if (!empty($property['agency_id'])) {
            $agency = $agencyModel->find($property['agency_id']);
        }
        
        // Get similar properties (same type and city)
        $similarProperties = $propertyModel
            ->where('status', 'published')
            ->where('type', $property['type'])
            ->where('city', $property['city'])
            ->where('id !=', $property['id'])
            ->limit(3)
            ->findAll();
        
        // Add main image to similar properties
        foreach ($similarProperties as &$simProp) {
            $mainImage = $mediaModel
                ->where('property_id', $simProp['id'])
                ->where('type', 'photo')
                ->orderBy('is_main', 'DESC')
                ->orderBy('display_order', 'ASC')
                ->first();
            $simProp['main_image'] = $mainImage;
        }
        unset($simProp);
        
        $data = [
            'title' => $property['title'] . ' - REBENCIA',
            'property' => $property,
            'images' => $images,
            'rooms' => $rooms,
            'proximities' => $proximities,
            'similar_properties' => $similarProperties,
            'agent' => $agent,
            'agency' => $agency
        ];
        
        return view('public/property_detail', $data);
    }
    
    public function index()
    {
        $propertyModel = model('PropertyModel');
        $mediaModel = model('PropertyMediaModel');
        
        // Get all published properties
        $properties = $propertyModel->where('status', 'published')
                                    ->orderBy('created_at', 'DESC')
                                    ->findAll();
        
        // Add main image to each property
        foreach ($properties as &$property) {
            $mainImage = $mediaModel
                ->where('property_id', $property['id'])
                ->where('type', 'photo')
                ->orderBy('is_main', 'DESC')
                ->orderBy('display_order', 'ASC')
                ->first();
            $property['main_image'] = $mainImage;
        }
        unset($property);
        
        $data = [
            'title' => 'Toutes les propriétés - REBENCIA',
            'properties' => $properties
        ];
        
        return view('public/properties_list', $data);
    }
    
    public function submitRequest()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requête invalide']);
        }
        
        $clientModel = model('ClientModel');
        $requestModel = model('PropertyRequestModel');
        
        // Validation des données
        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');
        $propertyId = $this->request->getPost('property_id');
        $requestType = $this->request->getPost('request_type');
        $message = $this->request->getPost('message');
        
        if (empty($name) || empty($phone) || empty($propertyId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Le nom et le téléphone sont obligatoires'
            ]);
        }
        
        // Vérifier si le client existe déjà (par téléphone)
        $existingClient = $clientModel->where('phone', $phone)->first();
        
        if ($existingClient) {
            $clientId = $existingClient['id'];
            
            // Mettre à jour les préférences basées sur la propriété actuelle
            $this->updateClientPreferences($clientId, $existingClient);
        } else {
            // Séparer le nom en prénom et nom de famille
            $nameParts = explode(' ', $name, 2);
            $firstName = $nameParts[0];
            $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
            
            // Créer un nouveau client avec les préférences
            $clientData = [
                'type' => 'individual',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'email' => $email,
                'source' => 'website',
                'status' => 'lead',
                // Préférences basées sur la propriété consultée
                'property_type_preference' => $this->request->getPost('property_type'),
                'transaction_type_preference' => $this->request->getPost('property_transaction_type'),
                'budget_min' => $this->request->getPost('property_price') ? $this->request->getPost('property_price') * 0.8 : null, // -20%
                'budget_max' => $this->request->getPost('property_price') ? $this->request->getPost('property_price') * 1.2 : null, // +20%
                'preferred_zones' => json_encode([
                    'city' => $this->request->getPost('property_city'),
                    'governorate' => $this->request->getPost('property_governorate')
                ]),
                'area_preference' => $this->request->getPost('property_area') ? json_encode([
                    'min' => $this->request->getPost('property_area') * 0.8,
                    'max' => $this->request->getPost('property_area') * 1.2,
                    'bedrooms_min' => $this->request->getPost('property_bedrooms'),
                    'bathrooms_min' => $this->request->getPost('property_bathrooms')
                ]) : null,
            ];
            
            try {
                $clientId = $clientModel->insert($clientData);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de la création du client. Veuillez réessayer.'
                ]);
            }
        }
        
        // Enregistrer la demande
        $requestData = [
            'property_id' => $propertyId,
            'client_id' => $clientId,
            'request_type' => $requestType,
            'message' => $message ?? '',  // Ensure message is never null
            'status' => 'pending',
            'source' => 'website'
        ];
        
        // Ajouter les infos spécifiques selon le type
        if ($requestType === 'visit') {
            $requestData['visit_date'] = $this->request->getPost('visit_date');
            $requestData['visit_time'] = $this->request->getPost('visit_time');
        }
        
        try {
            $requestId = $requestModel->insert($requestData);
            
            if (!$requestId) {
                log_message('error', 'Failed to insert property request: ' . json_encode($requestModel->errors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de l\'enregistrement de la demande. Veuillez réessayer.'
                ]);
            }
            
            log_message('info', 'Property request created successfully: ID=' . $requestId);
        } catch (\Exception $e) {
            log_message('error', 'Exception while inserting property request: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur technique. Veuillez réessayer plus tard.'
            ]);
        }
        
        // Message de succès selon le type
        $successMessage = $requestType === 'visit' 
            ? 'Votre demande de visite a été envoyée avec succès ! Nous vous contacterons bientôt.'
            : 'Votre demande d\'information a été envoyée avec succès ! Nous vous répondrons dans les plus brefs délais.';
        
        return $this->response->setJSON([
            'success' => true,
            'message' => $successMessage,
            'client_id' => $clientId
        ]);
    }
    
    private function updateClientPreferences($clientId, $currentData)
    {
        $clientModel = model('ClientModel');
        
        // Ne mettre à jour que si les préférences ne sont pas déjà définies
        $updateData = [];
        
        if (empty($currentData['property_type_preference'])) {
            $updateData['property_type_preference'] = $this->request->getPost('property_type');
        }
        
        if (empty($currentData['transaction_type_preference'])) {
            $updateData['transaction_type_preference'] = $this->request->getPost('property_transaction_type');
        }
        
        if (empty($currentData['preferred_zones'])) {
            $updateData['preferred_zones'] = json_encode([
                'city' => $this->request->getPost('property_city'),
                'governorate' => $this->request->getPost('property_governorate')
            ]);
        }
        
        // Ajuster le budget si non défini
        if (empty($currentData['budget_min']) && empty($currentData['budget_max'])) {
            $price = $this->request->getPost('property_price');
            if ($price) {
                $updateData['budget_min'] = $price * 0.8;
                $updateData['budget_max'] = $price * 1.2;
            }
        }
        
        // Ajouter les préférences de surface si non définies
        if (empty($currentData['area_preference'])) {
            $area = $this->request->getPost('property_area');
            if ($area) {
                $updateData['area_preference'] = json_encode([
                    'min' => $area * 0.8,
                    'max' => $area * 1.2,
                    'bedrooms_min' => $this->request->getPost('property_bedrooms'),
                    'bathrooms_min' => $this->request->getPost('property_bathrooms')
                ]);
            }
        }
        
        if (!empty($updateData)) {
            $clientModel->update($clientId, $updateData);
        }
    }
}
