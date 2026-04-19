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

    // --------------------------------------------------------
    // Requêtes complexes
    // --------------------------------------------------------

    /**
     * Retourne les zones avec le nom & type de leur parent.
     * Bypass soft-delete géré manuellement via z.deleted_at IS NULL.
     */
    public function getWithParent(array $filters = []): array
    {
        $q = $this->db->table('zones z')
            ->select('z.id, z.type, z.name, z.code, z.is_active, z.parent_id, z.created_at,
                      p.name AS parent_name, p.type AS parent_type')
            ->join('zones p', 'p.id = z.parent_id', 'left')
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

        return $q->orderBy('z.type, z.name', 'ASC')->get()->getResultArray();
    }

    /**
     * Enfants directs d'une zone (soft-delete géré par le modèle).
     */
    public function getChildren(int $parentId): array
    {
        return $this->where('parent_id', $parentId)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Liste les zones actives d'un type précis (pour les <select>).
     */
    public function getByType(string $type): array
    {
        return $this->where('type', $type)
                    ->where('is_active', 1)
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Comptage par type (pour le tableau de bord du module).
     */
    public function countByType(): array
    {
        $rows = $this->db->table('zones')
            ->select('type, COUNT(*) AS total')
            ->where('deleted_at IS NULL')
            ->where('is_active', 1)
            ->groupBy('type')
            ->get()
            ->getResultArray();

        $totals = ['pays' => 0, 'region' => 0, 'ville' => 0, 'code_postal' => 0];
        foreach ($rows as $row) {
            $totals[$row['type']] = (int) $row['total'];
        }
        return $totals;
    }
}
