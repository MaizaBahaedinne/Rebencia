<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table = 'properties';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'reference', 'title', 'title_ar', 'title_en',
        'description', 'description_ar', 'description_en',
        'type', 'transaction_type', 'price', 'rental_price',
        'internal_estimation', 'area_total', 'area_living', 'area_land',
        'rooms', 'bedrooms', 'bathrooms', 'floor', 'total_floors',
        'has_elevator', 'has_parking', 'parking_spaces', 'has_garden', 'has_pool',
        'construction_year', 'standing', 'condition_state', 'legal_status',
        'zone_id', 'address', 'city', 'governorate', 'postal_code', 'neighborhood',
        'latitude', 'longitude', 'agency_id', 'agent_id',
        'owner_name', 'owner_phone', 'owner_email',
        'status', 'featured', 'views_count', 'published_at',
        'disponibilite_date', 'hide_address', 'orientation', 'floor_type', 'gas_type',
        'energy_class', 'energy_consumption_kwh', 'co2_emission',
        'promo_price', 'promo_start_date', 'promo_end_date',
        'charge_syndic', 'charge_water', 'charge_gas', 'charge_electricity', 'charge_other',
        'internal_notes', 'created_by'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'has_elevator' => 'boolean',
        'has_parking' => 'boolean',
        'has_garden' => 'boolean',
        'has_pool' => 'boolean',
        'featured' => 'boolean',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'reference' => 'required|is_unique[properties.reference,id,{id}]',
        'title' => 'required|min_length[3]|max_length[255]',
        'type' => 'required',
        'transaction_type' => 'required',
        'price' => 'required|decimal',
    ];

    public function getPropertyWithDetails($id)
    {
        $builder = $this->select('properties.*, zones.name as zone_name, agencies.name as agency_name, CONCAT(users.first_name, " ", users.last_name) as agent_name')
            ->join('zones', 'zones.id = properties.zone_id', 'left')
            ->join('agencies', 'agencies.id = properties.agency_id', 'left')
            ->join('users', 'users.id = properties.agent_id', 'left')
            ->where('properties.id', $id);
        
        // Ne pas appliquer le filtre d'agence pour la vue - tout le monde peut voir tous les biens
        // applyAgencyFilter($builder, 'properties.agency_id');
        
        $property = $builder->first();

        if ($property) {
            // Récupérer les images
            $propertyMediaModel = model('PropertyMediaModel');
            $property['images'] = $propertyMediaModel->where('property_id', $id)->where('type', 'image')->findAll() ?: [];
        }

        return $property;
    }

    public function searchProperties($filters = [])
    {
        $builder = $this->builder();
        
        // Appliquer le filtre d'agence automatiquement
        applyAgencyFilter($builder, 'agency_id');
        
        if (!empty($filters['type'])) {
            $builder->where('type', $filters['type']);
        }
        
        if (!empty($filters['transaction_type'])) {
            $builder->where('transaction_type', $filters['transaction_type']);
        }
        
        if (isset($filters['price_min'])) {
            $builder->where('price >=', $filters['price_min']);
        }
        
        if (isset($filters['price_max'])) {
            $builder->where('price <=', $filters['price_max']);
        }
        
        if (!empty($filters['zone_id'])) {
            $builder->where('zone_id', $filters['zone_id']);
        }
        
        if (isset($filters['rooms_min'])) {
            $builder->where('rooms >=', $filters['rooms_min']);
        }
        
        if (isset($filters['area_min'])) {
            $builder->where('area_total >=', $filters['area_min']);
        }
        
        $builder->where('status', 'published');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Récupérer toutes les propriétés accessibles avec filtrage automatique par agence
     */
    public function getAllWithAgencyFilter($page = 20)
    {
        // Appliquer le filtre d'agence
        applyAgencyFilter($this, 'properties.agency_id');
        
        $this->select('properties.*, zones.name as zone_name, users.first_name as agent_name, agencies.name as agency_name')
            ->join('zones', 'zones.id = properties.zone_id', 'left')
            ->join('users', 'users.id = properties.agent_id', 'left')
            ->join('agencies', 'agencies.id = properties.agency_id', 'left')
            ->orderBy('properties.created_at', 'DESC');
        
        return $this->paginate($page);
    }
}
