<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyCharacteristicModel extends Model
{
    protected $table          = 'property_characteristics';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $deletedField   = 'deleted_at';

    protected $allowedFields = [
        'key', 'label', 'icon', 'type', 'unit',
        'options', 'applies_to', 'required_for',
        'sort_order', 'is_active',
    ];

    /**
     * Toutes les caractéristiques actives, triées, optionnellement filtrées par type de bien.
     */
    public function getActive(?string $propertyType = null): array
    {
        $builder = $this->select('*')
            ->where('is_active', 1)
            ->where('deleted_at IS NULL')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('label', 'ASC');

        $rows = $builder->findAll();

        if ($propertyType === null) {
            return $rows;
        }

        // Filtrer côté PHP : applies_to IS NULL → applicable à tous
        return array_filter($rows, static function (array $row) use ($propertyType): bool {
            if ($row['applies_to'] === null) {
                return true;
            }
            $types = json_decode($row['applies_to'], true) ?? [];
            return in_array($propertyType, $types, true);
        });
    }

    /**
     * Liste pour la gestion CRUD (soft-delete respecté).
     */
    public function getAll(): array
    {
        return $this->select('*')
            ->where('deleted_at IS NULL')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('label', 'ASC')
            ->findAll();
    }

    /**
     * Vérifie si une clé technique est déjà utilisée (pour unicité).
     */
    public function keyExists(string $key, ?int $excludeId = null): bool
    {
        $builder = $this->where('`key`', $key)->where('deleted_at IS NULL');
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
