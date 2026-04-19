<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table         = 'permissions';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['name', 'label', 'module'];

    /**
     * Retourne les permissions groupées par module.
     */
    public function getAllGrouped(): array
    {
        $rows = $this->orderBy('module')->orderBy('name')->findAll();
        $grouped = [];

        foreach ($rows as $perm) {
            $grouped[$perm['module']][] = $perm;
        }

        return $grouped;
    }
}
