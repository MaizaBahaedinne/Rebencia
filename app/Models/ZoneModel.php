<?php

namespace App\Models;

use CodeIgniter\Model;

class ZoneModel extends Model
{
    protected $table          = 'zones';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'type', 'name', 'code', 'parent_id', 'is_active',
    ];

    /** Métadonnées par type : label, icône Bootstrap Icons, couleur Bootstrap. */
    public const TYPE_META = [
        'pays'     => ['label' => 'Pays',         'icon' => 'bi-globe2',       'color' => 'primary'],
        'region'   => ['label' => 'Région / État', 'icon' => 'bi-map',          'color' => 'success'],
        'ville'    => ['label' => 'Ville',         'icon' => 'bi-buildings',    'color' => 'info'],
        'quartier' => ['label' => 'Quartier',      'icon' => 'bi-geo-alt-fill', 'color' => 'warning'],
    ];

    // ----------------------------------------------------------------
    // Requêtes
    // ----------------------------------------------------------------

    /**
     * Zones d'un type donné avec le nom du parent direct.
     */
    public function getWithParent(array $filters = []): array
    {
        $q = $this->db->table('zones z')
            ->select('z.id, z.type, z.name, z.code, z.is_active, z.parent_id, z.created_at,
                      p.name AS parent_name, p.type AS parent_type')
            ->join('zones p', 'p.id = z.parent_id AND p.deleted_at IS NULL', 'left')
            ->where('z.deleted_at IS NULL');

        if (! empty($filters['type'])) {
            $q->where('z.type', $filters['type']);
        }
        if (! empty($filters['parent_id'])) {
            $q->where('z.parent_id', (int) $filters['parent_id']);
        }
        if (! empty($filters['search'])) {
            $q->groupStart()
              ->like('z.name', $filters['search'])
              ->orLike('z.code', $filters['search'])
              ->groupEnd();
        }

        // Limite pour éviter timeout sur hébergement partagé (quartiers = milliers de lignes)
        $limit  = (int) ($filters['limit']  ?? 500);
        $offset = (int) ($filters['offset'] ?? 0);

        return $q->orderBy('z.name', 'ASC')->limit($limit, $offset)->get()->getResultArray();
    }

    /**
     * Enfants directs d'une zone (soft-delete respecté par le modèle).
     */
    public function getChildren(int $parentId): array
    {
        return $this->where('parent_id', $parentId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Zones actives d'un type précis (pour les <select> serveur).
     */
    public function getByType(string $type): array
    {
        return $this->where('type', $type)
                    ->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Enfants directs d'une zone — format allégé pour AJAX (id, name, code).
     */
    public function getByParent(int $parentId): array
    {
        return $this->db->table('zones')
            ->select('id, name, code')
            ->where('parent_id', $parentId)
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Comptage par type (dashboard du module).
     */
    public function countByType(): array
    {
        $rows = $this->db->table('zones')
            ->select('type, COUNT(*) AS total')
            ->where('deleted_at IS NULL')
            ->groupBy('type')
            ->get()
            ->getResultArray();

        $totals = ['pays' => 0, 'region' => 0, 'ville' => 0, 'quartier' => 0];
        foreach ($rows as $row) {
            if (isset($totals[$row['type']])) {
                $totals[$row['type']] = (int) $row['total'];
            }
        }
        return $totals;
    }

    /**
     * Remonte la chaîne parente d'une zone.
     * Retourne ['pays' => zone|null, 'region' => zone|null, 'ville' => zone|null]
     */
    public function getParentChain(array $zone): array
    {
        $chain   = ['pays' => null, 'region' => null, 'ville' => null];
        $current = $zone;

        while (! empty($current['parent_id'])) {
            $parent = $this->find((int) $current['parent_id']);
            if (! $parent) {
                break;
            }
            if (array_key_exists($parent['type'], $chain)) {
                $chain[$parent['type']] = $parent;
            }
            $current = $parent;
        }

        return $chain;
    }
}
