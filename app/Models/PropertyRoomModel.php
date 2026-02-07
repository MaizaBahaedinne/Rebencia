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
        'room_name',
        'room_type',
        'area_m2'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'area_m2' => 'float'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id' => 'required|is_natural_no_zero',
        'room_name' => 'required|min_length[2]|max_length[100]',
        'room_type' => 'required|in_list[salon,chambre,cuisine,salle_bain,wc,bureau,dressing,cave,garage,terrasse,balcon,autre]',
        'area_m2' => 'required|decimal|greater_than[0]'
    ];

    protected $validationMessages = [
        'property_id' => [
            'required' => 'L\'ID du bien est requis',
            'is_natural_no_zero' => 'L\'ID du bien doit être un nombre valide'
        ],
        'room_name' => [
            'required' => 'Le nom de la pièce est requis',
            'min_length' => 'Le nom doit contenir au moins 2 caractères',
            'max_length' => 'Le nom ne peut pas dépasser 100 caractères'
        ],
        'room_type' => [
            'required' => 'Le type de pièce est requis',
            'in_list' => 'Le type de pièce n\'est pas valide'
        ],
        'area_m2' => [
            'required' => 'La surface est requise',
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
                    ->orderBy('room_type', 'ASC')
                    ->orderBy('room_name', 'ASC')
                    ->findAll();
    }

    /**
     * Calculer la surface totale des pièces
     */
    public function getTotalAreaByProperty(int $propertyId): float
    {
        $result = $this->selectSum('area_m2', 'total')
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
        foreach ($rooms as $room) {
            if (!empty($room['room_name']) && !empty($room['area_m2'])) {
                $data[] = [
                    'property_id' => $propertyId,
                    'room_name' => $room['room_name'],
                    'room_type' => $room['room_type'] ?? 'autre',
                    'area_m2' => $room['area_m2']
                ];
            }
        }

        if (empty($data)) {
            return true;
        }

        return $this->insertBatch($data) !== false;
    }
}
