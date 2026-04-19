<?php

namespace App\Models;

use CodeIgniter\Model;

class TaskModel extends Model
{
    protected $table         = 'tasks';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'reference', 'title', 'description', 'type', 'status', 'priority',
        'created_by', 'assigned_to', 'due_date', 'labels',
    ];

    // ----------------------------------------------------------------
    // Constantes métier
    // ----------------------------------------------------------------

    public const TYPES = [
        'bug'         => ['label' => 'Bug',         'icon' => 'bi-bug',          'color' => '#dc3545'],
        'feature'     => ['label' => 'Fonctionnalité','icon' => 'bi-stars',       'color' => '#0d6efd'],
        'improvement' => ['label' => 'Amélioration', 'icon' => 'bi-arrow-up-circle','color' => '#6610f2'],
        'task'        => ['label' => 'Tâche',        'icon' => 'bi-check2-square','color' => '#198754'],
        'question'    => ['label' => 'Question',     'icon' => 'bi-question-circle','color' => '#fd7e14'],
    ];

    public const STATUSES = [
        'backlog'     => ['label' => 'Backlog',      'color' => 'secondary'],
        'todo'        => ['label' => 'À faire',      'color' => 'primary'],
        'in_progress' => ['label' => 'En cours',     'color' => 'warning'],
        'review'      => ['label' => 'En revue',     'color' => 'info'],
        'done'        => ['label' => 'Terminé',      'color' => 'success'],
        'cancelled'   => ['label' => 'Annulé',       'color' => 'dark'],
    ];

    public const PRIORITIES = [
        'low'      => ['label' => 'Basse',    'color' => '#6c757d', 'icon' => 'bi-arrow-down'],
        'medium'   => ['label' => 'Moyenne',  'color' => '#0d6efd', 'icon' => 'bi-dash'],
        'high'     => ['label' => 'Haute',    'color' => '#fd7e14', 'icon' => 'bi-arrow-up'],
        'critical' => ['label' => 'Critique', 'color' => '#dc3545', 'icon' => 'bi-lightning-fill'],
    ];

    // ----------------------------------------------------------------
    // Requêtes
    // ----------------------------------------------------------------

    public function getFiltered(array $filters = []): array
    {
        $builder = $this->db->table('tasks t')
            ->select('t.*, u1.first_name AS creator_first, u1.last_name AS creator_last,
                      u2.first_name AS assignee_first, u2.last_name AS assignee_last,
                      (SELECT COUNT(*) FROM task_comments tc WHERE tc.task_id = t.id) AS comment_count')
            ->join('users u1', 'u1.id = t.created_by', 'left')
            ->join('users u2', 'u2.id = t.assigned_to', 'left')
            ->where('t.deleted_at IS NULL');

        if (! empty($filters['status'])) {
            $builder->where('t.status', $filters['status']);
        }
        if (! empty($filters['type'])) {
            $builder->where('t.type', $filters['type']);
        }
        if (! empty($filters['priority'])) {
            $builder->where('t.priority', $filters['priority']);
        }
        if (! empty($filters['assigned_to'])) {
            $builder->where('t.assigned_to', $filters['assigned_to']);
        }
        if (! empty($filters['search'])) {
            $search = $this->db->escapeLikeString($filters['search']);
            $builder->groupStart()
                ->like('t.title', $search, 'both', null, true)
                ->orLike('t.reference', $search, 'both', null, true)
                ->groupEnd();
        }

        $builder->orderBy('FIELD(t.priority,"critical","high","medium","low")')
                ->orderBy('t.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    public function getWithDetails(int $id): ?array
    {
        $task = $this->db->table('tasks t')
            ->select('t.*, u1.first_name AS creator_first, u1.last_name AS creator_last,
                      u2.first_name AS assignee_first, u2.last_name AS assignee_last')
            ->join('users u1', 'u1.id = t.created_by', 'left')
            ->join('users u2', 'u2.id = t.assigned_to', 'left')
            ->where('t.id', $id)
            ->where('t.deleted_at IS NULL')
            ->get()->getRowArray();

        if (! $task) return null;

        $task['comments'] = $this->db->table('task_comments tc')
            ->select('tc.*, u.first_name, u.last_name')
            ->join('users u', 'u.id = tc.user_id', 'left')
            ->where('tc.task_id', $id)
            ->orderBy('tc.created_at', 'ASC')
            ->get()->getResultArray();

        return $task;
    }

    public function getStats(): array
    {
        $rows = $this->db->query("
            SELECT status, COUNT(*) AS cnt
            FROM tasks
            WHERE deleted_at IS NULL
            GROUP BY status
        ")->getResultArray();

        $stats = array_fill_keys(array_keys(self::STATUSES), 0);
        foreach ($rows as $r) {
            $stats[$r['status']] = (int) $r['cnt'];
        }
        return $stats;
    }

    public function generateReference(): string
    {
        $last = $this->db->table('tasks')
            ->selectMax('id')
            ->get()->getRowArray();
        $next = ($last['id'] ?? 0) + 1;
        return 'REB-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }

    public function addComment(int $taskId, int $userId, string $content): bool
    {
        return $this->db->table('task_comments')->insert([
            'task_id'    => $taskId,
            'user_id'    => $userId,
            'content'    => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
