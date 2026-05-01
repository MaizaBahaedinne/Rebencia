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
        'client_type', 'demand_type', 'first_name', 'last_name', 'phone', 'email',
        'profession', 'company',
        'address', 'zone_pays_id', 'zone_region_id', 'zone_ville_id', 'postal_code',
        'property_type_id', 'budget_min', 'budget_max', 'desired_zone',
        'owner_location', 'desired_price',
        'orientations',
        'surface_min', 'surface_max', 'rooms_min', 'bedrooms_min',
        'floor_preferred', 'has_elevator',
        'urgency', 'budget_flexibility',
        'bathrooms_min', 'parking_min', 'construction_state', 'furnished',
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

    public const DEMAND_TYPE_LABELS = [
        'achat'    => ['label' => 'Achat',    'color' => 'primary', 'icon' => 'bi-house-check'],
        'location' => ['label' => 'Location', 'color' => 'info',    'icon' => 'bi-key'],
    ];

    public const URGENCY_LABELS = [
        'faible'  => ['label' => 'Faible',  'color' => 'success'],
        'moyenne' => ['label' => 'Moyenne', 'color' => 'warning'],
        'elevee'  => ['label' => 'Élevée',  'color' => 'danger'],
    ];

    public const BUDGET_FLEXIBILITY_LABELS = [
        'strict'         => ['label' => 'Strict',         'color' => 'danger'],
        'flexible'       => ['label' => 'Flexible',       'color' => 'warning'],
        'tres_flexible'  => ['label' => 'Très flexible',  'color' => 'success'],
    ];

    public const ORIENTATION_LABELS = [
        'nord'      => 'Nord',
        'sud'       => 'Sud',
        'est'       => 'Est',
        'ouest'     => 'Ouest',
        'nord_est'  => 'Nord-Est',
        'nord_ouest'=> 'Nord-Ouest',
        'sud_est'   => 'Sud-Est',
        'sud_ouest' => 'Sud-Ouest',
    ];

    public const CONSTRUCTION_STATE_LABELS = [
        'neuf'         => ['label' => 'Neuf',             'color' => 'success',   'icon' => 'bi-stars'],
        'recent'       => ['label' => 'Récent (< 5 ans)', 'color' => 'info',      'icon' => 'bi-calendar-check'],
        'ancien'       => ['label' => 'Ancien',           'color' => 'secondary', 'icon' => 'bi-building'],
        'a_renover'    => ['label' => 'À rénover',        'color' => 'warning',   'icon' => 'bi-tools'],
        'indifferent'  => ['label' => 'Indifférent',      'color' => 'light',     'icon' => 'bi-dash-circle'],
    ];

    public const FURNISHED_LABELS = [
        'meuble'       => ['label' => 'Meublé',        'color' => 'primary',   'icon' => 'bi-lamp'],
        'semi_meuble'  => ['label' => 'Semi-meublé',   'color' => 'info',      'icon' => 'bi-lamp-fill'],
        'vide'         => ['label' => 'Vide',          'color' => 'secondary', 'icon' => 'bi-box'],
        'indifferent'  => ['label' => 'Indifférent',   'color' => 'light',     'icon' => 'bi-dash-circle'],
    ];

    public const FEATURES_CATALOG = [
        'equipements' => [
            'label' => 'Équipements',
            'icon'  => 'bi-tools',
            'items' => [
                'piscine'            => 'Piscine',
                'garage'             => 'Garage',
                'ascenseur'          => 'Ascenseur',
                'chauffage_central'  => 'Chauffage central',
                'climatisation'      => 'Climatisation',
            ],
        ],
        'confort' => [
            'label' => 'Confort',
            'icon'  => 'bi-house-heart',
            'items' => [
                'terrasse' => 'Terrasse',
                'balcon'   => 'Balcon',
                'jardin'   => 'Jardin',
                'vue_mer'  => 'Vue mer',
            ],
        ],
        'securite' => [
            'label' => 'Sécurité',
            'icon'  => 'bi-shield-check',
            'items' => [
                'residence_securisee' => 'Résidence sécurisée',
                'camera'              => 'Caméra',
                'gardien'             => 'Gardien',
            ],
        ],
    ];

    // ── Méthodes pivot ────────────────────────────────────────────────────────

    public function getPivotPropertyTypes(int $clientId): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('client_property_types')) {
            return [];
        }
        return array_column(
            $db->table('client_property_types')
                ->select('property_type_id')
                ->where('client_id', $clientId)
                ->get()->getResultArray(),
            'property_type_id'
        );
    }

    public function getPivotZones(int $clientId): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('client_search_zones')) {
            return [];
        }
        return $db->table('client_search_zones csz')
            ->select('csz.zone_id, z.name, z.type, z.latitude, z.longitude, z.geometry')
            ->join('zones z', 'z.id = csz.zone_id AND z.deleted_at IS NULL', 'left')
            ->where('csz.client_id', $clientId)
            ->get()->getResultArray();
    }

    public function getPivotFeatures(int $clientId): array
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('client_features')) {
            return [];
        }
        $rows = $db->table('client_features')
            ->where('client_id', $clientId)
            ->get()->getResultArray();
        // Retourner map: feature_key => requirement_type
        $map = [];
        foreach ($rows as $r) {
            $map[$r['feature_key']] = $r['requirement_type'];
        }
        return $map;
    }

    public function savePivotPropertyTypes(int $clientId, array $typeIds): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('client_property_types')) {
            return;
        }
        $db->table('client_property_types')->where('client_id', $clientId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach (array_unique(array_filter($typeIds)) as $tid) {
            $db->table('client_property_types')->insert([
                'client_id'        => $clientId,
                'property_type_id' => (int) $tid,
                'created_at'       => $now,
            ]);
        }
    }

    public function savePivotZones(int $clientId, array $zoneIds): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('client_search_zones')) {
            return;
        }
        $db->table('client_search_zones')->where('client_id', $clientId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach (array_unique(array_filter($zoneIds)) as $zid) {
            $db->table('client_search_zones')->insert([
                'client_id'  => $clientId,
                'zone_id'    => (int) $zid,
                'created_at' => $now,
            ]);
        }
    }

    public function savePivotFeatures(int $clientId, array $featuresRequired, array $featuresOptional): void
    {
        $db = \Config\Database::connect();
        if (! $db->tableExists('client_features')) {
            return;
        }
        $db->table('client_features')->where('client_id', $clientId)->delete();
        $now = date('Y-m-d H:i:s');
        foreach ($featuresRequired as $key) {
            if (! $key) continue;
            $db->table('client_features')->insert([
                'client_id'        => $clientId,
                'feature_key'      => $key,
                'requirement_type' => 'obligatoire',
                'weight'           => 2,
                'created_at'       => $now,
            ]);
        }
        foreach ($featuresOptional as $key) {
            if (! $key) continue;
            $db->table('client_features')->insert([
                'client_id'        => $clientId,
                'feature_key'      => $key,
                'requirement_type' => 'optionnel',
                'weight'           => 1,
                'created_at'       => $now,
            ]);
        }
    }

    public function countByType(): array
    {
        $db = \Config\Database::connect();
        $rows = $db->table('clients')
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
}
