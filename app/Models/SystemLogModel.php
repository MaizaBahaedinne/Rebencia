<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemLogModel extends Model
{
    protected $table         = 'system_logs';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'level', 'channel', 'message', 'context',
        'ip_address', 'url', 'user_id', 'created_at',
    ];

    /**
     * Retourne les logs système filtrés avec pagination.
     */
    public function getFiltered(array $filters = [], int $perPage = 50): array
    {
        $builder = $this->db->table('system_logs sl')
            ->select('sl.*, CONCAT(u.first_name, " ", u.last_name) AS user_name')
            ->join('users u', 'u.id = sl.user_id', 'left')
            ->orderBy('sl.created_at', 'DESC');

        if (! empty($filters['level'])) {
            $builder->where('sl.level', $filters['level']);
        }
        if (! empty($filters['channel'])) {
            $builder->where('sl.channel', $filters['channel']);
        }
        if (! empty($filters['user_id'])) {
            $builder->where('sl.user_id', $filters['user_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('DATE(sl.created_at) >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('DATE(sl.created_at) <=', $filters['date_to']);
        }
        if (! empty($filters['search'])) {
            $builder->like('sl.message', $filters['search']);
        }

        $total  = $builder->countAllResults(false);
        $page   = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $data   = $builder->limit($perPage, $offset)->get()->getResultArray();

        return compact('data', 'total', 'perPage', 'page');
    }

    /**
     * Statistiques rapides par niveau pour les badges.
     */
    public function getLevelStats(): array
    {
        $rows = $this->db->query(
            "SELECT level, COUNT(*) AS cnt FROM system_logs
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
             GROUP BY level"
        )->getResultArray();

        $stats = [];
        foreach ($rows as $row) {
            $stats[$row['level']] = (int) $row['cnt'];
        }

        return $stats;
    }

    /**
     * Export CSV des logs.
     */
    public function getForExport(array $filters = []): array
    {
        $builder = $this->db->table('system_logs sl')
            ->select('sl.created_at, sl.level, sl.channel, sl.message, sl.url, sl.ip_address,
                      CONCAT(u.first_name, " ", u.last_name) AS user_name')
            ->join('users u', 'u.id = sl.user_id', 'left')
            ->orderBy('sl.created_at', 'DESC');

        if (! empty($filters['level'])) {
            $builder->where('sl.level', $filters['level']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('DATE(sl.created_at) >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('DATE(sl.created_at) <=', $filters['date_to']);
        }

        return $builder->get()->getResultArray();
    }
}
