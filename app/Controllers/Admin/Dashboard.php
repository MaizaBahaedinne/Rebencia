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
            'stats' => $this->getDashboardStats()
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
            'properties_published' => $db->table('properties')->where('status', 'published')->countAllResults(),
            'transactions_pending' => $db->table('transactions')->where('status', 'pending')->countAllResults(),
            'leads_count' => $db->table('clients')->where('status', 'lead')->countAllResults(),
        ];
    }
}
