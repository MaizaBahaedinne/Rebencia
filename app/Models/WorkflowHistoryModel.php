<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkflowHistoryModel extends Model
{
    protected $table = 'workflow_history';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'instance_id', 'from_stage', 'to_stage', 'user_id', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Get history for instance
     */
    public function getForInstance($instanceId)
    {
        return $this->select('workflow_history.*, users.full_name as user_name')
            ->join('users', 'users.id = workflow_history.user_id', 'left')
            ->where('instance_id', $instanceId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
}
