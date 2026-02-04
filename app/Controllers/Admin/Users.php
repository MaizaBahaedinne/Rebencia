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
            'cin' => $this->request->getPost('cin'),
            'agency_id' => $this->request->getPost('agency_id'),
        ];

        // Handle avatar upload
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && !$avatar->hasMoved()) {
            // Delete old avatar if exists
            if ($user['avatar'] && file_exists(ROOTPATH . 'public/uploads/avatars/' . $user['avatar'])) {
                unlink(ROOTPATH . 'public/uploads/avatars/' . $user['avatar']);
            }
            
            $newName = $avatar->getRandomName();
            $avatar->move(ROOTPATH . 'public/uploads/avatars', $newName);
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
}
