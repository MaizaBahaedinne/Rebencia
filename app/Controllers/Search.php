<?php

namespace App\Controllers;

class Search extends BaseController
{
    public function index()
    {
        $propertyModel = model('PropertyModel');
        $mediaModel = model('PropertyMediaModel');
        
        // Get search parameters
        $filters = [
            'transaction_type' => $this->request->getGet('transaction_type'),
            'type' => $this->request->getGet('type'),
            'city' => $this->request->getGet('city'),
            'governorate' => $this->request->getGet('governorate'),
            'price_min' => $this->request->getGet('price_min'),
            'price_max' => $this->request->getGet('price_max'),
            'price_max_advanced' => $this->request->getGet('price_max_advanced'),
            'surface_min' => $this->request->getGet('surface_min'),
            'surface_max' => $this->request->getGet('surface_max'),
            'bedrooms_min' => $this->request->getGet('bedrooms_min'),
            'bathrooms_min' => $this->request->getGet('bathrooms_min'),
            'amenities' => $this->request->getGet('amenities'),
            'reference' => $this->request->getGet('reference')
        ];
        
        // Build query
        $query = $propertyModel->where('status', 'published');
        
        // Apply filters
        if (!empty($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }
        
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        
        if (!empty($filters['city'])) {
            $query->like('city', $filters['city']);
        }
        
        if (!empty($filters['governorate'])) {
            $query->where('governorate', $filters['governorate']);
        }
        
        if (!empty($filters['price_min'])) {
            $query->where('price >=', $filters['price_min']);
        }
        
        if (!empty($filters['price_max'])) {
            $query->where('price <=', $filters['price_max']);
        }
        
        if (!empty($filters['price_max_advanced'])) {
            $query->where('price <=', $filters['price_max_advanced']);
        }
        
        if (!empty($filters['surface_min'])) {
            $query->where('area_total >=', $filters['surface_min']);
        }
        
        if (!empty($filters['surface_max'])) {
            $query->where('area_total <=', $filters['surface_max']);
        }
        
        // Amenities filters
        if (!empty($filters['amenities']) && is_array($filters['amenities'])) {
            foreach ($filters['amenities'] as $amenity) {
                switch($amenity) {
                    case 'parking':
                        $query->where('has_parking', 1);
                        break;
                    case 'piscine':
                        $query->where('has_pool', 1);
                        break;
                    case 'jardin':
                        $query->where('has_garden', 1);
                        break;
                    case 'ascenseur':
                        $query->where('has_elevator', 1);
                        break;
                }
            }
        }
        
        if (!empty($filters['bedrooms_min'])) {
            $query->where('bedrooms >=', $filters['bedrooms_min']);
        }
        
        if (!empty($filters['bathrooms_min'])) {
            $query->where('bathrooms >=', $filters['bathrooms_min']);
        }
        
        if (!empty($filters['reference'])) {
            $query->like('reference', $filters['reference']);
        }
        
        // Get results
        $properties = $query->orderBy('created_at', 'DESC')->findAll();
        
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
            'title' => 'Résultats de recherche - REBENCIA',
            'properties' => $properties,
            'filters' => $filters,
            'total' => count($properties)
        ];
        
        return view('public/search_results', $data);
    }
}
