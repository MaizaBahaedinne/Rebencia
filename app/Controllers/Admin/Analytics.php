<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Analytics extends BaseController
{
    protected $propertyModel;
    protected $clientModel;
    protected $transactionModel;
    protected $commissionModel;
    protected $workflowInstanceModel;
    protected $db;

    public function __construct()
    {
        $this->propertyModel = model('PropertyModel');
        $this->clientModel = model('ClientModel');
        $this->transactionModel = model('TransactionModel');
        $this->commissionModel = model('CommissionModel');
        $this->workflowInstanceModel = model('WorkflowInstanceModel');
        $this->db = \Config\Database::connect();
    }

    /**
     * Analytics dashboard
     */
    public function index()
    {
        $data = [
            'title' => 'Analytics & Performance',
            'page_title' => 'Analytics',
            'conversionRate' => $this->getConversionRate(),
            'avgSaleTime' => $this->getAverageSaleTime(),
            'pipelineValue' => $this->getPipelineValue(),
            'monthlyRevenue' => $this->getMonthlyRevenue(),
            'topAgents' => $this->getTopAgents(),
            'propertyPerformance' => $this->getPropertyPerformance(),
            'clientSourceAnalysis' => $this->getClientSourceAnalysis(),
            'transactionTrends' => $this->getTransactionTrends()
        ];

        return view('admin/analytics/index', $data);
    }

    /**
     * Get conversion rate (leads to closed deals)
     */
    private function getConversionRate()
    {
        // Total clients
        $totalClients = $this->clientModel->countAll();
        
        // Clients with transactions
        $clientsWithTransactions = $this->db->query(
            "SELECT COUNT(DISTINCT client_id) as count 
             FROM transactions 
             WHERE status IN ('signed', 'completed')"
        )->getRow()->count;

        $rate = $totalClients > 0 ? ($clientsWithTransactions / $totalClients) * 100 : 0;

        return [
            'rate' => round($rate, 2),
            'total' => $totalClients,
            'converted' => $clientsWithTransactions
        ];
    }

    /**
     * Get average time from lead to sale
     */
    private function getAverageSaleTime()
    {
        $result = $this->db->query(
            "SELECT AVG(DATEDIFF(t.signature_date, c.created_at)) as avg_days
             FROM transactions t
             JOIN clients c ON t.client_id = c.id
             WHERE t.status IN ('signed', 'completed')
             AND t.signature_date IS NOT NULL
             AND t.signature_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)"
        )->getRow();

        return round($result->avg_days ?? 0);
    }

    /**
     * Get total pipeline value (pending + in_progress transactions)
     */
    private function getPipelineValue()
    {
        $result = $this->db->query(
            "SELECT 
                COUNT(*) as count,
                SUM(amount) as total
             FROM transactions
             WHERE status IN ('pending', 'in_progress', 'documents', 'validation')"
        )->getRow();

        return [
            'count' => $result->count ?? 0,
            'total' => $result->total ?? 0
        ];
    }

    /**
     * Get monthly revenue for last 12 months
     */
    private function getMonthlyRevenue()
    {
        $results = $this->db->query(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                SUM(amount) as revenue,
                COUNT(*) as deals
             FROM transactions
             WHERE status IN ('signed', 'completed')
             AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month ASC"
        )->getResultArray();

        $months = [];
        $revenue = [];
        $deals = [];

        foreach ($results as $row) {
            $months[] = date('M Y', strtotime($row['month'] . '-01'));
            $revenue[] = $row['revenue'];
            $deals[] = $row['deals'];
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'deals' => $deals
        ];
    }

    /**
     * Get top performing agents
     */
    private function getTopAgents()
    {
        return $this->db->query(
            "SELECT 
                u.id,
                CONCAT(u.first_name, ' ', u.last_name) as name,
                COUNT(DISTINCT t.id) as total_deals,
                SUM(t.amount) as total_revenue,
                SUM(c.amount) as total_commission,
                AVG(DATEDIFF(t.completion_date, t.created_at)) as avg_deal_time
             FROM users u
             LEFT JOIN transactions t ON u.id = t.user_id AND t.status IN ('signed', 'completed')
             LEFT JOIN commissions c ON c.transaction_id = t.id AND c.user_id = u.id
             WHERE u.role IN ('agent', 'manager')
             AND t.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY u.id
             HAVING total_deals > 0
             ORDER BY total_revenue DESC
             LIMIT 10"
        )->getResultArray();
    }

    /**
     * Get property performance by type
     */
    private function getPropertyPerformance()
    {
        return $this->db->query(
            "SELECT 
                p.type,
                COUNT(p.id) as total_properties,
                COUNT(t.id) as sold_count,
                AVG(DATEDIFF(t.completion_date, p.created_at)) as avg_days_to_sell,
                AVG(t.amount) as avg_sale_price
             FROM properties p
             LEFT JOIN transactions t ON p.id = t.property_id AND t.status IN ('signed', 'completed')
             WHERE p.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY p.type"
        )->getResultArray();
    }

    /**
     * Get client source analysis
     */
    private function getClientSourceAnalysis()
    {
        // Simulate source tracking (can be enhanced with actual source field)
        return $this->db->query(
            "SELECT 
                CASE 
                    WHEN preferences LIKE '%online%' THEN 'Online'
                    WHEN type = 'particulier' THEN 'Direct'
                    WHEN type = 'professionnel' THEN 'B2B'
                    ELSE 'Referral'
                END as source,
                COUNT(*) as count,
                COUNT(t.id) as converted
             FROM clients c
             LEFT JOIN transactions t ON c.id = t.client_id AND t.status IN ('signed', 'completed')
             GROUP BY source"
        )->getResultArray();
    }

    /**
     * Get transaction trends by month
     */
    private function getTransactionTrends()
    {
        return $this->db->query(
            "SELECT 
                DATE_FORMAT(created_at, '%Y-%m') as month,
                status,
                COUNT(*) as count
             FROM transactions
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
             GROUP BY month, status
             ORDER BY month DESC"
        )->getResultArray();
    }

    /**
     * Agent performance report
     */
    public function agentReport($agentId)
    {
        $userModel = model('UserModel');
        $agent = $userModel->find($agentId);

        if (!$agent) {
            return redirect()->back()->with('error', 'Agent non trouvé');
        }

        // Get agent statistics
        $stats = $this->getAgentStatistics($agentId);
        
        $data = [
            'title' => 'Rapport Agent - ' . $agent['first_name'] . ' ' . $agent['last_name'],
            'page_title' => 'Performance Agent',
            'agent' => $agent,
            'stats' => $stats,
            'monthlyPerformance' => $this->getAgentMonthlyPerformance($agentId),
            'recentDeals' => $this->getAgentRecentDeals($agentId)
        ];

        return view('admin/analytics/agent_report', $data);
    }

    /**
     * Get agent statistics
     */
    private function getAgentStatistics($agentId)
    {
        $result = $this->db->query(
            "SELECT 
                COUNT(DISTINCT p.id) as total_properties,
                COUNT(DISTINCT c.id) as total_clients,
                COUNT(DISTINCT t.id) as total_deals,
                SUM(CASE WHEN t.status IN ('signed', 'completed') THEN 1 ELSE 0 END) as closed_deals,
                SUM(CASE WHEN t.status IN ('signed', 'completed') THEN t.amount ELSE 0 END) as total_revenue,
                SUM(CASE WHEN t.status IN ('signed', 'completed') THEN com.amount ELSE 0 END) as total_commission
             FROM users u
             LEFT JOIN properties p ON u.id = p.user_id
             LEFT JOIN clients c ON u.id = c.user_id
             LEFT JOIN transactions t ON u.id = t.user_id
             LEFT JOIN commissions com ON t.id = com.transaction_id AND u.id = com.user_id
             WHERE u.id = ?",
            [$agentId]
        )->getRow();

        $conversionRate = $result->total_clients > 0 
            ? ($result->closed_deals / $result->total_clients) * 100 
            : 0;

        return [
            'total_properties' => $result->total_properties ?? 0,
            'total_clients' => $result->total_clients ?? 0,
            'total_deals' => $result->total_deals ?? 0,
            'closed_deals' => $result->closed_deals ?? 0,
            'total_revenue' => $result->total_revenue ?? 0,
            'total_commission' => $result->total_commission ?? 0,
            'conversion_rate' => round($conversionRate, 2)
        ];
    }

    /**
     * Get agent monthly performance
     */
    private function getAgentMonthlyPerformance($agentId)
    {
        return $this->db->query(
            "SELECT 
                DATE_FORMAT(t.created_at, '%Y-%m') as month,
                COUNT(*) as deals,
                SUM(t.amount) as revenue,
                SUM(c.amount) as commission
             FROM transactions t
             LEFT JOIN commissions c ON t.id = c.transaction_id AND c.user_id = ?
             WHERE t.user_id = ?
             AND t.status IN ('signed', 'completed')
             AND t.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY month
             ORDER BY month DESC
             LIMIT 12",
            [$agentId, $agentId]
        )->getResultArray();
    }

    /**
     * Get agent recent deals
     */
    private function getAgentRecentDeals($agentId)
    {
        return $this->db->query(
            "SELECT 
                t.*,
                p.title as property_title,
                p.reference as property_reference,
                CONCAT(c.first_name, ' ', c.last_name) as client_name,
                com.amount as commission_amount
             FROM transactions t
             LEFT JOIN properties p ON t.property_id = p.id
             LEFT JOIN clients c ON t.client_id = c.id
             LEFT JOIN commissions com ON t.id = com.transaction_id AND com.user_id = ?
             WHERE t.user_id = ?
             ORDER BY t.created_at DESC
             LIMIT 20",
            [$agentId, $agentId]
        )->getResultArray();
    }

    /**
     * Get commission evolution data (AJAX)
     */
    public function getCommissionEvolution()
    {
        $agentId = $this->request->getGet('agent_id');
        
        $query = "SELECT 
                    DATE_FORMAT(t.created_at, '%Y-%m') as month,
                    SUM(c.amount) as commission
                  FROM commissions c
                  JOIN transactions t ON c.transaction_id = t.id
                  WHERE t.status IN ('signed', 'completed')
                  AND t.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)";
        
        if ($agentId) {
            $query .= " AND c.user_id = " . (int)$agentId;
        }
        
        $query .= " GROUP BY month ORDER BY month ASC";
        
        $results = $this->db->query($query)->getResultArray();
        
        return $this->response->setJSON($results);
    }
}
