<?php

namespace App\Libraries;

use CodeIgniter\I18n\Time;

class HierarchyHelper
{
    protected $userModel;
    protected $entityModel;
    
    public function __construct()
    {
        $this->userModel = new \App\Models\UserModel();
        // EntityModel peut ne pas exister, on utilise une alternative
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
     * Get all subordinates of a user (direct and indirect)
     * @param int $userId
     * @return array Array of user IDs
     */
    public function getAllSubordinates($userId)
    {
        $subordinates = [];
        $directSubordinates = $this->getDirectSubordinates($userId);
        
        foreach ($directSubordinates as $subordinateId) {
            $subordinates[] = $subordinateId;
            // Récursif : ajouter les subordonnés des subordonnés
            $subordinates = array_merge($subordinates, $this->getAllSubordinates($subordinateId));
        }
        
        return array_unique($subordinates);
    }
    
    /**
     * Get direct subordinates of a user
     * @param int $userId
     * @return array Array of user IDs
     */
    public function getDirectSubordinates($userId)
    {
        $users = $this->userModel->where('manager_id', $userId)->findAll();
        return array_column($users, 'id');
    }
    
    /**
     * Get all entities under a given entity (recursive)
     * @param int $entityId
     * @return array Array of entity IDs
     */
    public function getAllSubEntities($entityId)
    {
        if (!$this->entityModel) {
            return [];
        }
        
        $subEntities = [];
        $directSubEntities = $this->getDirectSubEntities($entityId);
        
        foreach ($directSubEntities as $subEntityId) {
            $subEntities[] = $subEntityId;
            // Récursif
            $subEntities = array_merge($subEntities, $this->getAllSubEntities($subEntityId));
        }
        
        return array_unique($subEntities);
    }
    
    /**
     * Get direct sub-entities of an entity
     * @param int $entityId
     * @return array Array of entity IDs
     */
    public function getDirectSubEntities($entityId)
    {
        if (!$this->entityModel) {
            return [];
        }
        
        $entities = $this->entityModel->where('parent_id', $entityId)->findAll();
        return array_column($entities, 'id');
    }
    
    /**
     * Check if user can manage another user's resources
     * @param int $managerId Manager user ID
     * @param int $userId Target user ID
     * @return bool
     */
    public function canManageUser($managerId, $userId)
    {
        // Admin peut tout gérer (role_id = 1)
        $manager = $this->userModel->find($managerId);
        if ($manager && $manager['role_id'] == 1) {
            return true;
        }
        
        // L'utilisateur peut gérer ses propres ressources
        if ($managerId === $userId) {
            return true;
        }
        
        // Vérifier si userId est dans la hiérarchie de managerId
        $subordinates = $this->getAllSubordinates($managerId);
        return in_array($userId, $subordinates);
    }
    
    /**
     * Check if user can manage resources in an entity
     * @param int $userId
     * @param int $entityId
     * @return bool
     */
    public function canManageEntity($userId, $entityId)
    {
        $user = $this->userModel->find($userId);
        
        // Admin peut tout gérer
        if ($user && $user['role'] === 'admin') {
            return true;
        }
        
        // Vérifier si l'entité est celle de l'utilisateur
        if ($user && $user['entity_id'] == $entityId) {
            return true;
        }
        
        // Vérifier si l'entité est une sous-entité
        if ($user && $user['entity_id']) {
            $subEntities = $this->getAllSubEntities($user['entity_id']);
            return in_array($entityId, $subEntities);
        }
        
        return false;
    }
    
    /**
     * Get organization hierarchy tree
     * @param int|null $parentId
     * @return array
     */
    public function getHierarchyTree($parentId = null)
    {
        // Si pas de table entities, on retourne une structure basée sur les agences
        if (!$this->entityModel) {
            return $this->getAgencyBasedTree();
        }
        
        $entities = $this->entityModel
            ->where('parent_id', $parentId)
            ->orderBy('name', 'ASC')
            ->findAll();
        
        $tree = [];
        foreach ($entities as $entity) {
            $users = $this->userModel
                ->where('entity_id', $entity['id'])
                ->orderBy('first_name', 'ASC')
                ->findAll();
            
            $tree[] = [
                'entity' => $entity,
                'users' => $users,
                'children' => $this->getHierarchyTree($entity['id'])
            ];
        }
        
        return $tree;
    }
    
    /**
     * Get tree based on agencies (fallback when no entities table)
     * @return array
     */
    protected function getAgencyBasedTree()
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists('agencies')) {
            return [];
        }
        
        $agencyModel = new \App\Models\AgencyModel();
        $agencies = $agencyModel->orderBy('name', 'ASC')->findAll();
        
        $tree = [];
        foreach ($agencies as $agency) {
            $users = $this->userModel
                ->where('agency_id', $agency['id'])
                ->orderBy('first_name', 'ASC')
                ->findAll();
            
            $tree[] = [
                'entity' => [
                    'id' => $agency['id'],
                    'name' => $agency['name'],
                    'type' => 'agency',
                    'parent_id' => null
                ],
                'users' => $users,
                'children' => []
            ];
        }
        
        return $tree;
    }
    
    /**
     * Get users without manager
     * @return array
     */
    public function getUsersWithoutManager()
    {
        // role_id = 1 est généralement admin/super-admin
        return $this->userModel
            ->where('manager_id IS NULL')
            ->where('role_id >', 1)
            ->findAll();
    }
    
    /**
     * Get user hierarchy path (from user to top)
     * @param int $userId
     * @return array Array of user data
     */
    public function getUserHierarchyPath($userId)
    {
        $path = [];
        $currentUser = $this->userModel->find($userId);
        
        while ($currentUser) {
            $path[] = $currentUser;
            if ($currentUser['manager_id']) {
                $currentUser = $this->userModel->find($currentUser['manager_id']);
            } else {
                break;
            }
        }
        
        return $path;
    }
    
    /**
     * Get accessible user IDs for a manager (including self)
     * @param int $userId
     * @return array
     */
    public function getAccessibleUserIds($userId)
    {
        $user = $this->userModel->find($userId);
        
        // Admin voit tout (role_id = 1)
        if ($user && $user['role_id'] == 1) {
            $allUsers = $this->userModel->findAll();
            return array_column($allUsers, 'id');
        }
        
        // Récupérer l'utilisateur + tous ses subordonnés
        $accessibleIds = [$userId];
        $subordinates = $this->getAllSubordinates($userId);
        
        return array_merge($accessibleIds, $subordinates);
    }
}
