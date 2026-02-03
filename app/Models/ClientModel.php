<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table = 'clients';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'type', 'first_name', 'last_name', 'company_name', 'email', 'phone',
        'phone_secondary', 'address', 'city', 'governorate', 'cin', 'tax_id',
        'source', 'status', 'assigned_to', 'agency_id', 'notes',
        'property_type_preference', 'transaction_type_preference', 
        'budget_min', 'budget_max', 'preferred_zones', 'area_preference'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'type' => 'required|in_list[individual,company]',
        'email' => 'permit_empty|valid_email',
        'phone' => 'permit_empty|max_length[50]',
    ];

    public function getClientWithAgent($id)
    {
        return $this->select('clients.*, users.first_name as agent_first_name, users.last_name as agent_last_name, agencies.name as agency_name')
            ->join('users', 'users.id = clients.assigned_to', 'left')
            ->join('agencies', 'agencies.id = clients.agency_id', 'left')
            ->where('clients.id', $id)
            ->first();
    }

    public function getClientsByStatus($status = 'lead')
    {
        return $this->where('status', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function searchClients($filters = [])
    {
        $builder = $this->builder();
        
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        
        if (!empty($filters['assigned_to'])) {
            $builder->where('assigned_to', $filters['assigned_to']);
        }
        
        if (!empty($filters['agency_id'])) {
            $builder->where('agency_id', $filters['agency_id']);
        }
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('first_name', $filters['search'])
                ->orLike('last_name', $filters['search'])
                ->orLike('email', $filters['search'])
                ->orLike('phone', $filters['search'])
                ->groupEnd();
        }
        
        return $builder->get()->getResultArray();
    }
}
