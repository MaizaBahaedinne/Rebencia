<?php

namespace App\Models;

use CodeIgniter\Model;

class AgencyModel extends Model
{
    protected $table = 'agencies';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'code', 'type', 'parent_id', 'address', 'city', 'governorate',
        'postal_code', 'phone', 'email', 'website', 'latitude', 'longitude',
        'logo', 'status'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[200]',
        'code' => 'permit_empty|is_unique[agencies.code,id,{id}]',
        'type' => 'required|in_list[siege,agence]',
        'phone' => 'permit_empty|max_length[50]',
        'email' => 'permit_empty|valid_email',
    ];

    public function getAgenciesWithStats()
    {
        return $this->select('agencies.*, 
            COUNT(DISTINCT users.id) as users_count,
            COUNT(DISTINCT properties.id) as properties_count')
            ->join('users', 'users.agency_id = agencies.id', 'left')
            ->join('properties', 'properties.agency_id = agencies.id', 'left')
            ->groupBy('agencies.id')
            ->findAll();
    }

    public function getAgencyHierarchy($agencyId)
    {
        return $this->where('parent_id', $agencyId)
            ->where('status', 'active')
            ->findAll();
    }
}
