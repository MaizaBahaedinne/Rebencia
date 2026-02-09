<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $propertyModel = model('PropertyModel');
        $mediaModel = model('PropertyMediaModel');
        
        // Get featured properties
        $featured_properties = $propertyModel->where('featured', 1)
                                              ->where('status', 'published')
                                              ->limit(6)
                                              ->findAll();
        
        // Add main image to each featured property
        foreach ($featured_properties as &$property) {
            $mainImage = $mediaModel
                ->where('property_id', $property['id'])
                ->where('type', 'photo')
                ->orderBy('is_main', 'DESC')
                ->orderBy('display_order', 'ASC')
                ->first();
            $property['main_image'] = $mainImage;
        }
        unset($property);
        
        // Get latest properties
        $latest_properties = $propertyModel->where('status', 'published')
                                            ->orderBy('created_at', 'DESC')
                                            ->limit(8)
                                            ->findAll();
        
        // Add main image to each latest property
        foreach ($latest_properties as &$property) {
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
            'title' => 'REBENCIA REAL ESTATE - Votre partenaire immobilier en Tunisie',
            'featured_properties' => $featured_properties,
            'latest_properties' => $latest_properties
        ];

        return view('public/home', $data);
    }
    
    public function getCities()
    {
        $propertyModel = model('PropertyModel');
        
        // Get unique cities from properties table
        $cities = $propertyModel
            ->select('city')
            ->where('status', 'published')
            ->where('city IS NOT NULL')
            ->where('city !=', '')
            ->groupBy('city')
            ->orderBy('city', 'ASC')
            ->findAll();
        
        $cityList = array_column($cities, 'city');
        
        return $this->response->setJSON($cityList);
    }
}
