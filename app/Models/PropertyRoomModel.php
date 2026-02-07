<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PropertyRoomModel
 * Gestion des pièces d'un bien immobilier
 */
class PropertyRoomModel extends Model
{
    protected $table = 'property_rooms';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'property_id',
        'name_fr',
        'name_ar',
        'room_type',
        'surface',
        'width',
        'length',
        'height',
        'has_window',
        'window_type',
        'orientation',
        'sort_order'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'surface' => 'float',
        'width' => 'float',
        'length' => 'float',
        'height' => 'float',
        'has_window' => 'boolean'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id' => 'required|is_natural_no_zero',
        'name_fr' => 'required|min_length[2]|max_length[100]',
        'room_type' => 'required|in_list[bedroom,bathroom,kitchen,living,dining,office,storage,utility,other]',
        'surface' => 'permit_empty|decimal|greater_than[0]'
    ];

    protected $validationMessages = [
        'property_id' => [
            'required' => 'L\'ID du bien est requis',
            'is_natural_no_zero' => 'L\'ID du bien doit être un nombre valide'
        ],
        'name_fr' => [
            'required' => 'Le nom de la pièce est requis',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères'
        ],
        'room_type' => [
            'required' => 'Le type de pièce est requis',
            'in_list' => 'Le type de pièce n\'est pas valide'
        ],
        'surface' => [
            'decimal' => 'La surface doit être un nombre',
            'greater_than' => 'La surface doit être supérieure à 0'
        ]
    ];

    /**
     * Récupérer toutes les pièces d'un bien
     */
    public function getRoomsByProperty(int $propertyId): array
    {
        return $this->where('property_id', $propertyId)
                    ->orderBy('sort_order', 'ASC')
                    ->orderBy('room_type', 'ASC')
                    ->findAll();
    }

    /**
     * Calculer la surface totale des pièces
     */
    public function getTotalAreaByProperty(int $propertyId): float
    {
        $result = $this->selectSum('surface', 'total')
                       ->where('property_id', $propertyId)
                       ->first();
        
        return (float) ($result['total'] ?? 0);
    }

    /**
     * Supprimer toutes les pièces d'un bien
     */
    public function deleteByProperty(int $propertyId): bool
    {
        return $this->where('property_id', $propertyId)->delete();
    }

    /**
     * Sauvegarder plusieurs pièces d'un coup
     */
    public function saveRooms(int $propertyId, array $rooms): bool
    {
        // Supprimer les anciennes pièces
        $this->deleteByProperty($propertyId);

        // Insérer les nouvelles
        if (empty($rooms)) {
            return true;
        }

        $data = [];
        $sortOrder = 1;
        foreach ($rooms as $room) {
            if (!empty($room['room_name']) || !empty($room['surface'])) {
                $data[] = [
                    'property_id' => $propertyId,
                    'name_fr' => $room['room_name'] ?? '',
                    'name_ar' => $room['name_ar'] ?? null,
                    'room_type' => $room['room_type'] ?? 'other',
                    'surface' => $room['area_m2'] ?? $room['surface'] ?? null,
                    'sort_order' => $sortOrder++
                ];
            }
        }

        if (empty($data)) {
            return true;
        }

        return $this->insertBatch($data) !== false;
    }
}
