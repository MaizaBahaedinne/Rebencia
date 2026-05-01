<?php

namespace App\Models;

use CodeIgniter\Model;

class AgencyModel extends Model
{
    protected $table          = 'agencies';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'name', 'slug', 'email', 'phone', 'address', 'city',
        'logo', 'description', 'zone_id', 'is_active',
    ];

    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[150]',
        'slug' => 'required|min_length[2]|max_length[160]',
    ];

    // --------------------------------------------------------
    // Helpers
    // --------------------------------------------------------

    /**
     * Génère un slug unique à partir du nom.
     */
    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug  = strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $name), '-'));
        $base  = $slug;
        $i     = 1;

        while (true) {
            $builder = $this->db->table('agencies')->where('slug', $slug)->where('deleted_at IS NULL');
            if ($excludeId !== null) {
                $builder->where('id !=', $excludeId);
            }
            if ($builder->countAllResults() === 0) {
                break;
            }
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Liste des agences actives (pour selects).
     */
    public function getActive(): array
    {
        try {
            return $this->where('is_active', 1)
                        ->where('deleted_at IS NULL')
                        ->orderBy('name', 'ASC')
                        ->findAll();
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Liste enrichie (avec nb users + nb biens).
     */
    public function getList(array $filters = []): array
    {
        $builder = $this->db->table('agencies a')
            ->select('a.*,
                (SELECT COUNT(*) FROM users   u WHERE u.agency_id = a.id AND u.deleted_at   IS NULL) AS users_count,
                (SELECT COUNT(*) FROM properties p WHERE p.agency_id = a.id AND p.deleted_at IS NULL) AS properties_count')
            ->where('a.deleted_at IS NULL');

        if (isset($filters['is_active'])) {
            $builder->where('a.is_active', (int) $filters['is_active']);
        }
        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('a.name', $filters['search'])
                ->orLike('a.city', $filters['search'])
                ->orLike('a.email', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('a.name', 'ASC')->get()->getResultArray();
    }

    /**
     * Détail agence + nb membres + nb biens + zone.
     */
    public function findDetail(int $id): ?array
    {
        $row = $this->db->table('agencies a')
            ->select('a.*,
                z.name AS zone_name,
                (SELECT COUNT(*) FROM users      u WHERE u.agency_id = a.id AND u.deleted_at   IS NULL) AS users_count,
                (SELECT COUNT(*) FROM properties p WHERE p.agency_id = a.id AND p.deleted_at   IS NULL) AS properties_count')
            ->join('zones z', 'z.id = a.zone_id', 'left')
            ->where('a.id', $id)
            ->where('a.deleted_at IS NULL')
            ->get()->getRowArray();

        return $row ?: null;
    }

    /**
     * Membres d'une agence.
     */
    public function getMembers(int $agencyId): array
    {
        return $this->db->table('users u')
            ->select('u.id, u.first_name, u.last_name, u.email, u.phone,
                      u.status, u.avatar, u.last_login_at,
                      COALESCE(r.label, r.name) AS role_label, r.color AS role_color, r.name AS role_name')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.agency_id', $agencyId)
            ->where('u.deleted_at IS NULL')
            ->orderBy('u.first_name', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * Biens d'une agence (résumé).
     */
    public function getProperties(int $agencyId, int $limit = 10): array
    {
        return $this->db->table('properties p')
            ->select('p.id, p.reference, p.title, p.type, p.transaction_type, p.status, p.price, p.city,
                      u.first_name, u.last_name,
                      (SELECT path FROM property_images WHERE property_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image')
            ->join('users u', 'u.id = p.agent_id')
            ->where('p.agency_id', $agencyId)
            ->where('p.deleted_at IS NULL')
            ->orderBy('p.created_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}
