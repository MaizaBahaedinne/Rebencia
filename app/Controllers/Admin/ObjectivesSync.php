<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ObjectivesSync extends BaseController
{
    protected $objectiveModel;
    protected $transactionCommissionModel;

    public function __construct()
    {
        $this->objectiveModel = model('ObjectiveModel');
        $this->transactionCommissionModel = model('TransactionCommissionModel');
    }

    /**
     * Page de sync des objectifs (1-click)
     */
    public function index()
    {
        if (!canRead('objectives')) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $period = $this->request->getGet('period') ?? date('Y-m');
        
        // Récupérer les objectifs de cette période
        $objectives = $this->objectiveModel->getObjectivesWithDetails([
            'period' => $period,
            'status' => 'active'
        ]);

        // Calculer les stats actuelles pour chaque objectif
        foreach ($objectives as &$obj) {
            $obj['stats'] = $this->calculateCurrentStats($obj, $period);
            $obj['progress'] = $this->objectiveModel->calculateProgress($obj);
        }

        $data = [
            'title' => 'Synchronisation Objectifs',
            'objectives' => $objectives,
            'period' => $period,
            'periods' => $this->getAvailablePeriods()
        ];

        return view('admin/objectives/sync', $data);
    }

    /**
     * Resync tous les objectifs
     */
    public function syncAll()
    {
        if (!canUpdate('objectives')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Accès refusé'
            ])->setStatusCode(403);
        }

        try {
            $period = $this->request->getPost('period') ?? date('Y-m');
            
            $this->objectiveModel->syncAllObjectiveMetrics(null, null, $period);
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Tous les objectifs synchronisés pour ' . $period
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Resync un objectif spécifique
     */
    public function syncOne($id)
    {
        if (!canUpdate('objectives')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Accès refusé'
            ])->setStatusCode(403);
        }

        try {
            $objective = $this->objectiveModel->find($id);
            if (!$objective) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Objectif non trouvé'
                ])->setStatusCode(404);
            }

            $this->objectiveModel->syncAllObjectiveMetrics(
                $objective['type'] === 'personal' ? $objective['user_id'] : null,
                $objective['type'] === 'agency' ? $objective['agency_id'] : null,
                $objective['period']
            );

            // Récalculer le progress
            $objective = $this->objectiveModel->find($id);
            $progress = $this->objectiveModel->calculateProgress($objective);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Objectif synchronisé',
                'progress' => $progress,
                'objective' => $objective
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Calculer les stats actuelles pour un objectif
     */
    private function calculateCurrentStats($objective, $period)
    {
        list($year, $month) = explode('-', $period);
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $db = \Config\Database::connect();
        $stats = [];

        if ($objective['type'] === 'personal' && $objective['user_id']) {
            $agentId = $objective['user_id'];

            // CA payé
            $result = $db->table('transaction_commissions')
                ->selectSum('total_commission_ht', 'ca')
                ->where('agent_id', $agentId)
                ->where('payment_status', 'paid')
                ->where('payment_date >=', $startDate)
                ->where('payment_date <=', $endDate)
                ->get()->getRowArray();
            $stats['ca_current'] = (float) ($result['ca'] ?? 0);

            // Biens publiés
            $stats['rent_current'] = $db->table('properties')
                ->where('agent_id', $agentId)
                ->where('status', 'published')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();

            $stats['sale_current'] = $db->table('properties')
                ->where('agent_id', $agentId)
                ->where('status', 'published')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();

            // Contacts
            $stats['contacts_current'] = $db->table('clients')
                ->where('assigned_to', $agentId)
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();

            // Transactions
            $stats['transactions_current'] = $db->table('transactions')
                ->where('agent_id', $agentId)
                ->where('status', 'completed')
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();

        } elseif ($objective['type'] === 'agency' && $objective['agency_id']) {
            $agencyId = $objective['agency_id'];

            // CA payé (tous les agents)
            $result = $db->table('transaction_commissions')
                ->selectSum('total_commission_ht', 'ca')
                ->join('users', 'users.id = transaction_commissions.agent_id')
                ->where('users.agency_id', $agencyId)
                ->where('transaction_commissions.payment_status', 'paid')
                ->where('transaction_commissions.payment_date >=', $startDate)
                ->where('transaction_commissions.payment_date <=', $endDate)
                ->get()->getRowArray();
            $stats['ca_current'] = (float) ($result['ca'] ?? 0);

            // Biens publiés
            $stats['rent_current'] = $db->table('properties')
                ->join('users', 'users.id = properties.agent_id')
                ->where('users.agency_id', $agencyId)
                ->where('properties.status', 'published')
                ->where('DATE(properties.created_at) >=', $startDate)
                ->where('DATE(properties.created_at) <=', $endDate)
                ->countAllResults();

            $stats['sale_current'] = $db->table('properties')
                ->join('users', 'users.id = properties.agent_id')
                ->where('users.agency_id', $agencyId)
                ->where('properties.status', 'published')
                ->where('DATE(properties.created_at) >=', $startDate)
                ->where('DATE(properties.created_at) <=', $endDate)
                ->countAllResults();

            // Contacts
            $stats['contacts_current'] = $db->table('clients')
                ->where('agency_id', $agencyId)
                ->where('DATE(created_at) >=', $startDate)
                ->where('DATE(created_at) <=', $endDate)
                ->countAllResults();

            // Transactions
            $stats['transactions_current'] = $db->table('transactions')
                ->join('users', 'users.id = transactions.agent_id')
                ->where('users.agency_id', $agencyId)
                ->where('transactions.status', 'completed')
                ->where('DATE(transactions.created_at) >=', $startDate)
                ->where('DATE(transactions.created_at) <=', $endDate)
                ->countAllResults();
        }

        return $stats;
    }

    /**
     * Récupérer les périodes disponibles (6 mois)
     */
    private function getAvailablePeriods()
    {
        $periods = [];
        for ($i = 0; $i < 6; $i++) {
            $date = date('Y-m', strtotime("-$i months"));
            $periods[$date] = date('F Y', strtotime($date));
        }
        return $periods;
    }
}
