<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class CAController extends ResourceController
{
    protected $commissionModel;
    protected $objectiveModel;

    public function __construct()
    {
        $this->commissionModel = model('TransactionCommissionModel');
        $this->objectiveModel = model('ObjectiveModel');
    }

    /**
     * GET /api/ca/summary
     * Retourne le CA réel et les stats actuelles
     */
    public function summary()
    {
        try {
            $period = $this->request->getGet('period') ?? date('Y-m');
            $agencyId = $this->request->getGet('agency_id');

            list($year, $month) = explode('-', $period);
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            // CA réel payé
            $caRealized = $this->commissionModel->calculateActualCA(
                $agencyId,
                $startDate,
                $endDate
            );

            // Objectif pour cette période
            $objective = null;
            if ($agencyId) {
                $objective = $this->objectiveModel->getByAgencyAndPeriod($agencyId, $period);
            }

            // Progress
            $progress = 0;
            $objectiveAmount = 0;
            if ($objective && $objective['revenue_target'] > 0) {
                $objectiveAmount = $objective['revenue_target'];
                $progress = round(($caRealized / $objective['revenue_target']) * 100, 2);
            }

            return $this->respond([
                'status' => 'success',
                'data' => [
                    'period' => $period,
                    'ca_realized' => round($caRealized, 2),
                    'objective_amount' => $objectiveAmount,
                    'progress_percent' => $progress,
                    'timeline' => $this->getTimeline($agencyId, $period)
                ]
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * GET /api/ca/by-period
     * CA par période (12 derniers mois)
     */
    public function byPeriod()
    {
        try {
            $agencyId = $this->request->getGet('agency_id');
            $periods = $this->commissionModel->getCARByPeriod($agencyId);

            return $this->respond([
                'status' => 'success',
                'data' => $periods
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * GET /api/ca/by-agent
     * CA par agent pour le mois courant
     */
    public function byAgent()
    {
        try {
            $month = $this->request->getGet('month') ?? date('Y-m');
            $agents = $this->commissionModel->getCAByAgent($month);

            return $this->respond([
                'status' => 'success',
                'data' => $agents
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }

    /**
     * Timeline du CA pour le mois (jours avec CA payé)
     */
    private function getTimeline(?int $agencyId = null, string $period = null): array
    {
        if (!$period) $period = date('Y-m');

        list($year, $month) = explode('-', $period);
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));

        $builder = $this->commissionModel
            ->select("DATE(payment_date) as day")
            ->selectSum('total_commission_ht', 'ca_day')
            ->where('payment_status', 'paid')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate);

        if ($agencyId) {
            $builder->join('users', 'users.id = transaction_commissions.agent_id')
                ->where('users.agency_id', $agencyId);
        }

        return $builder->groupBy('day')
            ->orderBy('day', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * POST /api/ca/sync-objectives
     * Synchronise les objectifs avec le CA réalisé
     */
    public function syncObjectives()
    {
        // Vérifier les droits d'admin
        if (!in_array(session()->get('role'), ['admin', 'director', 'manager'])) {
            return $this->failForbidden('Accès refusé');
        }

        try {
            $period = $this->request->getPost('period') ?? date('Y-m');
            $userId = $this->request->getPost('user_id');
            $agencyId = $this->request->getPost('agency_id');

            $result = $this->objectiveModel->syncCAFromPaidCommissions(
                $userId ? (int) $userId : null,
                $agencyId ? (int) $agencyId : null,
                $period
            );

            return $this->respond([
                'status' => 'success',
                'message' => 'Objectifs synchronisés',
                'period' => $period
            ]);
        } catch (\Exception $e) {
            return $this->failServerError($e->getMessage());
        }
    }
}
