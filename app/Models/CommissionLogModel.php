<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Commission Log Model
 * Manages audit trail for all commission-related actions
 */
class CommissionLogModel extends Model
{
    protected $table = 'commission_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'entity_type',
        'entity_id',
        'action',
        'user_id',
        'user_role',
        'ip_address',
        'old_values',
        'new_values',
        'description'
    ];

    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';

    /**
     * Log an action
     */
    public function logAction(string $entityType, int $entityId, string $action, ?array $oldValues = null, ?array $newValues = null, ?string $description = null): bool
    {
        $data = [
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'user_id' => session()->get('user_id'),
            'user_role' => session()->get('role_name'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'description' => $description
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Get logs for specific entity
     */
    public function getEntityLogs(string $entityType, int $entityId, int $limit = 50): array
    {
        return $this->where([
            'entity_type' => $entityType,
            'entity_id' => $entityId
        ])
        ->orderBy('created_at', 'DESC')
        ->limit($limit)
        ->findAll();
    }

    /**
     * Get logs by user
     */
    public function getUserLogs(int $userId, int $limit = 100): array
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get recent logs with user details
     */
    public function getRecentLogsWithDetails(int $limit = 50): array
    {
        return $this->select('commission_logs.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = commission_logs.user_id', 'left')
            ->orderBy('commission_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get logs by date range
     */
    public function getLogsByDateRange(string $dateFrom, string $dateTo, ?string $entityType = null): array
    {
        $builder = $this->where('created_at >=', $dateFrom)
            ->where('created_at <=', $dateTo);

        if ($entityType) {
            $builder->where('entity_type', $entityType);
        }

        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}
