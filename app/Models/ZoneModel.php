<?php

namespace App\Models;

use CodeIgniter\Model;

class ZoneModel extends Model
{
    protected $table = 'zones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'name_ar', 'name_en', 'type', 'parent_id', 'country',
        'latitude', 'longitude', 'popularity_score'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getGovernorates()
    {
        return $this->where('type', 'governorate')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getCitiesByGovernorate($governorateId)
    {
        return $this->where('type', 'city')
            ->where('parent_id', $governorateId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    public function getZoneHierarchy($zoneId = null)
    {
        if ($zoneId) {
            return $this->where('parent_id', $zoneId)->findAll();
        }
        return $this->where('parent_id IS NULL')->findAll();
    }

    public function getPopularZones($limit = 10)
    {
        return $this->orderBy('popularity_score', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
