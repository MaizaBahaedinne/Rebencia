<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyTypeModel extends Model
{
    protected $table          = 'property_types';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'name', 'slug', 'icon', 'description', 'is_active',
    ];

    /**
     * Tous les types, soft-delete respecté.
     */
    public function getAll(): array
    {
        return $this->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Types actifs uniquement (pour les formulaires de biens).
     */
    public function getActive(): array
    {
        return $this->where('deleted_at', null)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Vérifie si un slug est déjà utilisé (hors $excludeId).
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->where('slug', $slug)->where('deleted_at', null);
        if ($excludeId !== null) {
            $builder = $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
