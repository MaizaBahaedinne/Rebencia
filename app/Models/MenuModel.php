<?php

namespace App\Models;

use CodeIgniter\Model;

class MenuModel extends Model
{
    protected $table = 'menus';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'title', 'icon', 'url', 'parent_id', 'order', 'is_active'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get menu items for a specific role
     */
    public function getMenusForRole($roleId)
    {
        return $this->db->table('menus')
            ->select('menus.*, role_menus.order as role_order, role_menus.is_visible')
            ->join('role_menus', 'role_menus.menu_id = menus.id', 'left')
            ->where('role_menus.role_id', $roleId)
            ->where('role_menus.is_visible', 1)
            ->where('menus.is_active', 1)
            ->orderBy('role_menus.order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get all menus in hierarchical structure
     */
    public function getMenuHierarchy()
    {
        $menus = $this->where('is_active', 1)
            ->orderBy('order', 'ASC')
            ->findAll();

        return $this->buildTree($menus);
    }

    /**
     * Build tree structure from flat array
     */
    private function buildTree($menus, $parentId = null)
    {
        $branch = [];
        foreach ($menus as $menu) {
            if ($menu['parent_id'] == $parentId) {
                $children = $this->buildTree($menus, $menu['id']);
                if ($children) {
                    $menu['children'] = $children;
                }
                $branch[] = $menu;
            }
        }
        return $branch;
    }

    /**
     * Get all parent menus (top-level)
     */
    public function getParentMenus()
    {
        return $this->where('parent_id', null)
            ->where('is_active', 1)
            ->orderBy('order', 'ASC')
            ->findAll();
    }

    /**
     * Get submenus for a parent
     */
    public function getSubMenus($parentId)
    {
        return $this->where('parent_id', $parentId)
            ->where('is_active', 1)
            ->orderBy('order', 'ASC')
            ->findAll();
    }
}
