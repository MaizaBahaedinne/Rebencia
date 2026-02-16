<?php

namespace App\Models;

use CodeIgniter\Model;

class ObjectiveModel extends Model
{
    protected $table = 'objectives';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'type',
        'user_id',
        'agency_id',
        'period',
        'revenue_target',
        'new_contacts_target',
        'properties_rent_target',
        'properties_sale_target',
        'transactions_target',
        'revenue_achieved',
        'new_contacts_achieved',
        'properties_rent_achieved',
        'properties_sale_achieved',
        'transactions_achieved',
        'status',
        'notes',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'type' => 'required|in_list[personal,agency]',
        'period' => 'required|regex_match[/^\d{4}-\d{2}$/]',
    ];

    protected $validationMessages = [
        'type' => [
            'required' => 'Le type est requis',
            'in_list' => 'Type invalide'
        ],
        'period' => [
            'required' => 'La période est requise',
            'regex_match' => 'Format de période invalide (YYYY-MM)'
        ]
    ];

    /**
     * Get objectives with user and agency details
     */
    public function getObjectivesWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table);
        $builder->select('objectives.*, 
                         users.first_name as user_first_name,
                         users.last_name as user_last_name,
                         agencies.name as agency_name,
                         creator.first_name as creator_first_name,
                         creator.last_name as creator_last_name');
        $builder->join('users', 'users.id = objectives.user_id', 'left');
        $builder->join('agencies', 'agencies.id = objectives.agency_id', 'left');
        $builder->join('users as creator', 'creator.id = objectives.created_by', 'left');

        // Apply filters
        if (!empty($filters['id'])) {
            $builder->where('objectives.id', $filters['id']);
        }

        if (!empty($filters['type'])) {
            $builder->where('objectives.type', $filters['type']);
        }

        if (!empty($filters['user_id'])) {
            $builder->where('objectives.user_id', $filters['user_id']);
        }

        if (!empty($filters['agency_id'])) {
            $builder->where('objectives.agency_id', $filters['agency_id']);
        }

        if (!empty($filters['period'])) {
            $builder->where('objectives.period', $filters['period']);
        }

        if (!empty($filters['status'])) {
            $builder->where('objectives.status', $filters['status']);
        }

        $builder->orderBy('objectives.period', 'DESC');
        $builder->orderBy('objectives.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get objective by user and period
     */
    public function getByUserAndPeriod($userId, $period)
    {
        return $this->where('user_id', $userId)
                    ->where('period', $period)
                    ->where('type', 'personal')
                    ->first();
    }

    /**
     * Get objective by agency and period
     */
    public function getByAgencyAndPeriod($agencyId, $period)
    {
        return $this->where('agency_id', $agencyId)
                    ->where('period', $period)
                    ->where('type', 'agency')
                    ->first();
    }

    /**
     * Calculate achievement percentages
     */
    public function calculateProgress($objective)
    {
        $progress = [];

        if ($objective['revenue_target'] > 0) {
            $progress['revenue'] = round(($objective['revenue_achieved'] / $objective['revenue_target']) * 100, 2);
        } else {
            $progress['revenue'] = 0;
        }

        if ($objective['new_contacts_target'] > 0) {
            $progress['contacts'] = round(($objective['new_contacts_achieved'] / $objective['new_contacts_target']) * 100, 2);
        } else {
            $progress['contacts'] = 0;
        }

        if ($objective['properties_rent_target'] > 0) {
            $progress['rent'] = round(($objective['properties_rent_achieved'] / $objective['properties_rent_target']) * 100, 2);
        } else {
            $progress['rent'] = 0;
        }

        if ($objective['properties_sale_target'] > 0) {
            $progress['sale'] = round(($objective['properties_sale_achieved'] / $objective['properties_sale_target']) * 100, 2);
        } else {
            $progress['sale'] = 0;
        }

        if ($objective['transactions_target'] > 0) {
            $progress['transactions'] = round(($objective['transactions_achieved'] / $objective['transactions_target']) * 100, 2);
        } else {
            $progress['transactions'] = 0;
        }

        // Calculate overall progress
        $totalTargets = array_filter([
            $objective['revenue_target'],
            $objective['new_contacts_target'],
            $objective['properties_rent_target'],
            $objective['properties_sale_target'],
            $objective['transactions_target']
        ]);

        if (count($totalTargets) > 0) {
            $progress['overall'] = round(array_sum($progress) / count($totalTargets), 2);
        } else {
            $progress['overall'] = 0;
        }

        return $progress;
    }

    /**
     * Update achieved values from actual data
     */
    public function updateAchievedValues($objectiveId)
    {
        $objective = $this->find($objectiveId);
        if (!$objective) {
            return false;
        }

        $period = $objective['period'];
        list($year, $month) = explode('-', $period);
        
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $db = \Config\Database::connect();

        $achieved = [
            'revenue_achieved' => 0,
            'new_contacts_achieved' => 0,
            'properties_rent_achieved' => 0,
            'properties_sale_achieved' => 0,
            'transactions_achieved' => 0,
        ];

        if ($objective['type'] === 'personal' && $objective['user_id']) {
            // Personal objectives
            $userId = $objective['user_id'];

            // Revenue from commissions
            $revenue = $db->table('commissions')
                ->selectSum('amount')
                ->where('user_id', $userId)
                ->where('status', 'paid')
                ->where('DATE(paid_at) >=', $startDate)
                ->where('DATE(paid_at) <=', $endDate)
                ->get()->getRow();
            $achieved['revenue_achieved'] = $revenue->amount ?? 0;

            // New contacts
            $contacts = $db->table('clients')
                ->where('assigned_to', $userId)
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();
            $achieved['new_contacts_achieved'] = $contacts;

            // Properties for rent
            $propertiesRent = $db->table('properties')
                ->where('agent_id', $userId)
                ->where('transaction_type', 'rent')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();
            $achieved['properties_rent_achieved'] = $propertiesRent;

            // Properties for sale
            $propertiesSale = $db->table('properties')
                ->where('agent_id', $userId)
                ->where('transaction_type', 'sale')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();
            $achieved['properties_sale_achieved'] = $propertiesSale;

            // Transactions
            $transactions = $db->table('transactions')
                ->where('agent_id', $userId)
                ->where('status', 'completed')
                ->where('DATE(completion_date) >=', $startDate)
                ->where('DATE(completion_date) <=', $endDate)
                ->countAllResults();
            $achieved['transactions_achieved'] = $transactions;

        } elseif ($objective['type'] === 'agency' && $objective['agency_id']) {
            // Agency objectives
            $agencyId = $objective['agency_id'];

            // Revenue from transactions
            $revenue = $db->table('transactions')
                ->selectSum('amount')
                ->where('agency_id', $agencyId)
                ->where('status', 'completed')
                ->where('DATE(completion_date) >=', $startDate)
                ->where('DATE(completion_date) <=', $endDate)
                ->get()->getRow();
            $achieved['revenue_achieved'] = $revenue->amount ?? 0;

            // New contacts
            $contacts = $db->table('clients')
                ->where('agency_id', $agencyId)
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();
            $achieved['new_contacts_achieved'] = $contacts;

            // Properties for rent
            $propertiesRent = $db->table('properties')
                ->where('agency_id', $agencyId)
                ->where('transaction_type', 'rent')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();
            $achieved['properties_rent_achieved'] = $propertiesRent;

            // Properties for sale
            $propertiesSale = $db->table('properties')
                ->where('agency_id', $agencyId)
                ->where('transaction_type', 'sale')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();
            $achieved['properties_sale_achieved'] = $propertiesSale;

            // Transactions
            $transactions = $db->table('transactions')
                ->where('agency_id', $agencyId)
                ->where('status', 'completed')
                ->where('DATE(completion_date) >=', $startDate)
                ->where('DATE(completion_date) <=', $endDate)
                ->countAllResults();
            $achieved['transactions_achieved'] = $transactions;
        }

        return $this->update($objectiveId, $achieved);
    }

    /**
     * Synchroniser TOUS les KPIs selon les données réelles
     * Inclut: CA payé + biens location/vente + contacts + transactions
     */
    public function syncAllObjectiveMetrics(?int $userId = null, ?int $agencyId = null, ?string $period = null)
    {
        if (!$period) {
            $period = date('Y-m');
        }

        list($year, $month) = explode('-', $period);
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $db = \Config\Database::connect();

        // Trouver les objectifs correspondants
        $query = $this->where('period', $period);
        
        if ($userId) {
            $query->where('user_id', $userId)->where('type', 'personal');
        } elseif ($agencyId) {
            $query->where('agency_id', $agencyId)->where('type', 'agency');
        }

        $objectives = $query->findAll();

        foreach ($objectives as $objective) {
            $update = [];

            if ($objective['type'] === 'personal' && $objective['user_id']) {
                $agentId = $objective['user_id'];

                // 1. CA réalisé (commissions HT payées)
                $caResult = $db->table('transaction_commissions')
                    ->selectSum('total_commission_ht', 'ca')
                    ->where('agent_id', $agentId)
                    ->where('payment_status', 'paid')
                    ->where('payment_date >=', $startDate)
                    ->where('payment_date <=', $endDate)
                    ->get()->getRowArray();
                
                $update['revenue_achieved'] = (float) ($caResult['ca'] ?? 0);

                // 2. Nouveaux contacts
                $contactsCount = $db->table('clients')
                    ->where('assigned_to', $agentId)
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['new_contacts_achieved'] = $contactsCount;

                // 3. Biens à louer publiés
                $rentCount = $db->table('properties')
                    ->where('agent_id', $agentId)
                    ->where('type', 'apartment') // ou 'house', 'land', etc selon votre logique
                    ->where('status', 'published')
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['properties_rent_achieved'] = $rentCount;

                // 4. Biens à vendre publiés
                $saleCount = $db->table('properties')
                    ->where('agent_id', $agentId)
                    ->where('type', 'apartment')
                    ->where('status', 'published')
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['properties_sale_achieved'] = $saleCount;

                // 5. Transactions complétées
                $transCount = $db->table('transactions')
                    ->where('agent_id', $agentId)
                    ->where('status', 'completed')
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['transactions_achieved'] = $transCount;

            } elseif ($objective['type'] === 'agency' && $objective['agency_id']) {
                $agencyId = $objective['agency_id'];

                // 1. CA réalisé (tous les agents de l'agence)
                $caResult = $db->table('transaction_commissions')
                    ->select('transaction_commissions.total_commission_ht')
                    ->selectSum('transaction_commissions.total_commission_ht', 'ca')
                    ->join('users', 'users.id = transaction_commissions.agent_id')
                    ->where('users.agency_id', $agencyId)
                    ->where('transaction_commissions.payment_status', 'paid')
                    ->where('transaction_commissions.payment_date >=', $startDate)
                    ->where('transaction_commissions.payment_date <=', $endDate)
                    ->get()->getRowArray();
                
                $update['revenue_achieved'] = (float) ($caResult['ca'] ?? 0);

                // 2. Nouveaux contacts (tous les agents)
                $contactsCount = $db->table('clients')
                    ->where('agency_id', $agencyId)
                    ->where('DATE(created_at) >=', $startDate)
                    ->where('DATE(created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['new_contacts_achieved'] = $contactsCount;

                // 3. Biens à louer publiés (tous les agents)
                $rentCount = $db->table('properties')
                    ->join('users', 'users.id = properties.agent_id')
                    ->where('users.agency_id', $agencyId)
                    ->where('properties.type', 'apartment')
                    ->where('properties.status', 'published')
                    ->where('DATE(properties.created_at) >=', $startDate)
                    ->where('DATE(properties.created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['properties_rent_achieved'] = $rentCount;

                // 4. Biens à vendre publiés (tous les agents)
                $saleCount = $db->table('properties')
                    ->join('users', 'users.id = properties.agent_id')
                    ->where('users.agency_id', $agencyId)
                    ->where('properties.type', 'apartment')
                    ->where('properties.status', 'published')
                    ->where('DATE(properties.created_at) >=', $startDate)
                    ->where('DATE(properties.created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['properties_sale_achieved'] = $saleCount;

                // 5. Transactions complétées (tous les agents)
                $transCount = $db->table('transactions')
                    ->join('users', 'users.id = transactions.agent_id')
                    ->where('users.agency_id', $agencyId)
                    ->where('transactions.status', 'completed')
                    ->where('DATE(transactions.created_at) >=', $startDate)
                    ->where('DATE(transactions.created_at) <=', $endDate)
                    ->countAllResults();
                
                $update['transactions_achieved'] = $transCount;
            }

            // Mettre à jour l'objectif avec tous les KPIs
            if (!empty($update)) {
                $this->update($objective['id'], $update);
            }
        }

        return true;
    }

    /**
     * Synchroniser le CA réalisé basé sur les commissions HT payées
     * Réel: somme des commission_ht où payment_status = 'paid'
     */
    public function syncCAFromPaidCommissions(?int $userId = null, ?int $agencyId = null, ?string $period = null)
    {
        // Appeller la nouvelle méthode complète
        return $this->syncAllObjectiveMetrics($userId, $agencyId, $period);
    }
}
