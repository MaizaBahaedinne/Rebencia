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
            'similar_properties' => $similarProperties
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
}
