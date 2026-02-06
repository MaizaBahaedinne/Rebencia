<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filtre de permissions RBAC pour Property Extended
 * 
 * Vérifie les permissions avant d'accéder aux features étendues
 */
class PropertyExtendedPermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $userId = $session->get('user_id');
        
        if (!$userId) {
            return redirect()->to('/admin/login');
        }
        
        // Récupérer le rôle actif
        $activeRole = $session->get('active_role');
        
        if (!$activeRole) {
            return redirect()->to('/admin/dashboard')->with('error', 'Aucun rôle actif');
        }
        
        // Vérifier permissions selon l'action
        $uri = $request->getUri()->getPath();
        
        // Permissions requises selon l'URL
        $permissionsMap = [
            'properties/config' => 'property.configure',
            'rooms/save' => 'property.edit_extended',
            'options/save' => 'property.edit_extended',
            'location/save' => 'property.edit_extended',
            'financial/save' => 'property.edit_financial',
            'costs/save' => 'property.edit_financial',
            'analysis' => 'property.view_analysis',
            'financial-report' => 'property.view_analysis',
            'portfolio' => 'property.view_analysis'
        ];
        
        foreach ($permissionsMap as $pattern => $permission) {
            if (strpos($uri, $pattern) !== false) {
                if (!$this->hasPermission($activeRole, $permission)) {
                    if ($request->isAJAX()) {
                        return service('response')->setJSON([
                            'success' => false,
                            'message' => 'Permission refusée'
                        ])->setStatusCode(403);
                    }
                    
                    return redirect()->back()->with('error', 'Vous n\'avez pas la permission d\'accéder à cette ressource');
                }
            }
        }
        
        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Pas de traitement après
    }
    
    /**
     * Vérifier si un rôle a une permission
     */
    private function hasPermission($roleId, $permissionCode): bool
    {
        $db = \Config\Database::connect();
        
        // Vérifier dans role_permissions
        $permission = $db->table('permissions')
                        ->where('code', $permissionCode)
                        ->get()
                        ->getRowArray();
        
        if (!$permission) {
            return false;
        }
        
        $rolePermission = $db->table('role_permissions')
                            ->where('role_id', $roleId)
                            ->where('permission_id', $permission['id'])
                            ->get()
                            ->getRowArray();
        
        return !empty($rolePermission);
    }
}
