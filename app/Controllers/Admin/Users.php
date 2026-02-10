<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $agencyModel;
    protected $hierarchyHelper;

    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->roleModel = model('RoleModel');
        $this->agencyModel = model('AgencyModel');
        $this->hierarchyHelper = new \App\Libraries\HierarchyHelper();
    }

    public function index()
    {
        $currentUserId = session()->get('user_id');
        $currentRoleLevel = session()->get('role_level');
        
        // Admin (role_level 100) voit tous les utilisateurs
        if ($currentRoleLevel == 100) {
            $users = $this->userModel->select('users.*, roles.display_name as role_name, agencies.name as agency_name')
                ->join('roles', 'roles.id = users.role_id')
                ->join('agencies', 'agencies.id = users.agency_id', 'left')
                ->orderBy('users.created_at', 'DESC')
                ->paginate(20);
        } else {
            // Autres utilisateurs : voir seulement leur hiérarchie (self + subordonnés récursifs)
            $accessibleUserIds = $this->hierarchyHelper->getAccessibleUserIds($currentUserId);
            
            if (empty($accessibleUserIds)) {
                $accessibleUserIds = [$currentUserId];
            }
            
            $users = $this->userModel->select('users.*, roles.display_name as role_name, agencies.name as agency_name')
                ->join('roles', 'roles.id = users.role_id')
                ->join('agencies', 'agencies.id = users.agency_id', 'left')
                ->whereIn('users.id', $accessibleUserIds)
                ->orderBy('users.created_at', 'DESC')
                ->paginate(20);
        }
        
        $data = [
            'title' => 'Gestion des Utilisateurs',
            'users' => $users
        ];

        return view('admin/users/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Nouvel Utilisateur',
            'roles' => $this->roleModel->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'managers' => $this->userModel->where('status', 'active')->findAll()
        ];

        return view('admin/users/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'username' => 'required|min_length[3]|is_unique[users.username]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'role_id' => 'required|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'role_id' => $this->request->getPost('role_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'status' => $this->request->getPost('status') ?? 'active',
            'email_verified' => true
        ];

        if ($this->userModel->insert($data)) {
            return redirect()->to('/admin/users')->with('success', 'Utilisateur créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Utilisateur non trouvé');
        }

        $data = [
            'title' => 'Modifier Utilisateur',
            'user' => $user,
            'roles' => $this->roleModel->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'managers' => $this->userModel->where('status', 'active')->where('id !=', $id)->findAll()
        ];

        return view('admin/users/edit', $data);
    }

    public function update($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Utilisateur non trouvé');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'username' => "required|min_length[3]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'role_id' => 'required|is_natural_no_zero',
        ];

        // Ajouter validation mot de passe si fourni
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $rules['password'] = 'required|min_length[8]';
            $rules['password_confirm'] = 'required|matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'role_id' => $this->request->getPost('role_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'status' => $this->request->getPost('status')
        ];

        // Update password only if provided
        if (!empty($newPassword)) {
            $data['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        // Skip model validation since we already validated in controller
        $this->userModel->skipValidation(true);
        
        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'Utilisateur modifié avec succès');
        }

        // Get model errors for debugging
        $modelErrors = $this->userModel->errors();
        $errorMessage = 'Erreur lors de la modification';
        
        if (!empty($modelErrors)) {
            return redirect()->back()->withInput()->with('errors', $modelErrors);
        }
        
        return redirect()->back()->withInput()->with('error', $errorMessage);
    }

    public function delete($id)
    {
        // Prevent deletion of own account
        if ($id == session()->get('user_id')) {
            return redirect()->to('/admin/users')->with('error', 'Vous ne pouvez pas supprimer votre propre compte');
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/admin/users')->with('success', 'Utilisateur supprimé');
        }

        return redirect()->to('/admin/users')->with('error', 'Erreur lors de la suppression');
    }

    /**
     * View user details with all related data
     */
    public function view($id)
    {
        // Get user with role and agency info
        $user = $this->userModel->select('users.*, roles.display_name as role_name, roles.level as role_level, agencies.name as agency_name, agencies.id as agency_id')
            ->join('roles', 'roles.id = users.role_id')
            ->join('agencies', 'agencies.id = users.agency_id', 'left')
            ->where('users.id', $id)
            ->first();
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Utilisateur non trouvé');
        }

        $db = \Config\Database::connect();
        
        // Get properties assigned to this user
        $properties = $db->table('properties')
            ->select('properties.*, zones.name as zone_name')
            ->join('zones', 'zones.id = properties.zone_id', 'left')
            ->where('properties.agent_id', $id)
            ->orderBy('properties.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get clients assigned to this user
        $clients = $db->table('clients')
            ->select('clients.*, CONCAT(clients.first_name, " ", clients.last_name) as full_name')
            ->where('clients.assigned_to', $id)
            ->orderBy('clients.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get transactions where user is the agent
        $transactions = $db->table('transactions')
            ->select('transactions.*, properties.reference as property_reference, properties.title as property_title,
                     clients.first_name as client_first_name, clients.last_name as client_last_name')
            ->join('properties', 'properties.id = transactions.property_id', 'left')
            ->join('clients', 'clients.id = transactions.client_id', 'left')
            ->where('transactions.agent_id', $id)
            ->orderBy('transactions.created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        // Get appointments assigned to this user
        $appointments = $db->table('appointments')
            ->select('appointments.*, properties.reference as property_reference, properties.title as property_title,
                     clients.first_name as client_first_name, clients.last_name as client_last_name')
            ->join('properties', 'properties.id = appointments.property_id', 'left')
            ->join('clients', 'clients.id = appointments.client_id', 'left')
            ->where('appointments.user_id', $id)
            ->orderBy('appointments.scheduled_at', 'DESC')
            ->limit(50)
            ->get()
            ->getResultArray();
        
        // Get commissions earned by this user
        $commissions = $db->table('transaction_commissions')
            ->select('transaction_commissions.*, transactions.reference as transaction_reference,
                     properties.reference as property_reference')
            ->join('transactions', 'transactions.id = transaction_commissions.transaction_id', 'left')
            ->join('properties', 'properties.id = transaction_commissions.property_id', 'left')
            ->where('transaction_commissions.agent_id', $id)
            ->orderBy('transaction_commissions.created_at', 'DESC')
            ->get()
            ->getResultArray();

        // Get team members if this user is a manager
        $teamMembers = [];
        $teamStats = [];
        if ($user['role_name'] === 'Manager' || $user['role_name'] === 'Admin') {
            $teamMembers = $this->userModel->select('users.*, agencies.name as agency_name')
                ->join('agencies', 'agencies.id = users.agency_id', 'left')
                ->where('users.manager_id', $id)
                ->where('users.status', 'active')
                ->orderBy('users.first_name', 'ASC')
                ->findAll();
            
            // Get stats for each team member
            foreach ($teamMembers as &$member) {
                $memberId = $member['id'];
                
                // Count properties
                $propertiesCount = $db->table('properties')
                    ->where('agent_id', $memberId)
                    ->countAllResults();
                
                // Count clients
                $clientsCount = $db->table('clients')
                    ->where('assigned_to', $memberId)
                    ->countAllResults();
                
                // Count transactions and calculate total
                $memberTransactions = $db->table('transactions')
                    ->select('SUM(final_price) as total_sales, COUNT(*) as count')
                    ->where('agent_id', $memberId)
                    ->where('status', 'completed')
                    ->get()
                    ->getRowArray();
                
                // Calculate commissions
                $memberCommissions = $db->table('transaction_commissions')
                    ->select('SUM(agent_commission_amount) as total_commission')
                    ->where('agent_id', $memberId)
                    ->get()
                    ->getRowArray();
                
                $member['stats'] = [
                    'properties' => $propertiesCount,
                    'clients' => $clientsCount,
                    'transactions' => $memberTransactions['count'] ?? 0,
                    'total_sales' => $memberTransactions['total_sales'] ?? 0,
                    'total_commission' => $memberCommissions['total_commission'] ?? 0
                ];
            }
        }

        $data = [
            'title' => 'Détails Utilisateur - ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'properties' => $properties,
            'clients' => $clients,
            'transactions' => $transactions,
            'appointments' => $appointments,
            'commissions' => $commissions,
            'team_members' => $teamMembers
        ];

        return view('admin/users/view', $data);
    }

    /**
     * User profile page
     */
    public function profile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/login')->with('error', 'Session expirée');
        }

        // Get user with relationships
        $user = $this->userModel->select('users.*, roles.display_name as role_name, agencies.name as agency_name')
            ->join('roles', 'roles.id = users.role_id')
            ->join('agencies', 'agencies.id = users.agency_id', 'left')
            ->where('users.id', $userId)
            ->first();

        $data = [
            'title' => 'Mon Profil',
            'user' => $user,
            'agencies' => $this->agencyModel->where('status', 'active')->findAll()
        ];

        return view('admin/users/profile', $data);
    }

    /**
     * Update profile
     */
    public function updateProfile()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/login')->with('error', 'Session expirée');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $userId . ']',
            'phone' => 'permit_empty|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
        ];
        
        // Only allow agency_id change for super admins
        $roleLevel = session()->get('role_level');
        if ($roleLevel >= 100) {
            $data['agency_id'] = $this->request->getPost('agency_id');
        }

        // Handle avatar upload
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            // Validate file size (max 2MB)
            if ($avatar->getSizeByUnit('mb') > 2) {
                return redirect()->back()->withInput()->with('error', 'La taille de l\'image ne doit pas dépasser 2MB');
            }
            
            // Validate file type
            if (!in_array($avatar->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'])) {
                return redirect()->back()->withInput()->with('error', 'Format d\'image non supporté. Utilisez JPG, PNG ou GIF');
            }
            
            // Create upload directory if it doesn't exist
            $uploadPath = FCPATH . 'uploads/avatars';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            
            // Delete old avatar if exists
            if (!empty($user['avatar']) && file_exists($uploadPath . '/' . $user['avatar'])) {
                @unlink($uploadPath . '/' . $user['avatar']);
            }
            
            $newName = $avatar->getRandomName();
            $avatar->move($uploadPath, $newName);
            $data['avatar'] = $newName;
        }

        // Skip model validation since we're only updating profile fields
        if ($this->userModel->skipValidation(true)->update($userId, $data)) {
            // Update session with new avatar if changed
            if (isset($data['avatar'])) {
                session()->set('user_avatar', $data['avatar']);
            }
            // Update session with new name if changed
            if (isset($data['first_name']) || isset($data['last_name'])) {
                $fullName = trim(($data['first_name'] ?? $user['first_name']) . ' ' . ($data['last_name'] ?? $user['last_name']));
                session()->set('user_name', $fullName);
            }
            
            return redirect()->to('/admin/profile')->with('success', 'Profil mis à jour avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/admin/login')->with('error', 'Session expirée');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('password_errors', $validation->getErrors());
        }

        // Verify current password
        if (!password_verify($this->request->getPost('current_password'), $user['password_hash'])) {
            return redirect()->back()->with('password_error', 'Mot de passe actuel incorrect');
        }

        $data = [
            'password_hash' => password_hash($this->request->getPost('new_password'), PASSWORD_BCRYPT)
        ];

        if ($this->userModel->update($userId, $data)) {
            return redirect()->to('/admin/profile')->with('success', 'Mot de passe modifié avec succès');
        }

        return redirect()->back()->with('password_error', 'Erreur lors du changement de mot de passe');
    }

    /**
     * Switch user role
     */
    public function switchRole()
    {
        $userId = session()->get('user_id');
        $roleId = $this->request->getPost('role_id');

        if (!$userId || !$roleId) {
            return redirect()->back()->with('error', 'Paramètres manquants');
        }

        // Verify user has this role
        $db = \Config\Database::connect();
        $userRole = $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->get()
            ->getRowArray();

        if (!$userRole) {
            return redirect()->back()->with('error', 'Vous n\'avez pas accès à ce rôle');
        }

        // Switch role
        if ($this->userModel->switchRole($userId, $roleId)) {
            // Update session with new role
            $activeRole = $this->userModel->getActiveRole($userId);
            session()->set([
                'role_id' => $activeRole['role_id'],
                'role_name' => $activeRole['name'],
                'role_display_name' => $activeRole['display_name'],
                'role_level' => $activeRole['level']
            ]);

            return redirect()->back()->with('success', 'Rôle changé vers: ' . $activeRole['display_name']);
        }

        return redirect()->back()->with('error', 'Erreur lors du changement de rôle');
    }

    /**
     * Manage user roles (assign/remove multiple roles)
     */
    public function manageRoles($userId)
    {
        $user = $this->userModel->getUserWithRoles($userId);
        
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'Utilisateur introuvable');
        }

        // Récupérer le niveau du rôle de l'utilisateur connecté
        $currentUserRoleLevel = session()->get('role_level');
        
        // Filtrer les rôles : afficher uniquement ceux en dessous du niveau de l'utilisateur connecté
        // Admin (level 100) voit tous les rôles
        if ($currentUserRoleLevel == 100) {
            $availableRoles = $this->roleModel->orderBy('level', 'DESC')->findAll();
        } else {
            $availableRoles = $this->roleModel
                ->where('level <', $currentUserRoleLevel)
                ->orderBy('level', 'DESC')
                ->findAll();
        }

        $data = [
            'title' => 'Gestion des Rôles - ' . $user['first_name'] . ' ' . $user['last_name'],
            'page_title' => 'Gestion des Rôles',
            'user' => $user,
            'allRoles' => $availableRoles
        ];

        return view('admin/users/manage_roles', $data);
    }

    /**
     * Assign role to user
     */
    public function assignRole($userId)
    {
        $roleId = $this->request->getPost('role_id');
        $setActive = $this->request->getPost('set_active') == '1';
        $setDefault = $this->request->getPost('set_default') == '1';

        if ($this->userModel->assignRole($userId, $roleId, $setActive, $setDefault)) {
            return redirect()->back()->with('success', 'Rôle assigné avec succès');
        }

        return redirect()->back()->with('error', 'Ce rôle est déjà assigné à cet utilisateur');
    }

    /**
     * Set default role for user
     */
    public function setDefaultRole($userId)
    {
        $roleId = $this->request->getPost('role_id');

        if (!$roleId) {
            return redirect()->back()->with('error', 'Rôle non spécifié');
        }

        // Verify user has this role
        $db = \Config\Database::connect();
        $userRole = $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->get()
            ->getRowArray();

        if (!$userRole) {
            return redirect()->back()->with('error', 'L\'utilisateur n\'a pas ce rôle');
        }

        if ($this->userModel->setDefaultRole($userId, $roleId)) {
            return redirect()->back()->with('success', 'Rôle par défaut défini avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors de la définition du rôle par défaut');
    }

    /**
     * Remove role from user
     */
    public function removeRole($userId, $roleId)
    {
        // Check if user has more than one role
        $db = \Config\Database::connect();
        $roleCount = $db->table('user_roles')
            ->where('user_id', $userId)
            ->countAllResults();

        if ($roleCount <= 1) {
            return redirect()->back()->with('error', 'L\'utilisateur doit avoir au moins un rôle');
        }

        if ($this->userModel->removeRole($userId, $roleId)) {
            return redirect()->back()->with('success', 'Rôle retiré avec succès');
        }

        return redirect()->back()->with('error', 'Erreur lors du retrait du rôle');
    }
    
    /**
     * Login as another user (admin only)
     */
    public function loginAs($userId)
    {
        // Vérifier que l'utilisateur actuel est admin
        $currentUserRole = session()->get('role_level');
        if ($currentUserRole != 100) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }
        
        // Récupérer l'utilisateur cible
        $targetUser = $this->userModel->find($userId);
        if (!$targetUser) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé');
        }
        
        // Sauvegarder l'ID admin original
        if (!session()->has('original_user_id')) {
            session()->set('original_user_id', session()->get('user_id'));
            session()->set('original_user_name', session()->get('user_name'));
        }
        
        // Charger les informations du nouvel utilisateur
        $role = $this->roleModel->find($targetUser['role_id']);
        
        // Mettre à jour la session
        session()->set([
            'user_id' => $targetUser['id'],
            'user_name' => $targetUser['first_name'] . ' ' . $targetUser['last_name'],
            'user_email' => $targetUser['email'],
            'role_id' => $targetUser['role_id'],
            'role_name' => $role['display_name'],
            'role_level' => $role['level'],
            'is_impersonating' => true
        ]);
        
        return redirect()->to('/admin/dashboard')->with('info', 'Vous êtes maintenant connecté en tant que ' . $targetUser['first_name'] . ' ' . $targetUser['last_name']);
    }
    
    /**
     * Return to original admin account
     */
    public function stopImpersonation()
    {
        if (!session()->has('original_user_id')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Aucune impersonation active');
        }
        
        $originalUserId = session()->get('original_user_id');
        $originalUser = $this->userModel->find($originalUserId);
        
        if (!$originalUser) {
            session()->destroy();
            return redirect()->to('/admin/login')->with('error', 'Session invalide');
        }
        
        $role = $this->roleModel->find($originalUser['role_id']);
        
        // Restaurer la session originale
        session()->remove('original_user_id');
        session()->remove('original_user_name');
        session()->remove('is_impersonating');
        
        session()->set([
            'user_id' => $originalUser['id'],
            'user_name' => $originalUser['first_name'] . ' ' . $originalUser['last_name'],
            'user_email' => $originalUser['email'],
            'role_id' => $originalUser['role_id'],
            'role_name' => $role['display_name'],
            'role_level' => $role['level']
        ]);
        
        return redirect()->to('/admin/dashboard')->with('success', 'Vous êtes de retour sur votre compte administrateur');
    }

    /**
     * Bulk management page
     */
    public function bulkManage()
    {
        // Seulement l'admin peut accéder à la gestion en masse
        if (session()->get('role_level') != 100) {
            return redirect()->to('/admin/users')->with('error', 'Accès non autorisé. Seul l\'administrateur peut gérer en masse.');
        }
        
        $data = [
            'title' => 'Gestion en masse des utilisateurs',
            'users' => $this->userModel->select('users.*, roles.name as role_name, agencies.name as agency_name, 
                                                 CONCAT(managers.first_name, " ", managers.last_name) as manager_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->join('agencies', 'agencies.id = users.agency_id', 'left')
                ->join('users as managers', 'managers.id = users.manager_id', 'left')
                ->orderBy('users.first_name', 'ASC')
                ->findAll(),
            'agencies' => $this->agencyModel->where('status', 'active')->findAll(),
            'roles' => $this->roleModel->findAll(),
            'managers' => $this->userModel->select('users.*, roles.name as role_name')
                ->join('roles', 'roles.id = users.role_id', 'left')
                ->orderBy('users.first_name', 'ASC')
                ->findAll()
        ];

        return view('admin/users/bulk_manage', $data);
    }

    /**
     * Execute bulk action
     */
    public function bulkAction()
    {
        // Seulement l'admin peut exécuter les actions en masse
        if (session()->get('role_level') != 100) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Accès non autorisé. Seul l\'administrateur peut effectuer des actions en masse.'
            ]);
        }
        
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/users/bulk-manage');
        }

        $json = $this->request->getJSON();
        $userIds = $json->user_ids ?? [];
        $action = $json->action ?? '';

        if (empty($userIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Aucun utilisateur sélectionné'
            ]);
        }

        try {
            switch ($action) {
                case 'agency':
                    $agencyId = $json->agency_id ?? null;
                    $this->userModel->whereIn('id', $userIds)->set(['agency_id' => $agencyId])->update();
                    $message = 'Agence mise à jour pour ' . count($userIds) . ' utilisateur(s)';
                    break;

                case 'manager':
                    $managerId = $json->manager_id ?? null;
                    $this->userModel->whereIn('id', $userIds)->set(['manager_id' => $managerId])->update();
                    $message = 'Manager mis à jour pour ' . count($userIds) . ' utilisateur(s)';
                    break;

                case 'role':
                    $roleId = $json->role_id ?? null;
                    if (!$roleId) {
                        throw new \Exception('Rôle non spécifié');
                    }
                    $this->userModel->whereIn('id', $userIds)->set(['role_id' => $roleId])->update();
                    $message = 'Rôle mis à jour pour ' . count($userIds) . ' utilisateur(s)';
                    break;

                case 'status':
                    $status = $json->status ?? 'active';
                    $this->userModel->whereIn('id', $userIds)->set(['status' => $status])->update();
                    $message = 'Statut mis à jour pour ' . count($userIds) . ' utilisateur(s)';
                    break;

                case 'delete':
                    // Vérifier qu'on ne supprime pas l'utilisateur actuel
                    $currentUserId = session()->get('user_id');
                    if (in_array($currentUserId, $userIds)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Vous ne pouvez pas supprimer votre propre compte'
                        ]);
                    }
                    $this->userModel->whereIn('id', $userIds)->delete();
                    $message = count($userIds) . ' utilisateur(s) supprimé(s)';
                    break;

                default:
                    throw new \Exception('Action non reconnue');
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage()
            ]);
        }
    }
}
