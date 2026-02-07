<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PropertyProximityModel
 * Gestion des proximités d'un bien immobilier
 */
class PropertyProximityModel extends Model
{
    protected $table = 'property_proximity';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'property_id',
        'proximity_type',
        'has_access',
        'distance_m',
        'distance_text'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'has_access' => 'boolean',
        'distance_m' => 'int'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id' => 'required|is_natural_no_zero',
        'proximity_type' => 'required|in_list[transport_public,ecole,administration,municipalite,hopital,commerces,mosquee,eglise,parc,plage,autre]',
        'has_access' => 'in_list[0,1]',
        'distance_m' => 'permit_empty|is_natural'
    ];

    protected $validationMessages = [
        'property_id' => [
            'required' => 'L\'ID du bien est requis',
            'is_natural_no_zero' => 'L\'ID du bien doit être un nombre valide'
        ],
        'proximity_type' => [
            'required' => 'Le type de proximité est requis',
            'in_list' => 'Le type de proximité n\'est pas valide'
        ]
    ];

    /**
     * Récupérer toutes les proximités d'un bien
     */
    public function getProximitiesByProperty(int $propertyId): array
    {
        return $this->where('property_id', $propertyId)
                    ->orderBy('proximity_type', 'ASC')
                    ->findAll();
    }

    /**
     * Supprimer toutes les proximités d'un bien
     */
    public function deleteByProperty(int $propertyId): bool
    {
        return $this->where('property_id', $propertyId)->delete();
    }

    /**
     * Sauvegarder plusieurs proximités d'un coup
     */
    public function saveProximities(int $propertyId, array $proximities): bool
    {
        // Supprimer les anciennes proximités
        $this->deleteByProperty($propertyId);

        // Insérer les nouvelles
        if (empty($proximities)) {
            return true;
        }

        $data = [];
        foreach ($proximities as $proximity) {
            if (!empty($proximity['proximity_type'])) {
                $data[] = [
                    'property_id' => $propertyId,
                    'proximity_type' => $proximity['proximity_type'],
                    'has_access' => $proximity['has_access'] ?? 1,
                    'distance_m' => !empty($proximity['distance_m']) ? $proximity['distance_m'] : null,
                    'distance_text' => $proximity['distance_text'] ?? null
                ];
            }
        }

        if (empty($data)) {
            return true;
        }

        return $this->insertBatch($data) !== false;
    }

    /**
     * Obtenir les proximités groupées par type
     */
    public function getProximitiesGrouped(int $propertyId): array
    {
        $proximities = $this->getProximitiesByProperty($propertyId);
        
        $grouped = [];
        foreach ($proximities as $proximity) {
            $grouped[$proximity['proximity_type']] = $proximity;
        }
        
        return $grouped;
    }
}
