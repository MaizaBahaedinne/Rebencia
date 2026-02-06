<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\HierarchyHelper;

class Hierarchy extends BaseController
{
    protected $hierarchyHelper;
    protected $userModel;
    protected $entityModel;
    
    public function __construct()
    {
        $this->hierarchyHelper = new HierarchyHelper();
        $this->userModel = new \App\Models\UserModel();
        
        // EntityModel peut ne pas exister
        $db = \Config\Database::connect();
        if ($db->tableExists('entities')) {
            try {
                $this->entityModel = new \App\Models\EntityModel();
            } catch (\Exception $e) {
                $this->entityModel = null;
            }
        } else {
            $this->entityModel = null;
        }
    }
    
    /**
     * Display organization hierarchy tree
     */
    public function index()
    {
        $data = [
            'title' => 'Hiérarchie organisationnelle',
            'tree' => $this->hierarchyHelper->getHierarchyTree(),
            'usersWithoutManager' => $this->hierarchyHelper->getUsersWithoutManager()
        ];
        
        return view('admin/hierarchy/index', $data);
    }
    
    /**
     * Assign manager to user
     */
    public function assignManager()
    {
        if ($this->request->getMethod() === 'post') {
            $userId = $this->request->getPost('user_id');
            $managerId = $this->request->getPost('manager_id');
            
            // Vérifier que le manager n'est pas un subordonné de l'utilisateur
            $subordinates = $this->hierarchyHelper->getAllSubordinates($userId);
            if (in_array($managerId, $subordinates)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Le manager ne peut pas être un subordonné de l\'utilisateur'
                ]);
            }
            
            // Mettre à jour
            $this->userModel->update($userId, ['manager_id' => $managerId]);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Manager assigné avec succès'
            ]);
        }
        
        $data = [
            'title' => 'Assigner un manager',
            'users' => $this->userModel->where('role !=', 'admin')->findAll(),
            'managers' => $this->userModel->findAll()
        ];
        
        return view('admin/hierarchy/assign_manager', $data);
    }
    
    /**
     * View user hierarchy
     */
    public function viewUser($userId)
    {
        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->to('/admin/hierarchy')->with('error', 'Utilisateur non trouvé');
        }
        
        $data = [
            'title' => 'Hiérarchie de ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'hierarchyPath' => $this->hierarchyHelper->getUserHierarchyPath($userId),
            'subordinates' => $this->hierarchyHelper->getDirectSubordinates($userId)
        ];
        
        return view('admin/hierarchy/view_user', $data);
    }
}
