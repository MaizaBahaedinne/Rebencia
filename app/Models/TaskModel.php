<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title', 'description', 'priority', 'status', 'due_date',
        'assigned_to', 'created_by', 'related_type', 'related_id',
        'completed_at', 'tags'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[200]',
        'assigned_to' => 'required|is_natural_no_zero',
        'created_by' => 'required|is_natural_no_zero'
    ];

    /**
     * Get tasks with user info
     */
    public function getWithUsers($id = null)
    {
        $builder = $this->select('tasks.*, 
            CONCAT(assigned.first_name, " ", assigned.last_name) as assigned_name,
            CONCAT(creator.first_name, " ", creator.last_name) as creator_name')
            ->join('users as assigned', 'assigned.id = tasks.assigned_to', 'left')
            ->join('users as creator', 'creator.id = tasks.created_by', 'left');

        if ($id) {
            return $builder->where('tasks.id', $id)->first();
        }

        return $builder->orderBy('tasks.created_at', 'DESC')->findAll();
    }

    /**
     * Get tasks by status for Kanban
     */
    public function getByStatus($userId = null)
    {
        $builder = $this->select('tasks.*, 
            CONCAT(users.first_name, " ", users.last_name) as assigned_name')
            ->join('users', 'users.id = tasks.assigned_to', 'left');

        if ($userId) {
            $builder->where('tasks.assigned_to', $userId);
        }

        $tasks = $builder->findAll();

        // Group by status
        $grouped = [
            'todo' => [],
            'in_progress' => [],
            'review' => [],
            'completed' => []
        ];

        foreach ($tasks as $task) {
            $grouped[$task['status']][] = $task;
        }

        return $grouped;
    }

    /**
     * Get user tasks
     */
    public function getUserTasks($userId, $status = null)
    {
        $builder = $this->where('assigned_to', $userId);

        if ($status) {
            $builder->where('status', $status);
        }

        return $builder->orderBy('due_date', 'ASC')->findAll();
    }

    /**
     * Get overdue tasks
     */
    public function getOverdue($userId = null)
    {
        $builder = $this->where('due_date <', date('Y-m-d'))
            ->where('status !=', 'completed')
            ->where('status !=', 'cancelled');

        if ($userId) {
            $builder->where('assigned_to', $userId);
        }

        return $builder->orderBy('due_date', 'ASC')->findAll();
    }

    /**
     * Mark as completed
     */
    public function markCompleted($id)
    {
        return $this->update($id, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get statistics
     */
    public function getStatistics($userId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        if ($userId) {
            $builder->where('assigned_to', $userId);
        }

        $total = $builder->countAllResults(false);
        $completed = $builder->where('status', 'completed')->countAllResults(false);
        $overdue = $builder->where('due_date <', date('Y-m-d'))
                          ->where('status !=', 'completed')
                          ->where('status !=', 'cancelled')
                          ->countAllResults();

        return [
            'total' => $total,
            'completed' => $completed,
            'pending' => $total - $completed,
            'overdue' => $overdue
        ];
    }
}
