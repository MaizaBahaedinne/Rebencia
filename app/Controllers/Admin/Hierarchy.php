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
            'usersWithoutManager' => $this->hierarchyHelper->getUsersWithoutManager(),
            'usersWithoutAgency' => $this->hierarchyHelper->getUsersWithoutAgency()
        ];
        
        return view('admin/hierarchy/index', $data);
    }
    
    /**
     * Assign manager to user
     */
    public function assignManager($userId = null)
    {
        if ($this->request->getMethod() === 'post') {
            $userId = $this->request->getPost('user_id');
            $managerId = $this->request->getPost('manager_id');
            
            // Vérifier que le manager n'est pas un subordonné de l'utilisateur
            $subordinates = $this->hierarchyHelper->getAllSubordinates($userId);
            if (in_array($managerId, $subordinates)) {
                return redirect()->back()->with('error', 'Le manager ne peut pas être un subordonné de l\'utilisateur');
            }
            
            // Mettre à jour
            $this->userModel->update($userId, ['manager_id' => $managerId]);
            
            return redirect()->to('/admin/hierarchy/view-user/' . $userId)->with('success', 'Manager assigné avec succès');
        }
        
        // Si userId fourni, récupérer l'utilisateur
        $selectedUser = null;
        if ($userId) {
            $selectedUser = $this->userModel->find($userId);
            if (!$selectedUser) {
                return redirect()->to('/admin/hierarchy')->with('error', 'Utilisateur non trouvé');
            }
        }
        
        $data = [
            'title' => 'Assigner un manager',
            'selectedUser' => $selectedUser,
            'users' => $this->userModel->findAll(),
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
        
        // Récupérer le manager
        $manager = null;
        if ($user['manager_id']) {
            $manager = $this->userModel->find($user['manager_id']);
        }
        
        // Récupérer les subordonnés directs
        $subordinates = $this->userModel->where('manager_id', $userId)->findAll();
        
        // Récupérer tous les subordonnés (récursif)
        $allSubordinates = $this->hierarchyHelper->getAllSubordinates($userId);
        
        // Récupérer les biens gérés par cet utilisateur
        $propertyModel = new \App\Models\PropertyModel();
        $properties = $propertyModel->where('agent_id', $userId)->findAll();
        
        $data = [
            'title' => 'Hiérarchie de ' . $user['first_name'] . ' ' . $user['last_name'],
            'user' => $user,
            'manager' => $manager,
            'subordinates' => $subordinates,
            'totalSubordinatesCount' => count($allSubordinates),
            'properties' => $properties
        ];
        
        return view('admin/hierarchy/view_user', $data);
    }
}
