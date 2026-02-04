<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username', 'email', 'password_hash', 'first_name', 'last_name',
        'phone', 'avatar', 'role_id', 'agency_id', 'manager_id',
        'status', 'last_login', 'email_verified'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'email_verified' => 'boolean',
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[100]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password_hash' => 'required|min_length[8]',
        'role_id' => 'required|integer',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $afterInsert = [];
    protected $beforeUpdate = ['hashPassword'];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password_hash'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password_hash'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getUserWithRole($id)
    {
        return $this->select('users.*, roles.name as role_name, roles.display_name as role_display_name')
            ->join('roles', 'roles.id = users.role_id')
            ->where('users.id', $id)
            ->first();
    }

    /**
     * Get user with all their roles
     */
    public function getUserWithRoles($userId)
    {
        $db = \Config\Database::connect();
        
        $user = $this->find($userId);
        if (!$user) {
            return null;
        }

        // Get all roles for this user
        $userRoles = $db->table('user_roles')
            ->select('user_roles.*, roles.name, roles.display_name, roles.level')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->get()
            ->getResultArray();

        $user['roles'] = $userRoles;
        
        // Get active role
        $activeRole = null;
        foreach ($userRoles as $role) {
            if ($role['is_active'] == 1) {
                $activeRole = $role;
                break;
            }
        }
        
        $user['active_role'] = $activeRole;
        
        return $user;
    }

    /**
     * Get active role for user
     */
    public function getActiveRole($userId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('user_roles')
            ->select('user_roles.*, roles.name, roles.display_name, roles.level')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.is_active', 1)
            ->get()
            ->getRowArray();
    }

    /**
     * Get default role for user
     */
    public function getDefaultRole($userId)
    {
        $db = \Config\Database::connect();
        
        return $db->table('user_roles')
            ->select('user_roles.*, roles.name, roles.display_name, roles.level')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('user_roles.is_default', 1)
            ->get()
            ->getRowArray();
    }

    /**
     * Set default role for user
     */
    public function setDefaultRole($userId, $roleId)
    {
        $db = \Config\Database::connect();
        
        // Remove is_default from all roles
        $db->table('user_roles')
            ->where('user_id', $userId)
            ->update(['is_default' => 0]);
        
        // Set new default role
        $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->update(['is_default' => 1]);
        
        return true;
    }

    /**
     * Switch active role for user
     */
    public function switchRole($userId, $roleId)
    {
        $db = \Config\Database::connect();
        
        // Deactivate all roles
        $db->table('user_roles')
            ->where('user_id', $userId)
            ->update(['is_active' => 0]);
        
        // Activate selected role
        $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->update(['is_active' => 1]);
        
        return true;
    }

    /**
     * Assign role to user
     */
    public function assignRole($userId, $roleId, $setActive = false, $setDefault = false)
    {
        $db = \Config\Database::connect();
        
        // Check if role already assigned
        $existing = $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->get()
            ->getRowArray();
        
        if ($existing) {
            return false; // Already assigned
        }
        
        // Check if this is the first role
        $roleCount = $db->table('user_roles')
            ->where('user_id', $userId)
            ->countAllResults();
        
        $isFirstRole = ($roleCount == 0);
        
        // If this is the first role, make it default and active
        if ($isFirstRole) {
            $setDefault = true;
            $setActive = true;
        }
        
        // If setDefault is true, remove default from other roles
        if ($setDefault) {
            $db->table('user_roles')
                ->where('user_id', $userId)
                ->update(['is_default' => 0]);
        }
        
        // If setActive is true, deactivate others
        if ($setActive) {
            $db->table('user_roles')
                ->where('user_id', $userId)
                ->update(['is_active' => 0]);
        }
        
        // Insert new role
        $db->table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $roleId,
            'is_default' => $setDefault ? 1 : 0,
            'is_active' => $setActive ? 1 : 0,
            'assigned_at' => date('Y-m-d H:i:s'),
            'assigned_by' => session()->get('user_id'),
        ]);
        
        return true;
    }

    /**
     * Remove role from user
     */
    public function removeRole($userId, $roleId)
    {
        $db = \Config\Database::connect();
        
        $db->table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->delete();
        
        return true;
    }

    public function getUsersByAgency($agencyId)
    {
        return $this->where('agency_id', $agencyId)
            ->where('status', 'active')
            ->findAll();
    }

    public function getUsersByManager($managerId)
    {
        return $this->where('manager_id', $managerId)
            ->where('status', 'active')
            ->findAll();
    }
}
