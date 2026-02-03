<?php

namespace App\Models;

use CodeIgniter\Model;

class WorkflowModel extends Model
{
    protected $table = 'workflows';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'description', 'entity_type', 'stages', 'is_default', 'is_active'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get default workflow for entity type
     */
    public function getDefaultForEntity($entityType)
    {
        return $this->where('entity_type', $entityType)
            ->where('is_default', 1)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Get stages as array
     */
    public function getStagesArray($workflowId)
    {
        $workflow = $this->find($workflowId);
        return $workflow ? json_decode($workflow['stages'], true) : [];
    }
}
