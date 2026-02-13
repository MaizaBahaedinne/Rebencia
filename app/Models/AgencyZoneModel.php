<?php

namespace App\Models;

use CodeIgniter\Model;

class AgencyZoneModel extends Model
{
    protected $table = 'agency_zones';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'agency_id', 'zone_id', 'boundary_coordinates', 'is_primary'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all zones assigned to an agency
     */
    public function getAgencyZones($agencyId)
    {
        return $this->select('agency_zones.*, zones.name, zones.name_ar, zones.type, zones.latitude, zones.longitude')
            ->join('zones', 'zones.id = agency_zones.zone_id')
            ->where('agency_zones.agency_id', $agencyId)
            ->findAll();
    }

    /**
     * Get all agencies assigned to a zone
     */
    public function getZoneAgencies($zoneId)
    {
        return $this->select('agency_zones.*, agencies.name as agency_name, agencies.code')
            ->join('agencies', 'agencies.id = agency_zones.agency_id')
            ->where('agency_zones.zone_id', $zoneId)
            ->findAll();
    }

    /**
     * Assign multiple zones to an agency
     */
    public function assignZonesToAgency($agencyId, $zones)
    {
        // Delete existing assignments
        $this->where('agency_id', $agencyId)->delete();

        // Insert new assignments
        $data = [];
        foreach ($zones as $zone) {
            $data[] = [
                'agency_id' => $agencyId,
                'zone_id' => $zone['zone_id'],
                'boundary_coordinates' => $zone['coordinates'] ?? null,
                'is_primary' => $zone['is_primary'] ?? 0,
            ];
        }

        if (!empty($data)) {
            return $this->insertBatch($data);
        }

        return true;
    }

    /**
     * Get all zone assignments with agency and zone details
     */
    public function getAllAssignments()
    {
        return $this->select('agency_zones.*, 
                             agencies.name as agency_name, 
                             agencies.code as agency_code,
                             zones.name as zone_name, 
                             zones.type as zone_type,
                             zones.latitude,
                             zones.longitude')
            ->join('agencies', 'agencies.id = agency_zones.agency_id')
            ->join('zones', 'zones.id = agency_zones.zone_id')
            ->orderBy('agencies.name', 'ASC')
            ->orderBy('agency_zones.is_primary', 'DESC')
            ->findAll();
    }
}
