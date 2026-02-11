<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyEstimationModel extends Model
{
    protected $table = 'property_estimations';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'client_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'property_type',
        'transaction_type',
        'address',
        'city',
        'governorate',
        'zone_id',
        'area_total',
        'rooms',
        'bedrooms',
        'bathrooms',
        'floor',
        'construction_year',
        'condition_state',
        'has_elevator',
        'has_parking',
        'has_garden',
        'description',
        'estimated_price',
        'status',
        'agent_id',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    protected $validationRules = [
        'client_id' => 'required|integer',
        'property_type' => 'required|in_list[apartment,villa,house,land,commercial,industrial,office]',
        'transaction_type' => 'required|in_list[sale,rent]',
    ];

    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get estimations with client and agent information
     */
    public function getEstimationsWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('property_estimations.*, 
                         users.first_name as agent_first_name,
                         users.last_name as agent_last_name,
                         zones.name as zone_name');
        $builder->join('users', 'users.id = property_estimations.agent_id', 'left');
        $builder->join('zones', 'zones.id = property_estimations.zone_id', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('property_estimations.status', $filters['status']);
        }

        if (!empty($filters['property_type'])) {
            $builder->where('property_estimations.property_type', $filters['property_type']);
        }

        if (!empty($filters['transaction_type'])) {
            $builder->where('property_estimations.transaction_type', $filters['transaction_type']);
        }

        if (!empty($filters['city'])) {
            $builder->like('property_estimations.city', $filters['city']);
        }

        if (!empty($filters['governorate'])) {
            $builder->like('property_estimations.governorate', $filters['governorate']);
        }

        if (!empty($filters['agent_id'])) {
            $builder->where('property_estimations.agent_id', $filters['agent_id']);
        }

        $builder->orderBy('property_estimations.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        return [
            'total' => $this->countAllResults(false),
            'pending' => $this->where('status', 'pending')->countAllResults(false),
            'in_progress' => $this->where('status', 'in_progress')->countAllResults(false),
            'estimated' => $this->where('status', 'estimated')->countAllResults(false),
            'contacted' => $this->where('status', 'contacted')->countAllResults(false),
            'converted' => $this->where('status', 'converted')->countAllResults(false),
            'cancelled' => $this->where('status', 'cancelled')->countAllResults(false),
        ];
    }
}
