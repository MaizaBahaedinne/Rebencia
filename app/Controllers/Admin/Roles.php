<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Roles extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $db;

    public function __construct()
    {
        $this->roleModel = model('RoleModel');
        $this->permissionModel = model('PermissionModel');
        $this->db = \Config\Database::connect();
    }

    /**
     * Liste des rôles
     */
    public function index()
    {
        // Get all roles with user counts and permission counts
        $roles = $this->db->query("
            SELECT r.*, 
                   COUNT(DISTINCT u.id) as user_count,
                   COUNT(DISTINCT rp.permission_id) as permission_count
            FROM roles r
            LEFT JOIN users u ON u.role_id = r.id
            LEFT JOIN role_permissions rp ON rp.role_id = r.id
            GROUP BY r.id
            ORDER BY r.level DESC
        ")->getResultArray();

        $data = [
            'title' => 'Gestion des Rôles',
            'page_title' => 'Gestion des Rôles',
            'roles' => $roles,
            'totalPermissions' => $this->permissionModel->countAllResults(),
            'totalUsers' => $this->db->table('users')->countAllResults()
        ];

        return view('admin/roles/index', $data);
    }

    /**
     * Créer un nouveau rôle
     */
    public function create()
    {
        $data = [
            'title' => 'Nouveau Rôle',
            'modules' => $this->getAvailableModules()
        ];

        return view('admin/roles/create', $data);
    }

    /**
     * Enregistrer un nouveau rôle
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|is_unique[roles.name]|alpha_dash',
            'display_name' => 'required|min_length[3]',
            'level' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $roleData = [
            'name' => $this->request->getPost('name'),
            'display_name' => $this->request->getPost('display_name'),
            'description' => $this->request->getPost('description'),
            'level' => $this->request->getPost('level'),
        ];

        $roleId = $this->roleModel->insert($roleData);

        if ($roleId) {
            // Enregistrer les permissions
            $this->saveRolePermissions($roleId, $this->request->getPost('permissions'));
            
            return redirect()->to(base_url('admin/roles'))->with('success', 'Rôle créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du rôle');
    }

    /**
     * Modifier un rôle
     */
    public function edit($id)
    {
        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rôle introuvable');
        }

        // Récupérer les permissions du rôle
        $rolePermissions = $this->db->table('role_permissions')
            ->where('role_id', $id)
            ->get()
            ->getResultArray();

        // Organiser les permissions par permission_id
        $permissionsMap = [];
        foreach ($rolePermissions as $perm) {
            $permissionsMap[$perm['permission_id']] = $perm;
        }

        $data = [
            'title' => 'Modifier Rôle',
            'role' => $role,
            'modules' => $this->getAvailableModules(),
            'rolePermissions' => $permissionsMap
        ];

        return view('admin/roles/edit', $data);
    }

    /**
     * Mettre à jour un rôle
     */
    public function update($id)
    {
        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rôle introuvable');
        }

        $validation = \Config\Services::validation();
        
        $rules = [
            'name' => 'required|alpha_dash|is_unique[roles.name,id,' . $id . ']',
            'display_name' => 'required|min_length[3]',
            'level' => 'required|integer',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $roleData = [
            'name' => $this->request->getPost('name'),
            'display_name' => $this->request->getPost('display_name'),
            'description' => $this->request->getPost('description'),
            'level' => $this->request->getPost('level'),
        ];

        $this->roleModel->update($id, $roleData);

        // Supprimer les anciennes permissions
        $this->db->table('role_permissions')->where('role_id', $id)->delete();

        // Enregistrer les nouvelles permissions
        $this->saveRolePermissions($id, $this->request->getPost('permissions'));

        return redirect()->to(base_url('admin/roles'))->with('success', 'Rôle modifié avec succès');
    }

    /**
     * Supprimer un rôle
     */
    public function delete($id)
    {
        $role = $this->roleModel->find($id);
        
        if (!$role) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Rôle introuvable');
        }

        // Vérifier si des utilisateurs ont ce rôle
        $userModel = model('UserModel');
        $usersCount = $userModel->where('role_id', $id)->countAllResults();
        
        if ($usersCount > 0) {
            return redirect()->to(base_url('admin/roles'))->with('error', 'Impossible de supprimer: des utilisateurs ont ce rôle');
        }

        // Supprimer les permissions associées
        $this->db->table('role_permissions')->where('role_id', $id)->delete();

        $this->roleModel->delete($id);

        return redirect()->to(base_url('admin/roles'))->with('success', 'Rôle supprimé avec succès');
    }

    /**
     * Matrice des permissions (vue principale)
     */
    public function matrix()
    {
        $roles = $this->roleModel->orderBy('level', 'ASC')->findAll();
        $modules = $this->getAvailableModules();

        // Récupérer toutes les permissions par rôle
        $permissionsMatrix = [];
        foreach ($roles as $role) {
            $rolePerms = $this->db->table('role_permissions')
                ->where('role_id', $role['id'])
                ->get()
                ->getResultArray();
            
            foreach ($rolePerms as $perm) {
                $permissionsMatrix[$role['id']][$perm['permission_id']] = $perm;
            }
        }

        $data = [
            'title' => 'Matrice des Permissions',
            'roles' => $roles,
            'modules' => $modules,
            'permissionsMatrix' => $permissionsMatrix
        ];

        return view('admin/roles/matrix', $data);
    }

    /**
     * Synchroniser les permissions avec les contrôleurs
     */
    public function syncPermissions()
    {
        $modules = $this->scanControllers();
        $synced = 0;

        foreach ($modules as $moduleName => $actions) {
            foreach ($actions as $action) {
                $permissionName = strtolower($moduleName) . '_' . $action;
                $description = ucfirst($action) . ' ' . ucfirst($moduleName);

                // Vérifier si la permission existe déjà
                $existing = $this->permissionModel->where('name', $permissionName)->first();
                
                if (!$existing) {
                    $this->permissionModel->insert([
                        'name' => $permissionName,
                        'description' => $description,
                        'module' => strtolower($moduleName)
                    ]);
                    $synced++;
                }
            }
        }

        return redirect()->back()->with('success', "$synced nouvelle(s) permission(s) synchronisée(s)");
    }

    /**
     * Enregistrer les permissions d'un rôle
     */
    private function saveRolePermissions($roleId, $permissions)
    {
        if (!$permissions || !is_array($permissions)) {
            return;
        }

        foreach ($permissions as $permId => $actions) {
            $data = [
                'role_id' => $roleId,
                'permission_id' => $permId,
                'can_create' => isset($actions['create']) ? 1 : 0,
                'can_read' => isset($actions['read']) ? 1 : 0,
                'can_update' => isset($actions['update']) ? 1 : 0,
                'can_delete' => isset($actions['delete']) ? 1 : 0,
                'can_validate' => isset($actions['validate']) ? 1 : 0,
            ];

            $this->db->table('role_permissions')->insert($data);
        }
    }

    /**
     * Scanner les contrôleurs pour détecter les modules
     */
    private function scanControllers()
    {
        $controllerPath = APPPATH . 'Controllers/Admin/';
        $modules = [];

        if (!is_dir($controllerPath)) {
            return $modules;
        }

        $files = scandir($controllerPath);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || $file === 'Auth.php' || $file === 'Dashboard.php') {
                continue;
            }

            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $moduleName = pathinfo($file, PATHINFO_FILENAME);
                $modules[$moduleName] = ['view', 'create', 'update', 'delete', 'export'];
            }
        }

        return $modules;
    }

    /**
     * Obtenir les modules disponibles avec leurs permissions
     */
    private function getAvailableModules()
    {
        $permissions = $this->permissionModel->orderBy('module', 'ASC')->orderBy('name', 'ASC')->findAll();
        
        $modules = [];
        foreach ($permissions as $permission) {
            $module = $permission['module'] ?? 'other';
            $modules[$module][] = $permission;
        }

        return $modules;
    }
}
