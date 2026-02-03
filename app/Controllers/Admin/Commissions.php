<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Commissions extends BaseController
{
    protected $commissionModel;
    protected $transactionModel;
    protected $userModel;

    public function __construct()
    {
        $this->commissionModel = model('CommissionModel');
        $this->transactionModel = model('TransactionModel');
        $this->userModel = model('UserModel');
    }

    /**
     * Commissions dashboard
     */
    public function index()
    {
        $month = $this->request->getGet('month') ?: date('Y-m');
        $agentId = $this->request->getGet('agent_id');
        $status = $this->request->getGet('status');

        // Build query
        $db = \Config\Database::connect();
        $builder = $db->table('commissions c');
        $builder->select('c.*, t.reference as transaction_ref, t.amount as transaction_amount, 
                         CONCAT(u.first_name, " ", u.last_name) as agent_name, 
                         p.title as property_title, p.reference as property_ref');
        $builder->join('transactions t', 't.id = c.transaction_id', 'left');
        $builder->join('users u', 'u.id = c.user_id', 'left');
        $builder->join('properties p', 'p.id = t.property_id', 'left');
        $builder->where('DATE_FORMAT(c.created_at, "%Y-%m")', $month);

        if ($agentId) {
            $builder->where('c.user_id', $agentId);
        }
        if ($status) {
            $builder->where('c.status', $status);
        }

        $commissions = $builder->orderBy('c.created_at', 'DESC')->get()->getResultArray();

        // Statistics
        $stats = $this->getCommissionStats($month, $agentId);

        // Get agents for filter
        $agents = $this->userModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Gestion des Commissions',
            'page_title' => 'Commissions',
            'commissions' => $commissions,
            'stats' => $stats,
            'agents' => $agents,
            'selectedMonth' => $month,
            'selectedAgent' => $agentId,
            'selectedStatus' => $status
        ];

        return view('admin/commissions/index', $data);
    }

    /**
     * Get commission statistics
     */
    private function getCommissionStats($month, $agentId = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('commissions');
        $builder->where('DATE_FORMAT(created_at, "%Y-%m")', $month);
        
        if ($agentId) {
            $builder->where('user_id', $agentId);
        }

        // Total amount
        $builder->selectSum('amount', 'total');
        $total = $builder->get()->getRowArray()['total'] ?? 0;

        // By status
        $builder = $db->table('commissions');
        $builder->select('status, SUM(amount) as total, COUNT(*) as count');
        $builder->where('DATE_FORMAT(created_at, "%Y-%m")', $month);
        if ($agentId) {
            $builder->where('user_id', $agentId);
        }
        $builder->groupBy('status');
        $byStatus = $builder->get()->getResultArray();

        $statusStats = [
            'pending' => ['amount' => 0, 'count' => 0],
            'approved' => ['amount' => 0, 'count' => 0],
            'paid' => ['amount' => 0, 'count' => 0]
        ];

        foreach ($byStatus as $row) {
            $statusStats[$row['status']] = [
                'amount' => $row['total'],
                'count' => $row['count']
            ];
        }

        // Top agents (if no specific agent selected)
        $topAgents = [];
        if (!$agentId) {
            $builder = $db->table('commissions c');
            $builder->select('c.user_id, CONCAT(u.first_name, " ", u.last_name) as agent_name, 
                            SUM(c.amount) as total_commission, COUNT(*) as transaction_count');
            $builder->join('users u', 'u.id = c.user_id', 'left');
            $builder->where('DATE_FORMAT(c.created_at, "%Y-%m")', $month);
            $builder->groupBy('c.user_id');
            $builder->orderBy('total_commission', 'DESC');
            $builder->limit(5);
            $topAgents = $builder->get()->getResultArray();
        }

        return [
            'total' => $total,
            'by_status' => $statusStats,
            'top_agents' => $topAgents
        ];
    }

    /**
     * Approve commission
     */
    public function approve($id)
    {
        $commission = $this->commissionModel->find($id);
        
        if (!$commission) {
            return redirect()->back()->with('error', 'Commission introuvable');
        }

        $this->commissionModel->update($id, [
            'status' => 'approved',
            'approved_at' => date('Y-m-d H:i:s'),
            'approved_by' => session()->get('user_id')
        ]);

        // Create notification for agent
        $notificationModel = model('NotificationModel');
        $notificationModel->createNotification(
            $commission['user_id'],
            'success',
            'Commission approuvée',
            'Votre commission de ' . number_format($commission['amount'], 0, ',', ' ') . ' TND a été approuvée',
            '/admin/commissions',
            'fa-check-circle'
        );

        return redirect()->back()->with('success', 'Commission approuvée avec succès');
    }

    /**
     * Mark as paid
     */
    public function markAsPaid($id)
    {
        $commission = $this->commissionModel->find($id);
        
        if (!$commission) {
            return redirect()->back()->with('error', 'Commission introuvable');
        }

        if ($commission['status'] !== 'approved') {
            return redirect()->back()->with('error', 'La commission doit être approuvée avant paiement');
        }

        $this->commissionModel->update($id, [
            'status' => 'paid',
            'paid_at' => date('Y-m-d H:i:s'),
            'paid_by' => session()->get('user_id')
        ]);

        // Create notification for agent
        $notificationModel = model('NotificationModel');
        $notificationModel->createNotification(
            $commission['user_id'],
            'success',
            'Commission payée',
            'Votre commission de ' . number_format($commission['amount'], 0, ',', ' ') . ' TND a été payée',
            '/admin/commissions',
            'fa-money-bill-wave'
        );

        return redirect()->back()->with('success', 'Commission marquée comme payée');
    }

    /**
     * Bulk approve
     */
    public function bulkApprove()
    {
        $ids = $this->request->getPost('commission_ids');
        
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Aucune commission sélectionnée');
        }

        $userId = session()->get('user_id');
        $count = 0;

        foreach ($ids as $id) {
            $commission = $this->commissionModel->find($id);
            if ($commission && $commission['status'] === 'pending') {
                $this->commissionModel->update($id, [
                    'status' => 'approved',
                    'approved_at' => date('Y-m-d H:i:s'),
                    'approved_by' => $userId
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "$count commission(s) approuvée(s)");
    }

    /**
     * Bulk pay
     */
    public function bulkPay()
    {
        $ids = $this->request->getPost('commission_ids');
        
        if (!$ids || !is_array($ids)) {
            return redirect()->back()->with('error', 'Aucune commission sélectionnée');
        }

        $userId = session()->get('user_id');
        $count = 0;

        foreach ($ids as $id) {
            $commission = $this->commissionModel->find($id);
            if ($commission && $commission['status'] === 'approved') {
                $this->commissionModel->update($id, [
                    'status' => 'paid',
                    'paid_at' => date('Y-m-d H:i:s'),
                    'paid_by' => $userId
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "$count commission(s) payée(s)");
    }

    /**
     * Agent commission report
     */
    public function agentReport($agentId)
    {
        $year = $this->request->getGet('year') ?: date('Y');

        // Get agent info
        $agent = $this->userModel->find($agentId);
        if (!$agent) {
            return redirect()->to('/admin/commissions')->with('error', 'Agent introuvable');
        }

        // Get monthly commissions for the year
        $db = \Config\Database::connect();
        $builder = $db->table('commissions');
        $builder->select('DATE_FORMAT(created_at, "%Y-%m") as month, 
                         SUM(amount) as total, 
                         COUNT(*) as count,
                         SUM(CASE WHEN status="paid" THEN amount ELSE 0 END) as paid_amount');
        $builder->where('user_id', $agentId);
        $builder->where('YEAR(created_at)', $year);
        $builder->groupBy('month');
        $builder->orderBy('month', 'ASC');
        $monthlyData = $builder->get()->getResultArray();

        // Total stats for the year
        $builder = $db->table('commissions');
        $builder->select('SUM(amount) as total, COUNT(*) as count');
        $builder->where('user_id', $agentId);
        $builder->where('YEAR(created_at)', $year);
        $yearStats = $builder->get()->getRowArray();

        $data = [
            'title' => 'Rapport Commissions - ' . ($agent['first_name'] ?? '') . ' ' . ($agent['last_name'] ?? ''),
            'page_title' => 'Rapport Agent',
            'agent' => $agent,
            'monthlyData' => $monthlyData,
            'yearStats' => $yearStats,
            'selectedYear' => $year
        ];

        return view('admin/commissions/agent_report', $data);
    }
}
