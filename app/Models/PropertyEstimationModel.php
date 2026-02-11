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
        'client_id', 'first_name', 'last_name', 'email', 'phone',
        'property_type', 'transaction_type', 'address', 'city', 'governorate', 'zone_id',
        'area_total', 'rooms', 'bedrooms', 'bathrooms', 'floor',
        'construction_year', 'condition_state',
        'has_elevator', 'has_parking', 'has_garden',
        'description', 'estimated_price', 'agent_id', 'status', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'has_elevator' => 'boolean',
        'has_parking' => 'boolean',
        'has_garden' => 'boolean',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'email' => 'required|valid_email|max_length[255]',
        'property_type' => 'required|in_list[apartment,villa,studio,office,shop,warehouse,land,other]',
        'transaction_type' => 'required|in_list[sale,rent]',
    ];

    protected $validationMessages = [
        'first_name' => [
            'required' => 'Le prénom est obligatoire',
        ],
        'last_name' => [
            'required' => 'Le nom est obligatoire',
        ],
        'email' => [
            'required' => 'L\'email est obligatoire',
            'valid_email' => 'L\'email doit être valide',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Get estimations with related data
    public function getEstimationsWithDetails($limit = null, $offset = null)
    {
        $builder = $this->select('property_estimations.*, 
                                  clients.first_name as client_first_name, 
                                  clients.last_name as client_last_name,
                                  users.first_name as agent_first_name,
                                  users.last_name as agent_last_name,
                                  zones.name as zone_name')
                        ->join('clients', 'clients.id = property_estimations.client_id', 'left')
                        ->join('users', 'users.id = property_estimations.agent_id', 'left')
                        ->join('zones', 'zones.id = property_estimations.zone_id', 'left')
                        ->orderBy('property_estimations.created_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    // Get statistics by status
    public function getStatsByStatus()
    {
        return $this->select('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->get()
                    ->getResultArray();
    }
}
