<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Transaction Commission Model
 * Manages calculated commissions for transactions
 */
class TransactionCommissionModel extends Model
{
    protected $table = 'transaction_commissions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'transaction_id',
        'property_id',
        'rule_id',
        'override_id',
        'override_level',
        'transaction_type',
        'property_type',
        'transaction_amount',
        'buyer_commission_ht',
        'buyer_commission_vat',
        'buyer_commission_ttc',
        'buyer_commission_type',
        'buyer_commission_value',
        'seller_commission_ht',
        'seller_commission_vat',
        'seller_commission_ttc',
        'seller_commission_type',
        'seller_commission_value',
        'total_commission_ht',
        'total_commission_vat',
        'total_commission_ttc',
        'agent_id',
        'agent_commission_percentage',
        'agent_commission_amount',
        'agency_commission_amount',
        'payment_status',
        'paid_amount',
        'payment_date',
        'calculated_by',
        'validated_at',
        'validated_by',
        'notes'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get commission by transaction ID
     */
    public function getByTransaction(int $transactionId): ?array
    {
        return $this->where('transaction_id', $transactionId)->first();
    }

    /**
     * Get commissions by agent with details
     */
    public function getAgentCommissions(int $agentId, ?string $status = null, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $this->select('transaction_commissions.*, properties.reference as property_ref, properties.title as property_title')
            ->join('properties', 'properties.id = transaction_commissions.property_id')
            ->where('transaction_commissions.agent_id', $agentId);

        if ($status) {
            $builder->where('payment_status', $status);
        }

        if ($dateFrom) {
            $builder->where('calculated_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('calculated_at <=', $dateTo);
        }

        return $builder->orderBy('calculated_at', 'DESC')->findAll();
    }

    /**
     * Get agent commission summary
     */
    public function getAgentSummary(int $agentId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $this->selectSum('total_commission_ttc', 'total_ttc')
            ->selectSum('agent_commission_amount', 'agent_total')
            ->selectSum('agency_commission_amount', 'agency_total')
            ->selectSum('paid_amount', 'total_paid')
            ->where('agent_id', $agentId);

        if ($dateFrom) {
            $builder->where('calculated_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('calculated_at <=', $dateTo);
        }

        return $builder->get()->getRowArray() ?? [];
    }

    /**
     * Get pending commissions by agency
     */
    public function getPendingByAgency(int $agencyId): array
    {
        return $this->select('transaction_commissions.*, users.first_name, users.last_name, properties.reference')
            ->join('users', 'users.id = transaction_commissions.agent_id')
            ->join('properties', 'properties.id = transaction_commissions.property_id')
            ->join('agencies', 'agencies.id = users.agency_id')
            ->where('agencies.id', $agencyId)
            ->where('payment_status', 'pending')
            ->orderBy('calculated_at', 'DESC')
            ->findAll();
    }

    /**
     * Mark commission as paid
     */
    public function markAsPaid(int $id, float $amount, int $userId): bool
    {
        return $this->update($id, [
            'payment_status' => 'paid',
            'paid_amount' => $amount,
            'payment_date' => date('Y-m-d'),
            'validated_at' => date('Y-m-d H:i:s'),
            'validated_by' => $userId
        ]);
    }

    /**
     * Get commissions summary by date range
     */
    public function getSummaryByDateRange(string $dateFrom, string $dateTo, ?int $agencyId = null): array
    {
        $builder = $this->selectSum('total_commission_ht', 'total_ht')
            ->selectSum('total_commission_ttc', 'total_ttc')
            ->selectSum('agent_commission_amount', 'agent_total')
            ->selectSum('agency_commission_amount', 'agency_total')
            ->selectSum('paid_amount', 'total_paid')
            ->selectCount('id', 'transaction_count')
            ->where('calculated_at >=', $dateFrom)
            ->where('calculated_at <=', $dateTo);

        if ($agencyId) {
            $builder->join('users', 'users.id = transaction_commissions.agent_id')
                ->where('users.agency_id', $agencyId);
        }

        return $builder->get()->getRowArray() ?? [];
    }

    /**
     * Get top performing agents by commission
     */
    public function getTopAgents(int $limit = 10, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $this->select('users.id, users.first_name, users.last_name, agencies.name as agency_name')
            ->selectSum('transaction_commissions.agent_commission_amount', 'total_commission')
            ->selectCount('transaction_commissions.id', 'transaction_count')
            ->join('users', 'users.id = transaction_commissions.agent_id')
            ->join('agencies', 'agencies.id = users.agency_id', 'left')
            ->groupBy('users.id');

        if ($dateFrom) {
            $builder->where('calculated_at >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('calculated_at <=', $dateTo);
        }

        return $builder->orderBy('total_commission', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
