<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Check authentication
        if (!session()->has('user_id')) {
            return redirect()->to('/admin/login');
        }

        $data = [
            'title' => 'Dashboard',
            'user' => $this->getCurrentUser(),
            'stats' => $this->getDashboardStats(),
            'revenue_stats' => $this->getRevenueStats(),
            'recent_transactions' => $this->getRecentTransactions(),
            'recent_clients' => $this->getRecentClients(),
            'popular_properties' => $this->getPopularProperties(),
            'monthly_revenue' => $this->getMonthlyRevenue()
        ];

        return view('admin/dashboard', $data);
    }

    private function getCurrentUser()
    {
        $userModel = model('UserModel');
        return $userModel->getUserWithRole(session()->get('user_id'));
    }

    private function getDashboardStats()
    {
        $db = \Config\Database::connect();
        
        return [
            'total_properties' => $db->table('properties')->countAllResults(),
            'total_clients' => $db->table('clients')->countAllResults(),
            'total_transactions' => $db->table('transactions')->countAllResults(),
            'total_users' => $db->table('users')->where('status', 'active')->countAllResults(),
            'properties_published' => $db->table('properties')->where('is_published', 1)->countAllResults(),
            'transactions_pending' => $db->table('transactions')->where('status', 'pending')->countAllResults(),
            'leads_count' => $db->table('clients')->where('status', 'lead')->countAllResults(),
        ];
    }

    private function getRevenueStats()
    {
        $db = \Config\Database::connect();
        
        $totalRevenue = $db->table('transactions')
            ->selectSum('amount')
            ->where('status', 'completed')
            ->get()
            ->getRow()
            ->amount ?? 0;

        $totalCommission = $db->table('transactions')
            ->selectSum('commission_amount')
            ->where('status', 'completed')
            ->get()
            ->getRow()
            ->commission_amount ?? 0;

        $monthlyRevenue = $db->table('transactions')
            ->selectSum('amount')
            ->where('status', 'completed')
            ->where('MONTH(transaction_date)', date('m'))
            ->where('YEAR(transaction_date)', date('Y'))
            ->get()
            ->getRow()
            ->amount ?? 0;

        return [
            'total_revenue' => $totalRevenue,
            'total_commission' => $totalCommission,
            'monthly_revenue' => $monthlyRevenue
        ];
    }

    private function getRecentTransactions()
    {
        $db = \Config\Database::connect();
        
        return $db->table('transactions t')
            ->select('t.*, p.title as property_title, p.reference as property_ref, 
                      c.first_name as buyer_name, c.last_name as buyer_lastname,
                      u.first_name as agent_name, u.last_name as agent_lastname')
            ->join('properties p', 'p.id = t.property_id', 'left')
            ->join('clients c', 'c.id = t.buyer_id', 'left')
            ->join('users u', 'u.id = t.agent_id', 'left')
            ->orderBy('t.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    private function getRecentClients()
    {
        $db = \Config\Database::connect();
        
        return $db->table('clients c')
            ->select('c.*, u.first_name as agent_name, u.last_name as agent_lastname')
            ->join('users u', 'u.id = c.assigned_to', 'left')
            ->orderBy('c.created_at', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    private function getPopularProperties()
    {
        $db = \Config\Database::connect();
        
        return $db->table('properties p')
            ->select('p.*, z.city, z.governorate, COUNT(t.id) as transaction_count')
            ->join('zones z', 'z.id = p.zone_id', 'left')
            ->join('transactions t', 't.property_id = p.id', 'left')
            ->where('p.is_published', 1)
            ->groupBy('p.id')
            ->orderBy('transaction_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();
    }

    private function getMonthlyRevenue()
    {
        $db = \Config\Database::connect();
        
        // Get last 6 months revenue
        $months = [];
        $revenues = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = date('Y-m', strtotime("-$i months"));
            $month = date('M', strtotime("-$i months"));
            
            $revenue = $db->table('transactions')
                ->selectSum('amount')
                ->where('status', 'completed')
                ->where('DATE_FORMAT(transaction_date, "%Y-%m")', $date)
                ->get()
                ->getRow()
                ->amount ?? 0;
            
            $months[] = $month;
            $revenues[] = $revenue;
        }
        
        return [
            'labels' => $months,
            'data' => $revenues
        ];
    }
}
