<?php

namespace App\Models;

use CodeIgniter\Model;

class SearchAlertModel extends Model
{
    protected $table = 'search_alerts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'client_id', 'first_name', 'last_name', 'email', 'phone',
        'property_type', 'transaction_type',
        'price_min', 'price_max', 'area_min', 'area_max',
        'rooms_min', 'bedrooms_min', 'bathrooms_min',
        'zones', 'cities', 'governorates',
        'has_elevator', 'has_parking', 'has_garden', 'has_pool',
        'frequency', 'is_active', 'last_sent_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'has_elevator' => 'boolean',
        'has_parking' => 'boolean',
        'has_garden' => 'boolean',
        'has_pool' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|max_length[100]',
        'last_name' => 'required|max_length[100]',
        'email' => 'required|valid_email|max_length[255]',
        'transaction_type' => 'required|in_list[sale,rent,both]',
        'frequency' => 'required|in_list[instant,daily,weekly]',
    ];

    protected $validationMessages = [
        'first_name' => [
            'required' => 'Le prénom est obligatoire',
        ],
        'last_name' => [
            'required' => 'Le nom est obligatoire',
        ],
        'email' => [
            'required' => 'L\'email est obligatoire',
            'valid_email' => 'L\'email doit être valide',
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Get alerts with related data
    public function getAlertsWithDetails($limit = null, $offset = null)
    {
        $builder = $this->select('search_alerts.*, 
                                  clients.first_name as client_first_name, 
                                  clients.last_name as client_last_name')
                        ->join('clients', 'clients.id = search_alerts.client_id', 'left')
                        ->orderBy('search_alerts.created_at', 'DESC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    // Get active alerts for sending
    public function getAlertsToSend($frequency = 'daily')
    {
        $builder = $this->where('is_active', 1)
                        ->where('frequency', $frequency);

        if ($frequency === 'daily') {
            $builder->where('(last_sent_at IS NULL OR last_sent_at < DATE_SUB(NOW(), INTERVAL 1 DAY))');
        } elseif ($frequency === 'weekly') {
            $builder->where('(last_sent_at IS NULL OR last_sent_at < DATE_SUB(NOW(), INTERVAL 7 DAY))');
        }

        return $builder->get()->getResultArray();
    }

    // Update last sent timestamp
    public function updateLastSent($alertId)
    {
        return $this->update($alertId, ['last_sent_at' => date('Y-m-d H:i:s')]);
    }
}
