<?php

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table          = 'leads';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'assigned_to', 'property_id', 'first_name', 'last_name',
        'email', 'phone', 'source', 'status',
        'budget_min', 'budget_max', 'notes',
        'property_type', 'property_types', 'transaction_type', 'priority', 'next_follow_up',
        'desired_surface', 'desired_location', 'desired_zone_ids',
        'surface_min', 'surface_max',
        'rooms_min', 'bedrooms_min', 'bathrooms_min',
        'floor_min', 'floor_max',
        'wants_parking', 'wants_elevator', 'wants_garden', 'wants_pool', 'wants_terrace',
        'construction_state', 'furnished', 'orientation', 'target_date',
    ];

    // --------------------------------------------------------

    /**
     * Retourne les leads filtrés avec info de l'assigné.
     */
    public function getFiltered(array $filters = [], int $perPage = 20): array
    {
        $builder = $this->db->table('leads l')
            ->select('l.*, 
                      CONCAT(u.first_name, " ", u.last_name) AS agent_name,
                      p.title AS property_title, p.reference AS property_ref')
            ->join('users u', 'u.id = l.assigned_to', 'left')
            ->join('properties p', 'p.id = l.property_id', 'left')
            ->where('l.deleted_at IS NULL');

        if (! empty($filters['status'])) {
            $builder->where('l.status', $filters['status']);
        }
        if (! empty($filters['assigned_to'])) {
            $builder->where('l.assigned_to', $filters['assigned_to']);
        }
        if (! empty($filters['priority'])) {
            $builder->where('l.priority', $filters['priority']);
        }
        if (! empty($filters['agency_id'])) {
            $builder->where('u.agency_id', (int) $filters['agency_id']);
        }
        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('l.first_name', $filters['search'])
                ->orLike('l.last_name', $filters['search'])
                ->orLike('l.email', $filters['search'])
                ->orLike('l.phone', $filters['search'])
                ->groupEnd();
        }

        $total  = $builder->countAllResults(false);
        $page   = max(1, (int) ($filters['page'] ?? 1));
        $offset = ($page - 1) * $perPage;
        $data   = $builder->orderBy('l.created_at', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        return [
            'data'     => $data,
            'total'    => $total,
            'per_page' => $perPage,
            'page'     => $page,
            'pages'    => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Lead complet avec notes et historique.
     */
    public function findWithDetails(int $id): ?array
    {
        $lead = $this->db->table('leads l')
            ->select('l.*, CONCAT(u.first_name, " ", u.last_name) AS agent_name,
                      p.title AS property_title, p.reference AS property_ref')
            ->join('users u', 'u.id = l.assigned_to', 'left')
            ->join('properties p', 'p.id = l.property_id', 'left')
            ->where('l.id', $id)
            ->where('l.deleted_at IS NULL')
            ->get()->getRowArray();

        if (! $lead) {
            return null;
        }

        $lead['lead_notes'] = $this->db->table('lead_notes ln')
            ->select('ln.*, u.first_name AS author_first_name, u.last_name AS author_last_name')
            ->join('users u', 'u.id = ln.user_id', 'left')
            ->where('ln.lead_id', $id)
            ->orderBy('ln.created_at', 'DESC')
            ->get()->getResultArray();

        $lead['status_history'] = $this->db->table('lead_status_history lsh')
            ->select('lsh.*, CONCAT(u.first_name, " ", u.last_name) AS changed_by')
            ->join('users u', 'u.id = lsh.user_id', 'left')
            ->where('lsh.lead_id', $id)
            ->orderBy('lsh.created_at', 'DESC')
            ->get()->getResultArray();

        return $lead;
    }

    /**
     * Données pipeline pour le CRM (kanban).
     */
    public function getPipeline(?int $agentId = null, ?int $agencyId = null): array
    {
        $statuses = ['new', 'contacted', 'interested', 'visit_done', 'negotiating', 'won', 'lost'];
        $pipeline = [];

        foreach ($statuses as $status) {
            $builder = $this->db->table('leads l')
                ->select('l.id, l.first_name, l.last_name, l.phone, l.priority,
                          l.created_at, p.title AS property_title')
                ->join('properties p', 'p.id = l.property_id', 'left')
                ->where('l.status', $status)
                ->where('l.deleted_at IS NULL');

            if ($agentId) {
                $builder->where('l.assigned_to', $agentId);
            }
            if ($agencyId) {
                $builder->join('users u', 'u.id = l.assigned_to', 'left')
                         ->where('u.agency_id', $agencyId);
            }

            $pipeline[$status] = $builder->orderBy('l.priority DESC, l.created_at DESC')
                ->get()->getResultArray();
        }

        return $pipeline;
    }

    /**
     * Statistiques pour le dashboard.
     */
    public function getStats(?int $agentId = null): array
    {
        $builder = $this->db->table('leads')->where('deleted_at IS NULL');
        if ($agentId) {
            $builder->where('assigned_to', $agentId);
        }

        $all = $builder->get()->getResultArray();

        $stats = ['total' => count($all), 'new' => 0, 'contacted' => 0,
                  'interested' => 0, 'visit_done' => 0, 'negotiating' => 0, 'won' => 0, 'lost' => 0];

        foreach ($all as $row) {
            if (isset($stats[$row['status']])) {
                $stats[$row['status']]++;
            }
        }

        return $stats;
    }

    /**
     * Mise à jour du statut avec historisation.
     */
    public function changeStatus(int $leadId, string $newStatus, int $userId, string $notes = ''): void
    {
        $lead = $this->find($leadId);
        if (! $lead) {
            return;
        }

        $this->update($leadId, ['status' => $newStatus]);

        $this->db->table('lead_status_history')->insert([
            'lead_id'    => $leadId,
            'user_id'    => $userId,
            'old_status' => $lead['status'],
            'new_status' => $newStatus,
            'notes'      => $notes,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ajoute une note à un lead.
     */
    public function addNote(int $leadId, int $userId, string $content): void
    {
        $this->db->table('lead_notes')->insert([
            'lead_id'    => $leadId,
            'user_id'    => $userId,
            'note'       => $content,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
