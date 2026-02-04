<?php

if (!function_exists('hasPermission')) {
    /**
     * Check if current user has permission for module and action
     * 
     * @param string $module Module name (properties, clients, etc.)
     * @param string $action Action name (can_create, can_read, can_update, can_delete, can_validate)
     * @return bool
     */
    function hasPermission($module, $action = 'can_read')
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return false;
        }

        // Get active role
        $userModel = model('UserModel');
        $activeRole = $userModel->getActiveRole($userId);
        
        if (!$activeRole) {
            return false;
        }

        // Super admin bypasses all checks
        if ($activeRole['level'] >= 100) {
            return true;
        }

        $db = \Config\Database::connect();
        
        // Get permission for this module
        $permission = $db->table('permissions')
            ->where('module', $module)
            ->get()
            ->getRowArray();
        
        if (!$permission) {
            return true; // No permission defined = allow
        }
        
        // Check role_permissions
        $rolePermission = $db->table('role_permissions')
            ->where('role_id', $activeRole['role_id'])
            ->where('permission_id', $permission['id'])
            ->get()
            ->getRowArray();
        
        if (!$rolePermission) {
            return false;
        }
        
        return $rolePermission[$action] == 1;
    }
}

if (!function_exists('canCreate')) {
    function canCreate($module) {
        return hasPermission($module, 'can_create');
    }
}

if (!function_exists('canRead')) {
    function canRead($module) {
        return hasPermission($module, 'can_read');
    }
}

if (!function_exists('canUpdate')) {
    function canUpdate($module) {
        return hasPermission($module, 'can_update');
    }
}

if (!function_exists('canDelete')) {
    function canDelete($module) {
        return hasPermission($module, 'can_delete');
    }
}

if (!function_exists('canValidate')) {
    function canValidate($module) {
        return hasPermission($module, 'can_validate');
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Check if current user is admin (level >= 80)
     */
    function isAdmin()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return false;
        }

        $userModel = model('UserModel');
        $activeRole = $userModel->getActiveRole($userId);
        
        return $activeRole && $activeRole['level'] >= 80;
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check if current user is super admin (level >= 100)
     */
    function isSuperAdmin()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return false;
        }

        $userModel = model('UserModel');
        $activeRole = $userModel->getActiveRole($userId);
        
        return $activeRole && $activeRole['level'] >= 100;
    }
}

if (!function_exists('getCurrentRole')) {
    /**
     * Get current user's active role
     */
    function getCurrentRole()
    {
        $userId = session()->get('user_id');
        
        if (!$userId) {
            return null;
        }

        $userModel = model('UserModel');
        return $userModel->getActiveRole($userId);
    }
}
