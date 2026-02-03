<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $propertyModel = model('PropertyModel');
        
        $data = [
            'title' => 'REBENCIA REAL ESTATE - Votre partenaire immobilier en Tunisie',
            'featured_properties' => $propertyModel->where('featured', 1)
                                                    ->where('status', 'published')
                                                    ->limit(6)
                                                    ->findAll(),
            'latest_properties' => $propertyModel->where('status', 'published')
                                                  ->orderBy('created_at', 'DESC')
                                                  ->limit(8)
                                                  ->findAll()
        ];

        return view('public/home', $data);
    }
}
