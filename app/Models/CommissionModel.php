<?php

namespace App\Models;

use CodeIgniter\Model;

class CommissionModel extends Model
{
    protected $table = 'commissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'transaction_id', 'user_id', 'agency_id', 'type', 'percentage',
        'amount', 'status', 'payment_date', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'transaction_id' => 'required|integer',
        'user_id' => 'required|integer',
        'amount' => 'required|decimal',
        'type' => 'required|in_list[agent,manager,agency,other]',
    ];

    public function calculateCommission($transactionId, $userId, $percentage)
    {
        $transactionModel = model('TransactionModel');
        $transaction = $transactionModel->find($transactionId);
        
        if (!$transaction) {
            return false;
        }

        $amount = ($transaction['amount'] * $percentage) / 100;

        return $this->insert([
            'transaction_id' => $transactionId,
            'user_id' => $userId,
            'percentage' => $percentage,
            'amount' => $amount,
            'type' => 'agent',
            'status' => 'pending'
        ]);
    }

    public function getCommissionsByUser($userId, $status = null)
    {
        $builder = $this->select('commissions.*, transactions.reference as transaction_ref, transactions.amount as transaction_amount')
            ->join('transactions', 'transactions.id = commissions.transaction_id')
            ->where('commissions.user_id', $userId);
        
        if ($status) {
            $builder->where('commissions.status', $status);
        }
        
        return $builder->findAll();
    }

    public function getTotalCommissionsByUser($userId)
    {
        return $this->selectSum('amount')
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->first();
    }
}
