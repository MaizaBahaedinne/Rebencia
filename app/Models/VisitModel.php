<?php

namespace App\Models;

use CodeIgniter\Model;

class VisitModel extends Model
{
    protected $table          = 'visits';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'client_id', 'property_id', 'agent_id',
        'visit_date', 'visit_time', 'duration',
        'status', 'notes',
        'feedback', 'feedback_notes',
        'whatsapp_sent', 'reminder_sent',
        'created_by',
    ];

    // ── Libellés ──────────────────────────────────────────────────────────────

    public const STATUS_LABELS = [
        'planifiee'   => ['label' => 'Planifiée',    'color' => 'secondary', 'icon' => 'bi-calendar-event'],
        'confirmee'   => ['label' => 'Confirmée',    'color' => 'primary',   'icon' => 'bi-calendar-check'],
        'effectuee'   => ['label' => 'Effectuée',    'color' => 'success',   'icon' => 'bi-check2-circle'],
        'annulee'     => ['label' => 'Annulée',      'color' => 'danger',    'icon' => 'bi-x-circle'],
        'replanifiee' => ['label' => 'Replanifiée',  'color' => 'warning',   'icon' => 'bi-arrow-clockwise'],
    ];

    public const STATUS_COLORS_HEX = [
        'planifiee'   => '#6c757d',
        'confirmee'   => '#0d6efd',
        'effectuee'   => '#198754',
        'annulee'     => '#dc3545',
        'replanifiee' => '#ffc107',
    ];

    public const FEEDBACK_LABELS = [
        'interesse'     => ['label' => 'Intéressé',      'color' => 'success'],
        'pas_interesse' => ['label' => 'Pas intéressé',  'color' => 'danger'],
        'negociation'   => ['label' => 'En négociation', 'color' => 'warning'],
    ];

    // ── Requêtes ──────────────────────────────────────────────────────────────

    /**
     * Liste filtrée + paginée avec JOINs client / property / agent.
     */
    public function getFiltered(array $filters): array
    {
        $page    = max(1, (int) ($filters['page'] ?? 1));
        $perPage = 20;
        $offset  = ($page - 1) * $perPage;

        $builder = $this->db->table('visits v')
            ->select('v.id, v.visit_date, v.visit_time, v.duration, v.status,
                      v.feedback, v.whatsapp_sent, v.created_at,
                      c.id AS client_id, c.first_name, c.last_name, c.phone AS client_phone,
                      p.id AS property_id, p.title AS property_title,
                      p.reference AS property_ref, p.city AS property_city,
                      u.id AS agent_id, u.first_name AS agent_first, u.last_name AS agent_last')
            ->join('clients c',    'c.id = v.client_id')
            ->join('properties p', 'p.id = v.property_id')
            ->join('users u',      'u.id = v.agent_id')
            ->where('v.deleted_at IS NULL');

        if (! empty($filters['status'])) {
            $builder->where('v.status', $filters['status']);
        }
        if (! empty($filters['agent_id'])) {
            $builder->where('v.agent_id', (int) $filters['agent_id']);
        }
        if (! empty($filters['date_from'])) {
            $builder->where('v.visit_date >=', $filters['date_from']);
        }
        if (! empty($filters['date_to'])) {
            $builder->where('v.visit_date <=', $filters['date_to']);
        }
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('c.first_name', $s)
                ->orLike('c.last_name', $s)
                ->orLike('p.title', $s)
                ->orLike('p.reference', $s)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);
        $rows  = $builder
            ->orderBy('v.visit_date', 'DESC')
            ->orderBy('v.visit_time', 'ASC')
            ->limit($perPage, $offset)
            ->get()->getResultArray();

        return [
            'rows'     => $rows,
            'total'    => $total,
            'page'     => $page,
            'per_page' => $perPage,
            'pages'    => $total > 0 ? (int) ceil($total / $perPage) : 1,
        ];
    }

    /**
     * Détail d'une visite avec toutes les relations.
     */
    public function findWithRelations(int $id): ?array
    {
        return $this->db->table('visits v')
            ->select('v.*,
                      c.first_name, c.last_name, c.phone AS client_phone,
                      c.email AS client_email, c.status AS client_status,
                      p.title AS property_title, p.reference AS property_ref,
                      p.city AS property_city, p.type AS property_type,
                      u.first_name AS agent_first, u.last_name AS agent_last,
                      u.phone AS agent_phone, u.email AS agent_email')
            ->join('clients c',    'c.id = v.client_id')
            ->join('properties p', 'p.id = v.property_id')
            ->join('users u',      'u.id = v.agent_id')
            ->where('v.id', $id)
            ->where('v.deleted_at IS NULL')
            ->get()
            ->getRowArray();
    }

    /**
     * Vérifie si l'agent a une visite en conflit (dans une fenêtre de ±duration minutes).
     * Retourne true si conflit détecté.
     */
    public function checkAgentConflict(
        int    $agentId,
        string $date,
        string $time,
        int    $duration  = 60,
        ?int   $excludeId = null
    ): bool {
        $sql = "SELECT COUNT(*) AS cnt
                FROM visits
                WHERE agent_id = ?
                  AND visit_date = ?
                  AND status IN ('planifiee', 'confirmee')
                  AND deleted_at IS NULL
                  AND ABS(TIME_TO_SEC(TIMEDIFF(visit_time, ?))) < ? * 60";

        $params = [$agentId, $date, $time, $duration];

        if ($excludeId !== null) {
            $sql   .= ' AND id != ?';
            $params[] = $excludeId;
        }

        $row = $this->db->query($sql, $params)->getRow();
        return (int) ($row->cnt ?? 0) > 0;
    }

    /**
     * Comptage par statut (pour les cards du tableau de bord).
     */
    public function countByStatus(): array
    {
        $rows = $this->db->table('visits')
            ->select('status, COUNT(*) AS cnt')
            ->where('deleted_at IS NULL')
            ->groupBy('status')
            ->get()->getResultArray();

        $map = array_fill_keys(array_keys(self::STATUS_LABELS), 0);
        foreach ($rows as $r) {
            if (isset($map[$r['status']])) {
                $map[$r['status']] = (int) $r['cnt'];
            }
        }
        return $map;
    }

    /**
     * Retourne les visites pour FullCalendar (format JSON API).
     */
    public function getForCalendar(string $start, string $end, ?int $agentId = null): array
    {
        $builder = $this->db->table('visits v')
            ->select('v.id, v.visit_date, v.visit_time, v.duration, v.status,
                      c.first_name, c.last_name,
                      p.title AS property_title, p.city AS property_city,
                      u.first_name AS agent_first, u.last_name AS agent_last')
            ->join('clients c',    'c.id = v.client_id')
            ->join('properties p', 'p.id = v.property_id')
            ->join('users u',      'u.id = v.agent_id')
            ->where('v.deleted_at IS NULL')
            ->where('v.visit_date >=', $start)
            ->where('v.visit_date <=', $end);

        if ($agentId !== null) {
            $builder->where('v.agent_id', $agentId);
        }

        return $builder
            ->orderBy('v.visit_date')
            ->orderBy('v.visit_time')
            ->get()->getResultArray();
    }
}
