<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PropertyDocumentModel
 * Gestion des documents d'un bien immobilier
 */
class PropertyDocumentModel extends Model
{
    protected $table = 'property_documents';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'property_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'description',
        'uploaded_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'file_size' => 'int'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id' => 'required|is_natural_no_zero',
        'document_type' => 'required|in_list[contrat,titre_foncier,plan,diagnostic_performance_energetique,diagnostic_technique,certificat_conformite,autorisation_construction,photo,autre]',
        'file_name' => 'required|max_length[255]',
        'file_path' => 'required|max_length[500]'
    ];

    protected $validationMessages = [
        'property_id' => [
            'required' => 'L\'ID du bien est requis',
            'is_natural_no_zero' => 'L\'ID du bien doit être un nombre valide'
        ],
        'document_type' => [
            'required' => 'Le type de document est requis',
            'in_list' => 'Le type de document n\'est pas valide'
        ],
        'file_name' => [
            'required' => 'Le nom du fichier est requis',
            'max_length' => 'Le nom ne peut pas dépasser 255 caractères'
        ],
        'file_path' => [
            'required' => 'Le chemin du fichier est requis',
            'max_length' => 'Le chemin ne peut pas dépasser 500 caractères'
        ]
    ];

    /**
     * Récupérer tous les documents d'un bien
     */
    public function getDocumentsByProperty(int $propertyId, ?string $documentType = null): array
    {
        $builder = $this->where('property_id', $propertyId);
        
        if ($documentType) {
            $builder->where('document_type', $documentType);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Récupérer les photos d'un bien
     */
    public function getPhotosByProperty(int $propertyId): array
    {
        return $this->getDocumentsByProperty($propertyId, 'photo');
    }

    /**
     * Supprimer tous les documents d'un bien
     */
    public function deleteByProperty(int $propertyId): bool
    {
        return $this->where('property_id', $propertyId)->delete();
    }

    /**
     * Supprimer un document et son fichier physique
     */
    public function deleteDocument(int $documentId): bool
    {
        $document = $this->find($documentId);
        
        if (!$document) {
            return false;
        }

        // Supprimer le fichier physique
        $fullPath = FCPATH . $document['file_path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Supprimer l'enregistrement
        return $this->delete($documentId);
    }

    /**
     * Compter les documents par type
     */
    public function countByType(int $propertyId): array
    {
        $result = $this->select('document_type, COUNT(*) as count')
                       ->where('property_id', $propertyId)
                       ->groupBy('document_type')
                       ->findAll();
        
        $counts = [];
        foreach ($result as $row) {
            $counts[$row['document_type']] = (int) $row['count'];
        }
        
        return $counts;
    }
}
