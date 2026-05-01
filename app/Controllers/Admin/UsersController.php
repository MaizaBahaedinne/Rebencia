<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

/**
 * UsersController – Gestion des utilisateurs internes.
 */
class UsersController extends BaseController
{
    protected UserModel $model;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->model     = new UserModel();
        $this->roleModel = new RoleModel();
    }

    /** Liste des utilisateurs. */
    public function index(): string
    {
        $this->requirePermission('users.view');

        $filters = [
            'status'  => $this->request->getGet('status'),
            'role_id' => $this->request->getGet('role_id'),
            'search'  => $this->request->getGet('search'),
        ];

        // Restriction agence : un non-admin ne voit que les membres de son agence
        $agencyId = (int) session()->get('agency_id');
        if ($agencyId && ! $this->auth->hasPermission('agencies.create')) {
            $filters['agency_id'] = $agencyId;
        }

        return $this->render('admin/users/index', [
            'page_title' => 'Gestion des utilisateurs',
            'users'      => $this->model->getWithRole($filters),
            'roles'      => $this->roleModel->findAll(),
            'filters'    => $filters,
        ]);
    }

    /** Formulaire création. */
    public function create(): string
    {
        $this->requirePermission('users.create');

        try { $agencies = (new \App\Models\AgencyModel())->getActive(); } catch (\Throwable $e) { $agencies = []; }

        return $this->render('admin/users/form', [
            'page_title'  => 'Nouvel utilisateur',
            'roles'       => $this->roleModel->where('is_active', 1)->findAll(),
            'user'        => [],
            'userRoleIds' => [],
            'agencies'    => $agencies,
        ]);
    }

    /** Enregistrement. */
    public function store()
    {
        $this->requirePermission('users.create');

        $roleIds = array_filter(array_map('intval', (array) $this->request->getPost('role_ids')));

        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'email'      => 'required|valid_email|is_unique[users.email]',
            'password'   => 'required|min_length[8]|max_length[255]',
        ];

        if (empty($roleIds)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez sélectionner au moins un rôle.');
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'role_id'       => $roleIds[0],
            'agency_id'     => $this->request->getPost('agency_id') ?: null,
            'first_name'    => $this->request->getPost('first_name'),
            'last_name'     => $this->request->getPost('last_name'),
            'email'         => $this->request->getPost('email'),
            'phone'         => ($this->request->getPost('phone') ?? '') ?: null,
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT, ['cost' => 12]),
            'status'        => $this->request->getPost('status') ?: 'active',
        ];

        // Avatar upload
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && ! $avatar->hasMoved()) {
            if (! in_array(strtolower($avatar->getClientExtension()), ['jpg','jpeg','png','gif','webp'], true)) {
                return redirect()->back()->withInput()->with('error', 'Format avatar invalide (jpg, png, gif, webp).');
            }
            if ($avatar->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Avatar trop volumineux (max 2 Mo).');
            }
            $newName = 'user_' . time() . '_' . random_int(1000, 9999) . '.' . $avatar->getClientExtension();
            $avatar->move(ROOTPATH . 'public/uploads/avatars/', $newName);
            $data['avatar'] = 'uploads/avatars/' . $newName;
        }

        $newId = $this->model->insert($data);
        $this->model->syncRoles($newId ?: $this->model->getInsertID(), $roleIds);

        $this->log->activity('user.create', 'users', 'user', $this->model->getInsertID(), 'Création utilisateur');

        return redirect()->to('/admin/users')->with('success', 'Utilisateur créé avec succès.');
    }

    /** Fiche utilisateur. */
    public function show(int $id): string
    {
        $this->requirePermission('users.view');

        $user = $this->model->findWithRole($id);
        if (! $user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Utilisateur #{$id} introuvable");
        }

        return $this->render('admin/users/show', [
            'page_title' => $user['first_name'] . ' ' . $user['last_name'],
            'user'       => $user,
            'permissions'=> $this->model->getPermissions($id),
        ]);
    }

    /** Formulaire édition. */
    public function edit(int $id): string
    {
        $this->requirePermission('users.edit');

        $user = $this->model->findWithRole($id);
        if (! $user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Utilisateur #{$id} introuvable");
        }

        $userRoles   = $this->model->getUserRoles($id);
        $userRoleIds = array_column($userRoles, 'role_id');
        // Fallback: si user_roles est vide (avant migration), utiliser role_id principal
        if (empty($userRoleIds) && !empty($user['role_id'])) {
            $userRoleIds = [(int) $user['role_id']];
        }

        try { $agencies = (new \App\Models\AgencyModel())->getActive(); } catch (\Throwable $e) { $agencies = []; }

        return $this->render('admin/users/form', [
            'page_title'  => 'Modifier – ' . $user['first_name'],
            'user'        => $user,
            'roles'       => $this->roleModel->where('is_active', 1)->findAll(),
            'userRoleIds' => $userRoleIds,
            'agencies'    => $agencies,
        ]);
    }

    /** Mise à jour. */
    public function update(int $id)
    {
        $this->requirePermission('users.edit');

        $user = $this->model->find($id);
        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Utilisateur introuvable.');
        }

        $roleIds = array_filter(array_map('intval', (array) $this->request->getPost('role_ids')));

        if (empty($roleIds)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez sélectionner au moins un rôle.');
        }

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => ($this->request->getPost('phone') ?? '') ?: null,
            'role_id'    => $roleIds[0],
            'agency_id'  => $this->request->getPost('agency_id') ?: null,
            'status'     => $this->request->getPost('status'),
        ];

        // Nouveau mot de passe (optionnel)
        $newPassword = $this->request->getPost('password');
        if (! empty($newPassword)) {
            $data['password_hash'] = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        // Avatar upload
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && ! $avatar->hasMoved()) {
            if (! in_array(strtolower($avatar->getClientExtension()), ['jpg','jpeg','png','gif','webp'], true)) {
                return redirect()->back()->withInput()->with('error', 'Format avatar invalide (jpg, png, gif, webp).');
            }
            if ($avatar->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'Avatar trop volumineux (max 2 Mo).');
            }
            // Supprimer l'ancien avatar
            if (! empty($user['avatar'])) {
                @unlink(ROOTPATH . 'public/' . $user['avatar']);
            }
            $newName = 'user_' . $id . '_' . time() . '.' . $avatar->getClientExtension();
            $avatar->move(ROOTPATH . 'public/uploads/avatars/', $newName);
            $data['avatar'] = 'uploads/avatars/' . $newName;
        }

        // Supprimer l'avatar si demandé
        if ($this->request->getPost('remove_avatar') === '1' && empty($data['avatar'])) {
            if (! empty($user['avatar'])) {
                @unlink(ROOTPATH . 'public/' . $user['avatar']);
            }
            $data['avatar'] = null;
        }

        $this->model->update($id, $data);
        $this->model->syncRoles($id, array_values($roleIds));

        $this->log->activity('user.update', 'users', 'user', $id, 'Modification utilisateur');

        return redirect()->to('/admin/users/' . $id . '/edit')->with('success', 'Utilisateur mis à jour.');
    }

    /** Active / suspend / met en attente. */
    public function toggleStatus(int $id)
    {
        $this->requirePermission('users.edit');

        $user = $this->model->find($id);
        if (! $user) {
            return $this->json(['error' => 'Introuvable'], 404);
        }

        $newStatus = $this->request->getPost('status');
        if (! in_array($newStatus, ['active', 'pending', 'suspended'])) {
            return $this->json(['error' => 'Statut invalide'], 422);
        }

        $this->model->update($id, ['status' => $newStatus]);
        $this->log->activity('user.status', 'users', 'user', $id, "Statut → {$newStatus}");

        return $this->json(['success' => true, 'status' => $newStatus]);
    }

    /** Suppression (soft). */
    public function delete(int $id)
    {
        $this->requirePermission('users.delete');

        if ($id === $this->auth->id()) {
            return redirect()->to('/admin/users')->with('error', 'Vous ne pouvez pas vous supprimer.');
        }

        $this->model->delete($id);
        $this->log->activity('user.delete', 'users', 'user', $id, 'Suppression utilisateur');

        return redirect()->to('/admin/users')->with('success', 'Utilisateur supprimé.');
    }

    /** Changement de rôle actif (switch role). */
    public function switchRole()
    {
        $roleId = (int) $this->request->getPost('role_id');
        $userId = $this->auth->id();

        // Vérifier que ce rôle est bien assigné à l'utilisateur
        $assigned = array_column($this->model->getUserRoles($userId), 'role_id');
        if (! in_array($roleId, array_map('intval', $assigned), true)) {
            return redirect()->back()->with('error', 'Rôle non autorisé.');
        }

        $role = $this->roleModel->find($roleId);
        if (! $role) {
            return redirect()->back()->with('error', 'Rôle introuvable.');
        }

        // Mettre à jour le rôle principal en session
        $newPermissions = \Config\Database::connect()->table('permissions p')
            ->select('p.name')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $roleId)
            ->get()->getResultArray();
        $permNames = array_column($newPermissions, 'name');

        session()->set([
            'user_role'       => $role['name'],
            'user_role_label' => $role['label'] ?? $role['name'],
            'user_role_id'    => $roleId,
            'permissions'     => $permNames,
        ]);

        // Mettre à jour role_id en BDD
        $this->model->update($userId, ['role_id' => $roleId]);

        $this->log->activity('user.switch_role', 'users', 'user', $userId, 'Changement de rôle → ' . ($role['label'] ?? $role['name']));

        $redirectTo = $this->request->getPost('redirect') ?: '/admin/dashboard';
        return redirect()->to($redirectTo)->with('success', 'Rôle actif : ' . esc($role['label'] ?? $role['name']));
    }

    /** Profil de l'utilisateur connecté. */
    public function profile(): string
    {
        $user = $this->model->findWithRole($this->auth->id());

        return $this->render('admin/users/profile', [
            'page_title' => 'Mon profil',
            'user'       => $user,
        ]);
    }

    /** Mise à jour du profil. */
    public function updateProfile()
    {
        $id   = $this->auth->id();
        $user = $this->model->find($id);

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => "required|valid_email|is_unique[users.email,id,{$id}]",
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name'  => $this->request->getPost('last_name'),
            'email'      => $this->request->getPost('email'),
            'phone'      => ($this->request->getPost('phone') ?? '') ?: null,
        ];

        $newPass = $this->request->getPost('password');
        if (! empty($newPass)) {
            if (strlen($newPass) < 8) {
                return redirect()->back()->with('error', 'Mot de passe trop court (min. 8 caractères).');
            }
            $data['password_hash'] = password_hash($newPass, PASSWORD_BCRYPT, ['cost' => 12]);
        }

        // Avatar upload
        $avatar = $this->request->getFile('avatar');
        if ($avatar && $avatar->isValid() && ! $avatar->hasMoved()) {
            if (! in_array(strtolower($avatar->getClientExtension()), ['jpg','jpeg','png','gif','webp'], true)) {
                return redirect()->back()->with('error', 'Format avatar invalide (jpg, png, gif, webp).');
            }
            if ($avatar->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Avatar trop volumineux (max 2 Mo).');
            }
            if (! empty($user['avatar'])) {
                @unlink(ROOTPATH . 'public/' . $user['avatar']);
            }
            $newName = 'user_' . $id . '_' . time() . '.' . $avatar->getClientExtension();
            $avatar->move(ROOTPATH . 'public/uploads/avatars/', $newName);
            $data['avatar'] = 'uploads/avatars/' . $newName;
        } elseif ($this->request->getPost('remove_avatar') === '1') {
            if (! empty($user['avatar'])) {
                @unlink(ROOTPATH . 'public/' . $user['avatar']);
            }
            $data['avatar'] = null;
        }

        $this->model->update($id, $data);

        // Rafraîchir la session
        session()->set('user_name', $data['first_name'] . ' ' . $data['last_name']);
        session()->set('user_email', $data['email']);
        if (isset($data['avatar'])) {
            session()->set('user_avatar', $data['avatar']);
        }

        return redirect()->to('/admin/profile')->with('success', 'Profil mis à jour.');
    }

    /** Changement de mot de passe. */
    public function changePassword()
    {
        $id   = $this->auth->id();
        $user = $this->model->find($id);

        $current = $this->request->getPost('current_password');
        if (! password_verify($current, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Mot de passe actuel incorrect.');
        }

        $new = $this->request->getPost('new_password');
        if (strlen($new) < 8) {
            return redirect()->back()->with('error', 'Nouveau mot de passe trop court (min. 8 caractères).');
        }

        $this->model->update($id, [
            'password_hash' => password_hash($new, PASSWORD_BCRYPT, ['cost' => 12]),
        ]);

        $this->log->activity('user.password', 'users', 'user', $id, 'Changement de mot de passe');

        return redirect()->to('/admin/profile')->with('success', 'Mot de passe modifié.');
    }
}
