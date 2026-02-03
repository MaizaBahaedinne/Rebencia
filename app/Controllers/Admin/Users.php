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
            'role_id' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'password_hash' => $this->request->getPost('password'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'role_id' => $this->request->getPost('role_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'manager_id' => $this->request->getPost('manager_id'),
            'status' => 'active',
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

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'role_id' => $this->request->getPost('role_id'),
            'agency_id' => $this->request->getPost('agency_id'),
            'status' => $this->request->getPost('status')
        ];

        // Update password only if provided
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $data['password_hash'] = $newPassword;
        }

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/admin/users')->with('success', 'Utilisateur mis à jour');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour');
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
}
