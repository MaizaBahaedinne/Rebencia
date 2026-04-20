<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table          = 'clients';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'client_type', 'first_name', 'last_name', 'phone', 'email',
        'profession', 'company',
        'address', 'zone_pays_id', 'zone_region_id', 'zone_ville_id', 'postal_code',
        'property_type_id', 'budget_min', 'budget_max', 'desired_zone',
        'owner_location', 'desired_price',
        'status', 'assigned_to', 'source',
        'notes',
    ];

    public const TYPE_LABELS = [
        'acheteur'     => ['label' => 'Acheteur',     'color' => 'primary',   'icon' => 'bi-cart'],
        'locataire'    => ['label' => 'Locataire',    'color' => 'info',      'icon' => 'bi-key'],
        'proprietaire' => ['label' => 'Propriétaire', 'color' => 'success',   'icon' => 'bi-house-check'],
        'investisseur' => ['label' => 'Investisseur', 'color' => 'warning',   'icon' => 'bi-graph-up'],
    ];

    public const STATUS_LABELS = [
        'nouveau'    => ['label' => 'Nouveau',     'color' => 'secondary'],
        'contacte'   => ['label' => 'Contacté',    'color' => 'info'],
        'actif'      => ['label' => 'Actif',       'color' => 'success'],
        'en_attente' => ['label' => 'En attente',  'color' => 'warning'],
        'converti'   => ['label' => 'Converti',    'color' => 'primary'],
        'inactif'    => ['label' => 'Inactif',     'color' => 'secondary'],
        'perdu'      => ['label' => 'Perdu',       'color' => 'danger'],
    ];

    public const SOURCE_LABELS = [
        'site_web'  => 'Site web',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'appel'     => 'Appel téléphonique',
        'email'     => 'Email',
        'agence'    => 'Agence',
        'referral'  => 'Référence',
        'autre'     => 'Autre',
    ];

    /**
     * Liste filtrée + paginée avec jointures (agent, zones, type bien).
     */
    public function getFiltered(array $filters): array
    {
        $db      = \Config\Database::connect();
        $builder = $db->table('clients c')
            ->select('c.*, 
                u.first_name AS agent_first, u.last_name AS agent_last,
                pt.name AS property_type_name,
                zp.name AS pays_name, zr.name AS region_name, zv.name AS ville_name')
            ->join('users u',          'u.id = c.assigned_to AND u.deleted_at IS NULL', 'left')
            ->join('property_types pt','pt.id = c.property_type_id AND pt.deleted_at IS NULL', 'left')
            ->join('zones zp',         'zp.id = c.zone_pays_id   AND zp.deleted_at IS NULL', 'left')
            ->join('zones zr',         'zr.id = c.zone_region_id AND zr.deleted_at IS NULL', 'left')
            ->join('zones zv',         'zv.id = c.zone_ville_id  AND zv.deleted_at IS NULL', 'left')
            ->where('c.deleted_at', null);

        if (! empty($filters['client_type'])) {
            $builder->where('c.client_type', $filters['client_type']);
        }
        if (! empty($filters['status'])) {
            $builder->where('c.status', $filters['status']);
        }
        if (! empty($filters['assigned_to'])) {
            $builder->where('c.assigned_to', $filters['assigned_to']);
        }
        if (! empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('c.first_name', $s)
                ->orLike('c.last_name', $s)
                ->orLike('c.phone', $s)
                ->orLike('c.email', $s)
                ->groupEnd();
        }

        $perPage = 20;
        $page    = max(1, (int) ($filters['page'] ?? 1));
        $total   = $builder->countAllResults(false);

        $data = $builder
            ->orderBy('c.created_at', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return [
            'data'    => $data,
            'total'   => $total,
            'page'    => $page,
            'pages'   => (int) ceil($total / $perPage),
            'perPage' => $perPage,
        ];
    }

    /**
     * Trouve un client avec toutes ses jointures.
     */
    public function findWithRelations(int $id): ?array
    {
        $db = \Config\Database::connect();
        return $db->table('clients c')
            ->select('c.*,
                u.first_name AS agent_first, u.last_name AS agent_last,
                pt.name AS property_type_name, pt.icon AS property_type_icon,
                zp.name AS pays_name, zr.name AS region_name, zv.name AS ville_name')
            ->join('users u',          'u.id = c.assigned_to AND u.deleted_at IS NULL', 'left')
            ->join('property_types pt','pt.id = c.property_type_id AND pt.deleted_at IS NULL', 'left')
            ->join('zones zp',         'zp.id = c.zone_pays_id   AND zp.deleted_at IS NULL', 'left')
            ->join('zones zr',         'zr.id = c.zone_region_id AND zr.deleted_at IS NULL', 'left')
            ->join('zones zv',         'zv.id = c.zone_ville_id  AND zv.deleted_at IS NULL', 'left')
            ->where('c.id', $id)
            ->where('c.deleted_at', null)
            ->get()
            ->getRowArray() ?: null;
    }

    /**
     * Comptage par type pour le tableau de bord.
     */
    public function countByType(): array
    {
        $rows = $this->db->table('clients')
            ->select('client_type, COUNT(*) AS total')
            ->where('deleted_at', null)
            ->groupBy('client_type')
            ->get()
            ->getResultArray();

        $counts = [];
        foreach ($rows as $r) {
            $counts[$r['client_type']] = (int) $r['total'];
        }
        return $counts;
    }
}
