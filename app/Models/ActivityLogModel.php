<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table         = 'activity_logs';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'user_id', 'action', 'module', 'entity_type',
        'entity_id', 'description', 'ip_address', 'user_agent', 'created_at',
    ];

    /**
     * Retourne les logs filtrés pour la page admin.
     */
    public function getFiltered(array $filters = [], int $perPage = 50): array
    {
        $builder = $this->db->table('activity_logs al')
            ->select('al.*, CONCAT(u.first_name, " ", u.last_name) AS user_name, u.email AS user_email')
            ->join('users u', 'u.id = al.user_id', 'left')
            ->orderBy('al.created_at', 'DESC');

        if (! empty($filters['user_id'])) {
            $builder->where('al.user_id', $filters['user_id']);
        }
        if (! empty($filters['module'])) {
            $builder->where('al.module', $filters['module']);
        }
        if (! empty($filters['action'])) {
            $builder->like('al.action', $filters['action']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('DATE(al.created_at) >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('DATE(al.created_at) <=', $filters['date_to']);
        }

        $total  = $builder->countAllResults(false);
        $page   = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $data   = $builder->limit($perPage, $offset)->get()->getResultArray();

        return compact('data', 'total', 'perPage', 'page');
    }

    /**
     * Export CSV : retourne tous les logs filtrés sans pagination.
     */
    public function getForExport(array $filters = []): array
    {
        $builder = $this->db->table('activity_logs al')
            ->select('al.created_at, CONCAT(u.first_name, " ", u.last_name) AS user_name,
                      al.action, al.module, al.description, al.ip_address')
            ->join('users u', 'u.id = al.user_id', 'left')
            ->orderBy('al.created_at', 'DESC');

        if (! empty($filters['user_id'])) {
            $builder->where('al.user_id', $filters['user_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('DATE(al.created_at) >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('DATE(al.created_at) <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }
}
