<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'reference', 'property_id', 'client_id', 'agent_id', 'agency_id',
        'type', 'transaction_date', 'amount', 'commission_percentage', 'commission_amount',
        'commission_paid', 'contract_number', 'notary', 'status', 
        'signature_date', 'completion_date', 'start_date', 'end_date',
        'contract_file', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'reference' => 'permit_empty|is_unique[transactions.reference,id,{id}]',
        'property_id' => 'required|is_natural_no_zero',
        'buyer_id' => 'required|is_natural_no_zero',
        'agent_id' => 'required|is_natural_no_zero',
        'type' => 'required|in_list[sale,rent]',
        'amount' => 'required|decimal',
        'transaction_date' => 'required|valid_date',
    ];

    protected $beforeInsert = ['generateReference'];
    
    protected function generateReference(array $data)
    {
        if (!isset($data['data']['reference']) || empty($data['data']['reference'])) {
            $data['data']['reference'] = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        }
        return $data;
    }

    public function getTransactionDetails($id)
    {
        return $this->select('transactions.*, 
            properties.reference as property_ref, properties.title as property_title,
            clients.first_name as client_first_name, clients.last_name as client_last_name,
            users.first_name as agent_first_name, users.last_name as agent_last_name,
            agencies.name as agency_name')
            ->join('properties', 'properties.id = transactions.property_id')
            ->join('clients', 'clients.id = transactions.client_id')
            ->join('users', 'users.id = transactions.agent_id')
            ->join('agencies', 'agencies.id = transactions.agency_id', 'left')
            ->where('transactions.id', $id)
            ->first();
    }

    public function getTransactionsByAgent($agentId)
    {
        return $this->where('agent_id', $agentId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function getTransactionsByStatus($status)
    {
        return $this->select('transactions.*, properties.title as property_title, clients.first_name as client_name')
            ->join('properties', 'properties.id = transactions.property_id')
            ->join('clients', 'clients.id = transactions.client_id')
            ->where('transactions.status', $status)
            ->findAll();
    }
}
