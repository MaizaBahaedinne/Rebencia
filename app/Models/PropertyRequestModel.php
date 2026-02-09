<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyRequestModel extends Model
{
    protected $table            = 'property_requests';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'client_id',
        'request_type',
        'message',
        'visit_date',
        'visit_time',
        'status',
        'source',
        'assigned_to',
        'response',
        'responded_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'property_id' => 'required|integer',
        'client_id' => 'required|integer',
        'request_type' => 'required|in_list[visit,information]',
        'status' => 'required|in_list[pending,contacted,scheduled,completed,cancelled]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Récupérer les demandes avec les informations du bien et du client
     */
    public function getWithDetails($id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->select('property_requests.*, properties.title as property_title, properties.reference, properties.type, properties.price, clients.name as client_name, clients.phone, clients.email');
        $builder->join('properties', 'properties.id = property_requests.property_id');
        $builder->join('clients', 'clients.id = property_requests.client_id');
        
        if ($id !== null) {
            $builder->where('property_requests.id', $id);
            return $builder->get()->getRowArray();
        }
        
        return $builder->orderBy('property_requests.created_at', 'DESC')->get()->getResultArray();
    }
    
    /**
     * Récupérer les demandes par statut
     */
    public function getByStatus($status)
    {
        return $this->getWithDetails()
            ->where('property_requests.status', $status)
            ->findAll();
    }
    
    /**
     * Récupérer les demandes d'un agent spécifique
     */
    public function getAssignedTo($userId)
    {
        return $this->where('assigned_to', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
