<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $agencyModel;

    public function __construct()
    {
        $this->userModel = model('UserModel');
        $this->roleModel = model('RoleModel');
        $this->agencyModel = model('AgencyModel');
    }

    public function index()
    {
        $data = [
            'title' => 'Gestion des Utilisateurs',
            'users' => $this->userModel->select('users.*, roles.display_name as role_name, agencies.name as agency_name')
                ->join('roles', 'roles.id = users.role_id')
                ->join('agencies', 'agencies.id = users.agency_id', 'left')
                ->orderBy('users.created_at', 'DESC')
                ->paginate(20)
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
            'cin' => $this->request->getPost('cin'),
            'role_id' => $this->request->getPost('role_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'hire_date' => $this->request->getPost('hire_date'),
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
            'cin' => $this->request->getPost('cin'),
            'role_id' => $this->request->getPost('role_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'hire_date' => $this->request->getPost('hire_date'),
            'status' => $this->request->getPost('status')
        ];

        // Update password only if provided
        if (!empty($newPassword)) {
            $data['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT);
        }

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'Utilisateur modifié avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la modification');
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
            $uploadPath = WRITEPATH . 'uploads/avatars';
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

        if ($this->userModel->update($userId, $data)) {
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

        $data = [
            'title' => 'Gestion des Rôles - ' . $user['first_name'] . ' ' . $user['last_name'],
            'page_title' => 'Gestion des Rôles',
            'user' => $user,
            'allRoles' => $this->roleModel->orderBy('level', 'DESC')->findAll()
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
}
