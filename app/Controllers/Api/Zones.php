<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class Zones extends ResourceController
{
    protected $modelName = 'App\Models\ZoneModel';
    protected $format = 'json';

    /**
     * Get all cities with their governorates
     */
    public function cities()
    {
        $zoneModel = model('ZoneModel');
        
        // Get all cities with their governorate information
        $cities = $zoneModel
            ->select('zones.id, zones.name, zones.name_ar, zones.name_en, parent.name as governorate, parent.name_ar as governorate_ar')
            ->join('zones as parent', 'parent.id = zones.parent_id', 'left')
            ->where('zones.type', 'city')
            ->orderBy('zones.popularity_score', 'DESC')
            ->orderBy('zones.name', 'ASC')
            ->findAll();
        
        return $this->respond($cities);
    }

    /**
     * Get all governorates
     */
    public function governorates()
    {
        $zoneModel = model('ZoneModel');
        
        $governorates = $zoneModel
            ->where('type', 'governorate')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->respond($governorates);
    }

    /**
     * Get cities by governorate
     */
    public function citiesByGovernorate($governorateId = null)
    {
        if (!$governorateId) {
            return $this->fail('Governorate ID required');
        }

        $zoneModel = model('ZoneModel');
        
        $cities = $zoneModel
            ->where('type', 'city')
            ->where('parent_id', $governorateId)
            ->orderBy('popularity_score', 'DESC')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        return $this->respond($cities);
    }
}
