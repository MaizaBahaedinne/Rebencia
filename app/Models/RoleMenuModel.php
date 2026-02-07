<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleMenuModel extends Model
{
    protected $table = 'role_menus';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'menu_id', 'role_id', 'order', 'is_visible'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;

    /**
     * Assign menu to role
     */
    public function assignMenuToRole($menuId, $roleId, $order = 0, $isVisible = 1)
    {
        // Check if already exists
        $existing = $this->where('menu_id', $menuId)
            ->where('role_id', $roleId)
            ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'order' => $order,
                'is_visible' => $isVisible
            ]);
        }

        return $this->insert([
            'menu_id' => $menuId,
            'role_id' => $roleId,
            'order' => $order,
            'is_visible' => $isVisible
        ]);
    }

    /**
     * Remove menu from role
     */
    public function removeMenuFromRole($menuId, $roleId)
    {
        return $this->where('menu_id', $menuId)
            ->where('role_id', $roleId)
            ->delete();
    }

    /**
     * Update menu order for role
     */
    public function updateMenuOrder($roleId, $menuOrders)
    {
        foreach ($menuOrders as $menuId => $order) {
            $this->where('menu_id', $menuId)
                ->where('role_id', $roleId)
                ->set(['order' => $order])
                ->update();
        }
        return true;
    }

    /**
     * Get all menus for a role with visibility status
     */
    public function getRoleMenus($roleId)
    {
        return $this->where('role_id', $roleId)
            ->orderBy('order', 'ASC')
            ->findAll();
    }

    /**
     * Bulk update role menus
     */
    public function bulkUpdateRoleMenus($roleId, $menus)
    {
        // Delete all existing menus for this role
        $this->where('role_id', $roleId)->delete();

        // Insert new menu assignments
        foreach ($menus as $index => $menu) {
            $this->insert([
                'menu_id' => $menu['id'],
                'role_id' => $roleId,
                'order' => $index,
                'is_visible' => $menu['visible'] ?? 1
            ]);
        }

        return true;
    }
}
