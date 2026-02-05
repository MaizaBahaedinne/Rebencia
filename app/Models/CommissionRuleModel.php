<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Commission Rule Model
 * Manages system-level default commission rules
 */
class CommissionRuleModel extends Model
{
    protected $table = 'commission_rules';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'name',
        'transaction_type',
        'property_type',
        'buyer_commission_type',
        'buyer_commission_value',
        'buyer_commission_vat',
        'seller_commission_type',
        'seller_commission_value',
        'seller_commission_vat',
        'is_active',
        'is_default',
        'description'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'transaction_type' => 'required|in_list[sale,rent]',
        'property_type' => 'required|in_list[apartment,villa,house,land,commercial,office,business]',
        'buyer_commission_type' => 'required|in_list[percentage,fixed,months]',
        'buyer_commission_value' => 'required|decimal',
        'seller_commission_type' => 'required|in_list[percentage,fixed,months]',
        'seller_commission_value' => 'required|decimal',
    ];

    /**
     * Get default rule for transaction and property type
     */
    public function getDefaultRule(string $transactionType, string $propertyType): ?array
    {
        return $this->where([
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'is_default' => 1,
            'is_active' => 1
        ])->first();
    }

    /**
     * Get all active rules grouped by type
     */
    public function getActiveRulesGrouped(): array
    {
        $rules = $this->where('is_active', 1)->findAll();
        
        $grouped = [
            'sale' => [],
            'rent' => []
        ];
        
        foreach ($rules as $rule) {
            $grouped[$rule['transaction_type']][] = $rule;
        }
        
        return $grouped;
    }

    /**
     * Get rules by transaction type
     */
    public function getRulesByTransaction(string $transactionType): array
    {
        return $this->where([
            'transaction_type' => $transactionType,
            'is_active' => 1
        ])->findAll();
    }

    /**
     * Set a rule as default for its type (and unset others)
     */
    public function setAsDefault(int $ruleId): bool
    {
        $rule = $this->find($ruleId);
        if (!$rule) {
            return false;
        }

        // Unset other defaults for same transaction/property type
        $this->where([
            'transaction_type' => $rule['transaction_type'],
            'property_type' => $rule['property_type']
        ])->set(['is_default' => 0])->update();

        // Set this one as default
        return $this->update($ruleId, ['is_default' => 1]);
    }

    /**
     * Duplicate a rule
     */
    public function duplicateRule(int $ruleId, string $newName): ?int
    {
        $rule = $this->find($ruleId);
        if (!$rule) {
            return null;
        }

        unset($rule['id'], $rule['created_at'], $rule['updated_at']);
        $rule['name'] = $newName;
        $rule['is_default'] = 0;

        return $this->insert($rule) ? $this->getInsertID() : null;
    }
}
