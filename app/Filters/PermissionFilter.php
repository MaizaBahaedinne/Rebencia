<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session()->get('user_id');
        
        // Allow if user not logged in (auth filter will handle)
        if (!$userId) {
            return redirect()->to(base_url('admin/login'));
        }

        // Get active role
        $userModel = model('UserModel');
        $activeRole = $userModel->getActiveRole($userId);
        
        if (!$activeRole) {
            return redirect()->to(base_url('admin/dashboard'))
                ->with('error', 'Aucun rôle actif. Veuillez contacter l\'administrateur.');
        }

        // Super admin bypasses all checks
        if ($activeRole['level'] >= 100) {
            return;
        }

        // Get current controller and method
        $router = service('router');
        $controller = $router->controllerName();
        $method = $router->methodName();
        
        // Extract module name from controller
        // Example: App\Controllers\Admin\Properties -> properties
        $parts = explode('\\', $controller);
        $moduleName = strtolower(end($parts));
        
        // Map methods to permission actions
        $actionMap = [
            'index' => 'can_read',
            'view' => 'can_read',
            'show' => 'can_read',
            'create' => 'can_create',
            'store' => 'can_create',
            'edit' => 'can_update',
            'update' => 'can_update',
            'delete' => 'can_delete',
            'destroy' => 'can_delete',
        ];
        
        $requiredAction = $actionMap[$method] ?? 'can_read';
        
        // Check permission
        if (!$this->hasPermission($activeRole['role_id'], $moduleName, $requiredAction)) {
            return redirect()->back()
                ->with('error', 'Accès refusé. Vous n\'avez pas les permissions nécessaires pour cette action.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }

    /**
     * Check if role has specific permission
     */
    private function hasPermission($roleId, $module, $action)
    {
        $db = \Config\Database::connect();
        
        // Get permission for this module
        $permission = $db->table('permissions')
            ->where('module', $module)
            ->get()
            ->getRowArray();
        
        if (!$permission) {
            // No permission defined = allow (for backward compatibility)
            return true;
        }
        
        // Check role_permissions
        $rolePermission = $db->table('role_permissions')
            ->where('role_id', $roleId)
            ->where('permission_id', $permission['id'])
            ->get()
            ->getRowArray();
        
        if (!$rolePermission) {
            return false;
        }
        
        // Check specific action
        return $rolePermission[$action] == 1;
    }
}
