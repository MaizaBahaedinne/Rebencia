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
            'area_min' => $this->request->getGet('area_min'),
            'area_max' => $this->request->getGet('area_max'),
            'bedrooms_min' => $this->request->getGet('bedrooms_min'),
            'bathrooms_min' => $this->request->getGet('bathrooms_min'),
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
        
        if (!empty($filters['area_min'])) {
            $query->where('surface >=', $filters['area_min']);
        }
        
        if (!empty($filters['area_max'])) {
            $query->where('surface <=', $filters['area_max']);
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
