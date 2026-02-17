<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class CommissionRates extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Liste des taux de commission par utilisateur
     */
    public function index()
    {
        if (!canRead('users')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Accès refusé');
        }

        // Récupérer les taux par défaut du système
        $defaults = $this->userModel->db->table('commission_defaults')->limit(1)->get()->getRowArray();
        if (!$defaults) {
            $defaults = [
                'agent_commission_share_sale' => 50.00,
                'agent_commission_share_rent' => 50.00
            ];
        }

        // Récupérer tous les utilisateurs avec détails
        $users = $this->userModel
            ->select('users.id, users.first_name, users.last_name, users.email, users.status,
                     users.agent_commission_share_sale, users.agent_commission_share_rent,
                     users.is_commission_exceptional, users.commission_exceptional_note,
                     roles.display_name as role_name, agencies.name as agency_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('agencies', 'agencies.id = users.agency_id', 'left')
            ->orderBy('users.first_name', 'ASC')
            ->orderBy('users.last_name', 'ASC')
            ->findAll();

        // Filtres
        $filterStatus = $this->request->getGet('status');
        $filterRole = $this->request->getGet('role');
        $filterAgency = $this->request->getGet('agency');
        $filterException = $this->request->getGet('exception');

        if ($filterStatus) {
            $users = array_filter($users, fn($u) => $u['status'] === $filterStatus);
        }
        if ($filterRole) {
            $users = array_filter($users, fn($u) => $u['role_name'] === $filterRole);
        }
        if ($filterAgency) {
            $users = array_filter($users, fn($u) => $u['agency_name'] === $filterAgency);
        }
        if ($filterException) {
            $users = array_filter($users, fn($u) => (bool)$u['is_commission_exceptional']);
        }

        // Récupérer les rôles et agences pour les filtres
        $roles = $this->userModel->db->table('roles')->select('id, display_name')->get()->getResultArray();
        $agencies = $this->userModel->db->table('agencies')->where('status', 'active')->get()->getResultArray();

        $data = [
            'title' => 'Gestion des Taux de Commission',
            'defaults' => $defaults,
            'users' => array_values($users), // Réindexer
            'roles' => $roles,
            'agencies' => $agencies,
            'filterStatus' => $filterStatus,
            'filterRole' => $filterRole,
            'filterAgency' => $filterAgency,
            'filterException' => $filterException
        ];

        return view('admin/commission_rates/index', $data);
    }

    /**
     * Mettre à jour les taux par défaut du système
     */
    public function saveDefaults()
    {
        if (!canUpdate('users') || session()->get('role_level') < 100) {
            return redirect()->back()->with('error', 'Accès refusé - Admin uniquement');
        }

        $sale = (float) $this->request->getPost('agent_commission_share_sale');
        $rent = (float) $this->request->getPost('agent_commission_share_rent');

        // Validation
        if ($sale < 0 || $sale > 100 || $rent < 0 || $rent > 100) {
            return redirect()->back()->with('error', 'Les pourcentages doivent être entre 0 et 100');
        }

        // Mettre à jour ou créer
        $existing = $this->userModel->db->table('commission_defaults')->limit(1)->get()->getRowArray();
        
        if ($existing) {
            $this->userModel->db->table('commission_defaults')
                ->update([
                    'agent_commission_share_sale' => $sale,
                    'agent_commission_share_rent' => $rent,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        } else {
            $this->userModel->db->table('commission_defaults')
                ->insert([
                    'agent_commission_share_sale' => $sale,
                    'agent_commission_share_rent' => $rent,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
        }

        return redirect()->back()->with('success', 'Taux par défaut mis à jour avec succès');
    }

    /**
     * Mettre à jour les taux d'un utilisateur (AJAX)
     */
    public function updateRate($userId)
    {
        if (!canUpdate('users')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Accès refusé'
            ])->setStatusCode(403);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requête invalide'
            ])->setStatusCode(400);
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ])->setStatusCode(404);
        }

        $field = $this->request->getPost('field');
        $value = $this->request->getPost('value');

        // Validation
        if (!in_array($field, ['agent_commission_share_sale', 'agent_commission_share_rent', 'is_commission_exceptional', 'commission_exceptional_note'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Champ invalide'
            ])->setStatusCode(400);
        }

        // Validation des valeurs
        if (in_array($field, ['agent_commission_share_sale', 'agent_commission_share_rent'])) {
            $value = (float) $value;
            if ($value < 0 || $value > 100) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Le pourcentage doit être entre 0 et 100'
                ])->setStatusCode(400);
            }
        }

        if ($field === 'is_commission_exceptional') {
            $value = (int) $value;
        }

        // Mise à jour
        $updateData = [$field => $value];
        $this->userModel->update($userId, $updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Taux mis à jour avec succès',
            'data' => [
                'user_id' => $userId,
                'field' => $field,
                'value' => $value
            ]
        ]);
    }

    /**
     * Exporter les taux en CSV
     */
    public function export()
    {
        if (!canRead('users')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Accès refusé');
        }

        $users = $this->userModel
            ->select('first_name, last_name, email, agent_commission_share_sale, 
                     agent_commission_share_rent, is_commission_exceptional, 
                     commission_exceptional_note')
            ->orderBy('first_name', 'ASC')
            ->findAll();

        // Générer CSV
        $filename = 'commission_rates_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, ['Prénom', 'Nom', 'Email', 'Split Ventes Agent (%)', 'Split Locations Agent (%)', 'Profil Exceptionnel', 'Note']);
        
        // Data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['first_name'],
                $user['last_name'],
                $user['email'],
                $user['agent_commission_share_sale'],
                $user['agent_commission_share_rent'],
                $user['is_commission_exceptional'] ? 'Oui' : 'Non',
                $user['commission_exceptional_note']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Réinitialiser les taux par défaut (admin)
     */
    public function resetDefaults()
    {
        if (!canUpdate('users') || session()->get('role_level') < 100) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        // Récupérer les taux par défaut du système
        $defaults = $this->userModel->db->table('commission_defaults')->limit(1)->get()->getRowArray();
        if (!$defaults) {
            $defaults = ['agent_commission_share_sale' => 50.00, 'agent_commission_share_rent' => 50.00];
        }

        $affected = $this->userModel->db->table('users')
            ->update([
                'agent_commission_share_sale' => $defaults['agent_commission_share_sale'],
                'agent_commission_share_rent' => $defaults['agent_commission_share_rent'],
                'is_commission_exceptional' => 0,
                'commission_exceptional_note' => null
            ]);

        return redirect()->back()->with('success', sprintf(
            'Tous les taux ont été réinitialisés aux valeurs par défaut (Ventes: %s%%, Locations: %s%%)',
            number_format($defaults['agent_commission_share_sale'], 2),
            number_format($defaults['agent_commission_share_rent'], 2)
        ));
    }
}
