<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table          = 'properties';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'reference', 'agent_id', 'title', 'description', 'type', 'transaction_type',
        'status', 'price', 'surface', 'rooms', 'bedrooms', 'bathrooms',
        'floor', 'total_floors', 'parking', 'furnished',
        'address', 'city', 'zone', 'latitude', 'longitude', 'features',
        'is_published', 'published_at', 'published_by', 'featured', 'views_count',
    ];

    // --------------------------------------------------------

    /**
     * Filtre + pagination pour la liste admin.
     */
    public function getFiltered(array $filters = [], int $perPage = 20): array
    {
        $builder = $this->db->table('properties p')
            ->select('p.*, u.first_name, u.last_name,
                      (SELECT path FROM property_images WHERE property_id = p.id AND is_primary = 1 LIMIT 1) AS primary_image')
            ->join('users u', 'u.id = p.agent_id')
            ->where('p.deleted_at IS NULL');

        if (! empty($filters['status'])) {
            $builder->where('p.status', $filters['status']);
        }
        if (! empty($filters['type'])) {
            $builder->where('p.type', $filters['type']);
        }
        if (! empty($filters['agent_id'])) {
            $builder->where('p.agent_id', $filters['agent_id']);
        }
        if (! empty($filters['city'])) {
            $builder->like('p.city', $filters['city']);
        }
        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('p.title', $filters['search'])
                ->orLike('p.reference', $filters['search'])
                ->orLike('p.address', $filters['search'])
                ->groupEnd();
        }

        $total   = $builder->countAllResults(false);
        $page    = max(1, (int) ($filters['page'] ?? 1));
        $offset  = ($page - 1) * $perPage;
        $results = $builder->orderBy('p.created_at', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        return [
            'data'      => $results,
            'total'     => $total,
            'per_page'  => $perPage,
            'page'      => $page,
            'pages'     => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Trouve un bien avec toutes ses images.
     */
    public function findWithImages(int $id): ?array
    {
        $property = $this->db->table('properties p')
            ->select('p.*, u.first_name, u.last_name, u.email AS agent_email')
            ->join('users u', 'u.id = p.agent_id')
            ->where('p.id', $id)
            ->where('p.deleted_at IS NULL')
            ->get()->getRowArray();

        if (! $property) {
            return null;
        }

        $property['images'] = $this->db->table('property_images')
            ->where('property_id', $id)
            ->orderBy('sort_order')
            ->get()
            ->getResultArray();

        return $property;
    }

    /**
     * Génère une référence unique (ex: REB-2024-00042).
     */
    public function generateReference(): string
    {
        $year  = date('Y');
        $last  = $this->db->table('properties')
            ->selectMax('id')
            ->get()
            ->getRow();
        $seq   = ($last->id ?? 0) + 1;

        return sprintf('REB-%s-%05d', $year, $seq);
    }

    /**
     * Statistiques pour le dashboard.
     */
    public function getStats(): array
    {
        $db = $this->db;

        return [
            'total'     => (int) $db->table('properties')->where('deleted_at IS NULL')->countAllResults(),
            'available' => (int) $db->table('properties')->where('status', 'available')->where('deleted_at IS NULL')->countAllResults(),
            'sold'      => (int) $db->table('properties')->where('status', 'sold')->where('deleted_at IS NULL')->countAllResults(),
            'reserved'  => (int) $db->table('properties')->where('status', 'reserved')->where('deleted_at IS NULL')->countAllResults(),
            'published' => (int) $db->table('properties')->where('is_published', 1)->where('deleted_at IS NULL')->countAllResults(),
        ];
    }

    /**
     * Enregistre l'historique d'une modification.
     */
    public function logChange(int $propertyId, int $userId, string $field, $old, $new): void
    {
        $this->db->table('property_history')->insert([
            'property_id'  => $propertyId,
            'user_id'      => $userId,
            'action'       => 'update',
            'field_changed'=> $field,
            'old_value'    => $old,
            'new_value'    => $new,
            'created_at'   => date('Y-m-d H:i:s'),
        ]);
    }
}
