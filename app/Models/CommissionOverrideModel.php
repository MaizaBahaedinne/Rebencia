<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Commission Override Model
 * Manages commission overrides at agency/role/user level
 */
class CommissionOverrideModel extends Model
{
    protected $table = 'commission_overrides';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'override_level',
        'agency_id',
        'role_id',
        'user_id',
        'transaction_type',
        'property_type',
        'buyer_commission_type',
        'buyer_commission_value',
        'buyer_commission_vat',
        'seller_commission_type',
        'seller_commission_value',
        'seller_commission_vat',
        'is_active',
        'notes',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'override_level' => 'required|in_list[agency,role,user]',
        'transaction_type' => 'required|in_list[sale,rent]',
        'property_type' => 'required|in_list[apartment,villa,house,land,commercial,office,business]',
    ];

    /**
     * Get override for specific user (highest priority)
     */
    public function getUserOverride(int $userId, string $transactionType, string $propertyType): ?array
    {
        return $this->where([
            'override_level' => 'user',
            'user_id' => $userId,
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'is_active' => 1
        ])->first();
    }

    /**
     * Get override for specific role
     */
    public function getRoleOverride(int $roleId, string $transactionType, string $propertyType): ?array
    {
        return $this->where([
            'override_level' => 'role',
            'role_id' => $roleId,
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'is_active' => 1
        ])->first();
    }

    /**
     * Get override for specific agency
     */
    public function getAgencyOverride(int $agencyId, string $transactionType, string $propertyType): ?array
    {
        return $this->where([
            'override_level' => 'agency',
            'agency_id' => $agencyId,
            'transaction_type' => $transactionType,
            'property_type' => $propertyType,
            'is_active' => 1
        ])->first();
    }

    /**
     * Get best override for user (user > role > agency)
     * Returns the highest priority override found
     */
    public function getBestOverrideForUser(int $userId, int $roleId, int $agencyId, string $transactionType, string $propertyType): ?array
    {
        // Priority 1: User level
        $override = $this->getUserOverride($userId, $transactionType, $propertyType);
        if ($override) {
            return $override;
        }

        // Priority 2: Role level
        $override = $this->getRoleOverride($roleId, $transactionType, $propertyType);
        if ($override) {
            return $override;
        }

        // Priority 3: Agency level
        $override = $this->getAgencyOverride($agencyId, $transactionType, $propertyType);
        if ($override) {
            return $override;
        }

        return null;
    }

    /**
     * Get all overrides for a user
     */
    public function getUserOverrides(int $userId): array
    {
        return $this->where([
            'override_level' => 'user',
            'user_id' => $userId,
            'is_active' => 1
        ])->findAll();
    }

    /**
     * Get all overrides for an agency
     */
    public function getAgencyOverrides(int $agencyId): array
    {
        return $this->where([
            'override_level' => 'agency',
            'agency_id' => $agencyId,
            'is_active' => 1
        ])->findAll();
    }

    /**
     * Get all overrides for a role
     */
    public function getRoleOverrides(int $roleId): array
    {
        return $this->where([
            'override_level' => 'role',
            'role_id' => $roleId,
            'is_active' => 1
        ])->findAll();
    }

    /**
     * Create or update an override
     */
    public function upsertOverride(array $data): bool
    {
        $existing = null;

        // Find existing override
        $where = [
            'override_level' => $data['override_level'],
            'transaction_type' => $data['transaction_type'],
            'property_type' => $data['property_type']
        ];

        switch ($data['override_level']) {
            case 'user':
                $where['user_id'] = $data['user_id'];
                break;
            case 'role':
                $where['role_id'] = $data['role_id'];
                break;
            case 'agency':
                $where['agency_id'] = $data['agency_id'];
                break;
        }

        $existing = $this->where($where)->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data) !== false;
    }
}
