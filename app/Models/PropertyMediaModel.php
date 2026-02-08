<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyMediaModel extends Model
{
    protected $table            = 'property_media';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'type',
        'file_path',
        'title',
        'description',
        'display_order',
        'is_main'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    // Validation
    protected $validationRules      = [
        'property_id' => 'required|is_natural_no_zero',
        'type'        => 'required|in_list[photo,video,document,plan]',
        'file_path'   => 'required|max_length[255]'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Récupérer toutes les images d'un bien
     */
    public function getPropertyImages($propertyId)
    {
        return $this->where('property_id', $propertyId)
                    ->where('type', 'photo')
                    ->orderBy('display_order', 'ASC')
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Récupérer l'image principale d'un bien
     */
    public function getPrimaryImage($propertyId)
    {
        return $this->where('property_id', $propertyId)
                    ->where('type', 'image')
                    ->orderBy('display_order', 'ASC')
                    ->first();
    }

    /**
     * Définir une image comme principale
     */
    public function setPrimaryImage($propertyId, $mediaId)
    {
        // Définir display_order = 0 pour cette image (sera affichée en premier)
        return $this->update($mediaId, ['display_order' => 0]);
    }

    /**
     * Supprimer physiquement le fichier
     */
    public function deleteMediaFile($mediaId)
    {
        $media = $this->find($mediaId);
        
        if (!$media) {
            return false;
        }

        $filePath = WRITEPATH . $media['file_path'];
        
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        return $this->delete($mediaId);
    }

    /**
     * Réorganiser l'ordre d'affichage
     */
    public function reorderMedia($propertyId, $orderArray)
    {
        foreach ($orderArray as $order => $mediaId) {
            $this->update($mediaId, ['display_order' => $order + 1]);
        }
        
        return true;
    }

    /**
     * Compter les médias d'un bien
     */
    public function countPropertyMedia($propertyId, $type = null)
    {
        $builder = $this->where('property_id', $propertyId);
        
        if ($type) {
            $builder->where('type', $type);
        }
        
        return $builder->countAllResults();
    }

    /**
     * Supprimer tous les médias d'un bien
     */
    public function deletePropertyMedia($propertyId)
    {
        $media = $this->where('property_id', $propertyId)->findAll();
        
        foreach ($media as $item) {
            $this->deleteMediaFile($item['id']);
        }
        
        return true;
    }
}
