<?php

namespace App\Models;

use CodeIgniter\Model;

class SliderModel extends Model
{
    protected $table            = 'sliders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'subtitle',
        'description',
        'image',
        'button_text',
        'button_link',
        'button_text_2',
        'button_link_2',
        'display_order',
        'is_active',
        'animation_type',
        'text_position',
        'overlay_opacity'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [
        'title' => 'required|max_length[255]',
        'display_order' => 'permit_empty|integer',
        'is_active' => 'permit_empty|in_list[0,1]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
    
    /**
     * Get active sliders ordered by display order
     */
    public function getActiveSliders()
    {
        return $this->where('is_active', 1)
                    ->orderBy('display_order', 'ASC')
                    ->findAll();
    }
    
    /**
     * Get next display order
     */
    public function getNextOrder()
    {
        $result = $this->selectMax('display_order')->first();
        return ($result['display_order'] ?? 0) + 1;
    }
}
