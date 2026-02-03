<?php

namespace App\Libraries;

class AuditLogger
{
    protected $db;
    protected $auditModel;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Log an action
     */
    public function log($action, $module, $recordId = null, $oldValues = null, $newValues = null)
    {
        $request = \Config\Services::request();
        
        $data = [
            'user_id' => session()->get('user_id'),
            'action' => $action,
            'module' => $module,
            'record_id' => $recordId,
            'old_values' => $oldValues ? json_encode($oldValues) : null,
            'new_values' => $newValues ? json_encode($newValues) : null,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString()
        ];

        $this->db->table('audit_logs')->insert($data);
    }

    /**
     * Log property action
     */
    public function logProperty($action, $propertyId, $oldData = null, $newData = null)
    {
        $this->log($action, 'property', $propertyId, $oldData, $newData);
    }

    /**
     * Log client action
     */
    public function logClient($action, $clientId, $oldData = null, $newData = null)
    {
        $this->log($action, 'client', $clientId, $oldData, $newData);
    }

    /**
     * Log transaction action
     */
    public function logTransaction($action, $transactionId, $oldData = null, $newData = null)
    {
        $this->log($action, 'transaction', $transactionId, $oldData, $newData);
    }

    /**
     * Log user action
     */
    public function logUser($action, $userId, $oldData = null, $newData = null)
    {
        $this->log($action, 'user', $userId, $oldData, $newData);
    }

    /**
     * Get logs for a specific record
     */
    public function getLogsForRecord($module, $recordId)
    {
        return $this->db->table('audit_logs')
            ->select('audit_logs.*, CONCAT(users.first_name, " ", users.last_name) as user_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->where('audit_logs.module', $module)
            ->where('audit_logs.record_id', $recordId)
            ->orderBy('audit_logs.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get recent logs
     */
    public function getRecentLogs($limit = 50)
    {
        return $this->db->table('audit_logs')
            ->select('audit_logs.*, CONCAT(users.first_name, " ", users.last_name) as user_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }
}
