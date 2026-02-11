<?php

namespace App\Models;

use CodeIgniter\Model;

class DashboardModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get Admin Dashboard Statistics (System-level)
     */
    public function getAdminStats()
    {
        $stats = [];

        // Total users
        $stats['total_users'] = $this->db->table('users')->countAllResults();

        // Active users
        $stats['active_users'] = $this->db->table('users')
            ->where('status', 'active')
            ->countAllResults();

        // New users this month
        $stats['new_users_month'] = $this->db->table('users')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->countAllResults();

        // Total agencies
        $stats['total_agencies'] = $this->db->table('agencies')
            ->where('status', 'active')
            ->countAllResults();

        // Total clients
        $stats['total_clients'] = $this->db->table('clients')->countAllResults();

        // Total properties
        $stats['total_properties'] = $this->db->table('properties')->countAllResults();

        // Total transactions
        $stats['total_transactions'] = $this->db->table('transactions')
            ->where('status', 'completed')
            ->countAllResults();

        // Support tasks pending
        $stats['support_pending'] = $this->db->table('tasks')
            ->where('status', 'pending')
            ->countAllResults();

        // Audit logs today
        $stats['audit_logs_today'] = $this->db->table('audit_logs')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();

        // Error logs today
        $stats['error_logs_today'] = $this->db->table('audit_logs')
            ->where('level', 'error')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();

        // Emails sent this week
        $stats['emails_week'] = $this->db->table('email_logs')
            ->where('sent_at >=', date('Y-m-d', strtotime('-7 days')))
            ->countAllResults();

        // SMS sent this week
        $stats['sms_week'] = $this->db->table('sms_logs')
            ->where('sent_at >=', date('Y-m-d', strtotime('-7 days')))
            ->countAllResults();

        // Property views last 30 days
        $stats['property_views_month'] = $this->db->table('property_views')
            ->where('viewed_at >=', date('Y-m-d', strtotime('-30 days')))
            ->countAllResults();

        // Server info
        $stats['server_load'] = function_exists('sys_getloadavg') ? sys_getloadavg()[0] : 0;
        $stats['memory_usage'] = memory_get_usage(true) / 1024 / 1024; // MB
        $stats['disk_free'] = disk_free_space('/') / 1024 / 1024 / 1024; // GB
        $stats['disk_total'] = disk_total_space('/') / 1024 / 1024 / 1024; // GB

        // Recent activities
        $stats['recent_activities'] = $this->db->table('audit_logs')
            ->select('audit_logs.*, users.first_name, users.last_name')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $stats;
    }

    /**
     * Get Director Dashboard Statistics (All group data)
     */
    public function getDirectorStats()
    {
        $stats = [];

        // Total clients
        $stats['total_clients'] = $this->db->table('clients')->countAllResults();

        // Clients this month
        $stats['clients_month'] = $this->db->table('clients')
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->countAllResults();

        // Total agencies
        $stats['total_agencies'] = $this->db->table('agencies')
            ->where('status', 'active')
            ->countAllResults();

        // Total properties
        $stats['total_properties'] = $this->db->table('properties')->countAllResults();

        // Properties available
        $stats['properties_available'] = $this->db->table('properties')
            ->where('status', 'available')
            ->countAllResults();

        // Properties by type
        $stats['properties_by_type'] = $this->db->table('properties')
            ->select('property_type, COUNT(*) as count')
            ->groupBy('property_type')
            ->get()
            ->getResultArray();

        // Total transactions
        $stats['total_transactions'] = $this->db->table('transactions')
            ->where('status', 'completed')
            ->countAllResults();

        // Total revenue
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('status', 'completed')
            ->get()
            ->getRow();
        $stats['total_revenue'] = $result->amount ?? 0;

        // Revenue this month
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('status', 'completed')
            ->where('MONTH(completion_date)', date('m'))
            ->where('YEAR(completion_date)', date('Y'))
            ->get()
            ->getRow();
        $stats['revenue_month'] = $result->amount ?? 0;

        // Revenue by agency
        $stats['revenue_by_agency'] = $this->db->table('transactions t')
            ->select('a.name, SUM(t.amount) as revenue, COUNT(t.id) as transactions')
            ->join('agencies a', 'a.id = t.agency_id', 'left')
            ->where('t.status', 'completed')
            ->groupBy('a.id')
            ->orderBy('revenue', 'DESC')
            ->get()
            ->getResultArray();

        // Top agents
        $stats['top_agents'] = $this->db->table('transactions t')
            ->select('u.first_name, u.last_name, COUNT(t.id) as deals, SUM(t.amount) as revenue')
            ->join('users u', 'u.id = t.agent_id', 'left')
            ->where('t.status', 'completed')
            ->groupBy('u.id')
            ->orderBy('deals', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Monthly revenue (last 12 months)
        $stats['monthly_revenue'] = $this->db->query("
            SELECT 
                DATE_FORMAT(completion_date, '%Y-%m') as month,
                SUM(amount) as revenue,
                COUNT(*) as transactions
            FROM transactions
            WHERE status = 'completed'
                AND completion_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(completion_date, '%Y-%m')
            ORDER BY month ASC
        ")->getResultArray();

        // Active objectives
        $stats['active_objectives'] = $this->db->table('objectives')
            ->where('status', 'active')
            ->where('period', date('Y-m'))
            ->countAllResults();

        // Recent transactions
        $stats['recent_transactions'] = $this->db->table('transactions t')
            ->select('t.*, p.title as property_title, c.first_name, c.last_name, u.first_name as agent_first_name, u.last_name as agent_last_name')
            ->join('properties p', 'p.id = t.property_id', 'left')
            ->join('clients c', 'c.id = t.client_id', 'left')
            ->join('users u', 'u.id = t.agent_id', 'left')
            ->orderBy('t.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $stats;
    }

    /**
     * Get Agency Manager Dashboard Statistics (Filtered by agency_id)
     */
    public function getManagerStats($agencyId)
    {
        $stats = [];

        // Total clients
        $stats['total_clients'] = $this->db->table('clients')
            ->where('agency_id', $agencyId)
            ->countAllResults();

        // Clients this month
        $stats['clients_month'] = $this->db->table('clients')
            ->where('agency_id', $agencyId)
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->countAllResults();

        // Total properties
        $stats['total_properties'] = $this->db->table('properties')
            ->where('agency_id', $agencyId)
            ->countAllResults();

        // Properties available
        $stats['properties_available'] = $this->db->table('properties')
            ->where('agency_id', $agencyId)
            ->where('status', 'available')
            ->countAllResults();

        // Total transactions
        $stats['total_transactions'] = $this->db->table('transactions')
            ->where('agency_id', $agencyId)
            ->where('status', 'completed')
            ->countAllResults();

        // Total revenue
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('agency_id', $agencyId)
            ->where('status', 'completed')
            ->get()
            ->getRow();
        $stats['total_revenue'] = $result->amount ?? 0;

        // Revenue this month
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('agency_id', $agencyId)
            ->where('status', 'completed')
            ->where('MONTH(completion_date)', date('m'))
            ->where('YEAR(completion_date)', date('Y'))
            ->get()
            ->getRow();
        $stats['revenue_month'] = $result->amount ?? 0;

        // Active agents
        $stats['active_agents'] = $this->db->table('users')
            ->where('agency_id', $agencyId)
            ->where('status', 'active')
            ->countAllResults();

        // Performance by agent
        $stats['agents_performance'] = $this->db->table('users u')
            ->select('u.first_name, u.last_name, COUNT(t.id) as deals, COALESCE(SUM(t.amount), 0) as revenue')
            ->join('transactions t', 't.agent_id = u.id AND t.status = "completed"', 'left')
            ->where('u.agency_id', $agencyId)
            ->where('u.status', 'active')
            ->groupBy('u.id')
            ->orderBy('deals', 'DESC')
            ->get()
            ->getResultArray();

        // Monthly revenue (last 12 months)
        $stats['monthly_revenue'] = $this->db->query("
            SELECT 
                DATE_FORMAT(completion_date, '%Y-%m') as month,
                SUM(amount) as revenue,
                COUNT(*) as transactions
            FROM transactions
            WHERE agency_id = ?
                AND status = 'completed'
                AND completion_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(completion_date, '%Y-%m')
            ORDER BY month ASC
        ", [$agencyId])->getResultArray();

        // Pending requests
        $stats['pending_requests'] = $this->db->table('property_requests pr')
            ->join('properties p', 'p.id = pr.property_id')
            ->where('p.agency_id', $agencyId)
            ->where('pr.status', 'pending')
            ->countAllResults();

        // Agency objective
        $stats['agency_objective'] = $this->db->table('objectives')
            ->where('type', 'agency')
            ->where('agency_id', $agencyId)
            ->where('period', date('Y-m'))
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        // Recent transactions
        $stats['recent_transactions'] = $this->db->table('transactions t')
            ->select('t.*, p.title as property_title, c.first_name, c.last_name, u.first_name as agent_first_name, u.last_name as agent_last_name')
            ->join('properties p', 'p.id = t.property_id', 'left')
            ->join('clients c', 'c.id = t.client_id', 'left')
            ->join('users u', 'u.id = t.agent_id', 'left')
            ->where('t.agency_id', $agencyId)
            ->orderBy('t.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $stats;
    }

    /**
     * Get Agent Dashboard Statistics (Filtered by user_id)
     * Used for both Coordinateur and Collaborateur roles
     */
    public function getAgentStats($userId)
    {
        $stats = [];

        // My property requests
        $stats['my_requests'] = $this->db->table('property_requests')
            ->where('assigned_to', $userId)
            ->countAllResults();

        // Pending requests
        $stats['requests_pending'] = $this->db->table('property_requests')
            ->where('assigned_to', $userId)
            ->where('status', 'pending')
            ->countAllResults();

        // Completed requests
        $stats['requests_completed'] = $this->db->table('property_requests')
            ->where('assigned_to', $userId)
            ->where('status', 'completed')
            ->countAllResults();

        // My estimations
        $stats['my_estimations'] = $this->db->table('property_estimations')
            ->where('agent_id', $userId)
            ->countAllResults();

        // Pending estimations
        $stats['estimations_pending'] = $this->db->table('property_estimations')
            ->where('agent_id', $userId)
            ->where('status', 'pending')
            ->countAllResults();

        // My clients
        $stats['my_clients'] = $this->db->table('clients')
            ->where('assigned_to', $userId)
            ->countAllResults();

        // Clients this month
        $stats['clients_month'] = $this->db->table('clients')
            ->where('assigned_to', $userId)
            ->where('MONTH(created_at)', date('m'))
            ->where('YEAR(created_at)', date('Y'))
            ->countAllResults();

        // My properties
        $stats['my_properties'] = $this->db->table('properties')
            ->where('agent_id', $userId)
            ->countAllResults();

        // Properties available
        $stats['properties_available'] = $this->db->table('properties')
            ->where('agent_id', $userId)
            ->where('status', 'available')
            ->countAllResults();

        // My transactions
        $stats['my_transactions'] = $this->db->table('transactions')
            ->where('agent_id', $userId)
            ->where('status', 'completed')
            ->countAllResults();

        // My revenue total
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('agent_id', $userId)
            ->where('status', 'completed')
            ->get()
            ->getRow();
        $stats['my_revenue'] = $result->amount ?? 0;

        // Revenue this month
        $result = $this->db->table('transactions')
            ->selectSum('amount')
            ->where('agent_id', $userId)
            ->where('status', 'completed')
            ->where('MONTH(completion_date)', date('m'))
            ->where('YEAR(completion_date)', date('Y'))
            ->get()
            ->getRow();
        $stats['revenue_month'] = $result->amount ?? 0;

        // My commissions paid
        $result = $this->db->table('commissions')
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('status', 'paid')
            ->get()
            ->getRow();
        $stats['commissions_paid'] = $result->amount ?? 0;

        // Commissions pending
        $result = $this->db->table('commissions')
            ->selectSum('amount')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->get()
            ->getRow();
        $stats['commissions_pending'] = $result->amount ?? 0;

        // My upcoming appointments
        $stats['upcoming_appointments'] = $this->db->table('appointments')
            ->where('agent_id', $userId)
            ->where('appointment_date >=', date('Y-m-d'))
            ->where('status', 'scheduled')
            ->countAllResults();

        // My tasks
        $stats['my_tasks'] = $this->db->table('tasks')
            ->where('assigned_to', $userId)
            ->where('status !=', 'completed')
            ->countAllResults();

        // My personal objective
        $stats['my_objective'] = $this->db->table('objectives')
            ->where('type', 'personal')
            ->where('user_id', $userId)
            ->where('period', date('Y-m'))
            ->where('status', 'active')
            ->get()
            ->getRowArray();

        // Monthly revenue (last 12 months)
        $stats['monthly_revenue'] = $this->db->query("
            SELECT 
                DATE_FORMAT(completion_date, '%Y-%m') as month,
                SUM(amount) as revenue,
                COUNT(*) as transactions
            FROM transactions
            WHERE agent_id = ?
                AND status = 'completed'
                AND completion_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
            GROUP BY DATE_FORMAT(completion_date, '%Y-%m')
            ORDER BY month ASC
        ", [$userId])->getResultArray();

        // Recent activities
        $stats['recent_activities'] = $this->db->table('audit_logs')
            ->select('audit_logs.*')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Recent transactions
        $stats['recent_transactions'] = $this->db->table('transactions t')
            ->select('t.*, p.title as property_title, c.first_name, c.last_name')
            ->join('properties p', 'p.id = t.property_id', 'left')
            ->join('clients c', 'c.id = t.client_id', 'left')
            ->where('t.agent_id', $userId)
            ->orderBy('t.created_at', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return $stats;
    }

    /**
     * Calculate objective progress percentage
     */
    public function calculateObjectiveProgress($objective)
    {
        if (!$objective) {
            return null;
        }

        $progress = [];
        $total = 0;
        $count = 0;

        // Revenue
        if ($objective['revenue_target'] > 0) {
            $progress['revenue'] = round(($objective['revenue_achieved'] / $objective['revenue_target']) * 100, 1);
            $total += $progress['revenue'];
            $count++;
        }

        // Contacts
        if ($objective['new_contacts_target'] > 0) {
            $progress['contacts'] = round(($objective['new_contacts_achieved'] / $objective['new_contacts_target']) * 100, 1);
            $total += $progress['contacts'];
            $count++;
        }

        // Properties rent
        if ($objective['properties_rent_target'] > 0) {
            $progress['rent'] = round(($objective['properties_rent_achieved'] / $objective['properties_rent_target']) * 100, 1);
            $total += $progress['rent'];
            $count++;
        }

        // Properties sale
        if ($objective['properties_sale_target'] > 0) {
            $progress['sale'] = round(($objective['properties_sale_achieved'] / $objective['properties_sale_target']) * 100, 1);
            $total += $progress['sale'];
            $count++;
        }

        // Transactions
        if ($objective['transactions_target'] > 0) {
            $progress['transactions'] = round(($objective['transactions_achieved'] / $objective['transactions_target']) * 100, 1);
            $total += $progress['transactions'];
            $count++;
        }

        // Overall
        $progress['overall'] = $count > 0 ? round($total / $count, 1) : 0;

        return $progress;
    }
}
