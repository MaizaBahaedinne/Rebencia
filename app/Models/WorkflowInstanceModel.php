<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkflowInstanceModel extends Model
{
    protected $table = 'workflow_instances';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'workflow_id', 'entity_type', 'entity_id', 'current_stage', 
        'assigned_to', 'started_at', 'completed_at', 'metadata'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = false;

    /**
     * Get instance for entity
     */
    public function getForEntity($entityType, $entityId)
    {
        return $this->where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->whereNull('completed_at')
            ->first();
    }

    /**
     * Move to next stage
     */
    public function moveToStage($instanceId, $newStage, $userId, $notes = null)
    {
        $instance = $this->find($instanceId);
        if (!$instance) {
            return false;
        }

        // Update instance
        $this->update($instanceId, ['current_stage' => $newStage]);

        // Create history entry
        $historyModel = model('WorkflowHistoryModel');
        $historyModel->insert([
            'instance_id' => $instanceId,
            'from_stage' => $instance['current_stage'],
            'to_stage' => $newStage,
            'user_id' => $userId,
            'notes' => $notes
        ]);

        return true;
    }

    /**
     * Get instances by stage
     */
    public function getByStage($workflowId, $stage)
    {
        return $this->where('workflow_id', $workflowId)
            ->where('current_stage', $stage)
            ->whereNull('completed_at')
            ->findAll();
    }

    /**
     * Complete instance
     */
    public function complete($instanceId, $userId)
    {
        return $this->update($instanceId, [
            'completed_at' => date('Y-m-d H:i:s')
        ]);
    }
}
