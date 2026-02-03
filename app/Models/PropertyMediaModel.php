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
        'file_name',
        'file_size',
        'mime_type',
        'title',
        'description',
        'display_order',
        'is_primary'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'property_id' => 'required|is_natural_no_zero',
        'type'        => 'required|in_list[image,video,document]',
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
                    ->where('type', 'image')
                    ->orderBy('is_primary', 'DESC')
                    ->orderBy('display_order', 'ASC')
                    ->findAll();
    }

    /**
     * Récupérer l'image principale d'un bien
     */
    public function getPrimaryImage($propertyId)
    {
        return $this->where('property_id', $propertyId)
                    ->where('type', 'image')
                    ->where('is_primary', 1)
                    ->first();
    }

    /**
     * Définir une image comme principale
     */
    public function setPrimaryImage($propertyId, $mediaId)
    {
        // Retirer le statut primary de toutes les images
        $this->where('property_id', $propertyId)
             ->set(['is_primary' => 0])
             ->update();

        // Définir la nouvelle image principale
        return $this->update($mediaId, ['is_primary' => 1]);
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
