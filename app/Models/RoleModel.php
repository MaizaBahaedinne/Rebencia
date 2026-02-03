<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name', 'display_name', 'description', 'level'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|is_unique[roles.name,id,{id}]',
        'display_name' => 'required|max_length[150]',
    ];

    public function getRoleWithPermissions($roleId)
    {
        $role = $this->find($roleId);
        
        if ($role) {
            $db = \Config\Database::connect();
            $permissions = $db->table('role_permissions')
                ->select('permissions.*, role_permissions.can_create, role_permissions.can_read, role_permissions.can_update, role_permissions.can_delete, role_permissions.can_validate')
                ->join('permissions', 'permissions.id = role_permissions.permission_id')
                ->where('role_permissions.role_id', $roleId)
                ->get()
                ->getResultArray();
            
            $role['permissions'] = $permissions;
        }
        
        return $role;
    }
}
