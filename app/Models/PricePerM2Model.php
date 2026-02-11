<?php

namespace App\Models;

use CodeIgniter\Model;

class PricePerM2Model extends Model
{
    protected $table = 'price_per_m2';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'zone_id', 'city', 'governorate', 'property_type', 'transaction_type',
        'price_min', 'price_max', 'price_average', 'surface_average',
        'properties_count', 'evolution', 'period', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'price_average' => 'required|decimal',
        'property_type' => 'required|in_list[apartment,villa,studio,office,shop,warehouse,land]',
        'transaction_type' => 'required|in_list[sale,rent]',
    ];

    public function getPricesWithZones($filters = [])
    {
        $builder = $this->select('price_per_m2.*, zones.name as zone_name, zones.name_ar as zone_name_ar')
            ->join('zones', 'zones.id = price_per_m2.zone_id', 'left')
            ->where('price_per_m2.is_active', 1)
            ->orderBy('price_per_m2.created_at', 'DESC');

        if (!empty($filters['governorate'])) {
            $builder->where('price_per_m2.governorate', $filters['governorate']);
        }

        if (!empty($filters['city'])) {
            $builder->where('price_per_m2.city', $filters['city']);
        }

        if (!empty($filters['zone_id'])) {
            $builder->where('price_per_m2.zone_id', $filters['zone_id']);
        }

        if (!empty($filters['property_type'])) {
            $builder->where('price_per_m2.property_type', $filters['property_type']);
        }

        if (!empty($filters['transaction_type'])) {
            $builder->where('price_per_m2.transaction_type', $filters['transaction_type']);
        }

        return $builder->findAll();
    }

    public function getByLocation($governorate = null, $city = null, $zoneId = null)
    {
        $builder = $this->where('is_active', 1);

        if ($zoneId) {
            $builder->where('zone_id', $zoneId);
        } elseif ($city) {
            $builder->where('city', $city);
        } elseif ($governorate) {
            $builder->where('governorate', $governorate);
        }

        return $builder->findAll();
    }

    public function getStatsByType()
    {
        return $this->select('property_type, transaction_type, AVG(price_average) as avg_price, COUNT(*) as count')
            ->where('is_active', 1)
            ->groupBy(['property_type', 'transaction_type'])
            ->findAll();
    }
}
