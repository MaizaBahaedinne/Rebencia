<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Menus extends BaseController
{
    protected $menuModel;
    protected $roleModel;
    protected $roleMenuModel;

    public function __construct()
    {
        $this->menuModel = model('MenuModel');
        $this->roleModel = model('RoleModel');
        $this->roleMenuModel = model('RoleMenuModel');
    }

    /**
     * List all menus
     */
    public function index()
    {
        $data = [
            'title' => 'Gestion des Menus',
            'menus' => $this->menuModel->getMenuHierarchy()
        ];

        return view('admin/menus/index', $data);
    }

    /**
     * Create new menu item
     */
    public function create()
    {
        $data = [
            'title' => 'Nouveau Menu',
            'parentMenus' => $this->menuModel->getParentMenus()
        ];

        return view('admin/menus/create', $data);
    }

    /**
     * Store new menu
     */
    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'title' => 'required|min_length[3]|max_length[100]',
            'icon' => 'permit_empty|max_length[50]',
            'url' => 'permit_empty|max_length[255]',
            'parent_id' => 'permit_empty|is_natural',
            'order' => 'required|is_natural'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'icon' => $this->request->getPost('icon'),
            'url' => $this->request->getPost('url'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'order' => $this->request->getPost('order'),
            'is_active' => 1
        ];

        if ($this->menuModel->insert($data)) {
            return redirect()->to('/admin/menus')->with('success', 'Menu créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du menu');
    }

    /**
     * Edit menu
     */
    public function edit($id)
    {
        $menu = $this->menuModel->find($id);

        if (!$menu) {
            return redirect()->to('/admin/menus')->with('error', 'Menu non trouvé');
        }

        $data = [
            'title' => 'Modifier Menu',
            'menu' => $menu,
            'parentMenus' => $this->menuModel->getParentMenus()
        ];

        return view('admin/menus/edit', $data);
    }

    /**
     * Update menu
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();

        $rules = [
            'title' => 'required|min_length[3]|max_length[100]',
            'icon' => 'permit_empty|max_length[50]',
            'url' => 'permit_empty|max_length[255]',
            'parent_id' => 'permit_empty|is_natural',
            'order' => 'required|is_natural'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'icon' => $this->request->getPost('icon'),
            'url' => $this->request->getPost('url'),
            'parent_id' => $this->request->getPost('parent_id') ?: null,
            'order' => $this->request->getPost('order'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->menuModel->update($id, $data)) {
            return redirect()->to('/admin/menus')->with('success', 'Menu mis à jour avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la mise à jour du menu');
    }

    /**
     * Delete menu
     */
    public function delete($id)
    {
        // Check if menu has children
        $children = $this->menuModel->getSubMenus($id);
        
        if (!empty($children)) {
            return redirect()->to('/admin/menus')->with('error', 'Impossible de supprimer un menu ayant des sous-menus');
        }

        if ($this->menuModel->delete($id)) {
            return redirect()->to('/admin/menus')->with('success', 'Menu supprimé avec succès');
        }

        return redirect()->to('/admin/menus')->with('error', 'Erreur lors de la suppression du menu');
    }

    /**
     * Manage role-specific menus
     */
    public function roleMenus($roleId = null)
    {
        try {
            $roles = $this->roleModel->findAll();
            
            if (!$roleId && !empty($roles)) {
                $roleId = $roles[0]['id'];
            }

            $role = $this->roleModel->find($roleId);
            
            if (!$role) {
                return redirect()->to('/admin/menus')->with('error', 'Rôle non trouvé');
            }

            // Get all available menus
            $allMenus = $this->menuModel->getMenuHierarchy();
            
            // Get menus assigned to this role
            $roleMenus = $this->roleMenuModel->getRoleMenus($roleId);
            $assignedMenuIds = array_column($roleMenus, 'menu_id');

            $data = [
                'title' => 'Gestion des Menus par Rôle',
                'roles' => $roles,
                'currentRole' => $role,
                'allMenus' => $allMenus,
                'assignedMenuIds' => $assignedMenuIds,
                'roleMenus' => $roleMenus
            ];

            return view('admin/menus/role_menus', $data);
        } catch (\Exception $e) {
            return redirect()->to('/admin/menus')->with('error', 'Erreur: Les tables menus n\'existent pas encore. Veuillez exécuter le fichier menus_tables.sql via phpMyAdmin. Détails: ' . $e->getMessage());
        }
    }

    /**
     * Update role menus (AJAX)
     */
    public function updateRoleMenus()
    {
        $roleId = $this->request->getPost('role_id');
        $menusJson = $this->request->getPost('menus');
        
        // Decode JSON string
        $menus = json_decode($menusJson, true);

        if (!$roleId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Données invalides: role_id manquant'
            ]);
        }
        
        if (!is_array($menus)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Données invalides: aucun menu à assigner (menus doit être un tableau)'
            ]);
        }

        try {
            $this->roleMenuModel->bulkUpdateRoleMenus($roleId, $menus);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Menus mis à jour avec succès'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ]);
        }
    }
}
