<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table         = 'roles';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = ['name', 'label', 'description', 'color', 'is_active'];

    // --------------------------------------------------------

    /**
     * Retourne tous les rôles actifs avec leurs permissions.
     */
    public function getAllWithPermissions(): array
    {
        $roles = $this->where('is_active', 1)->findAll();

        foreach ($roles as &$role) {
            $role['permissions'] = $this->db->table('permissions p')
                ->select('p.id, p.name, p.label, p.module')
                ->join('role_permissions rp', 'rp.permission_id = p.id')
                ->where('rp.role_id', $role['id'])
                ->get()
                ->getResultArray();
        }

        return $roles;
    }

    /**
     * Synchronise les permissions d'un rôle.
     * $permissionIds : tableau d'IDs à attribuer.
     */
    public function syncPermissions(int $roleId, array $permissionIds): bool
    {
        $this->db->transStart();

        // Nettoyage
        $this->db->table('role_permissions')->where('role_id', $roleId)->delete();

        // Ré-insertion
        if (! empty($permissionIds)) {
            $rows = [];
            foreach ($permissionIds as $pid) {
                $rows[] = [
                    'role_id'       => $roleId,
                    'permission_id' => (int) $pid,
                    'created_at'    => date('Y-m-d H:i:s'),
                ];
            }
            $this->db->table('role_permissions')->insertBatch($rows);
        }

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Statistiques adoption pour la matrice.
     * Retourne le nombre d'utilisateurs par rôle.
     */
    public function getAdoptionStats(): array
    {
        $rows = $this->db->table('roles r')
            ->select('r.id, r.name, COALESCE(r.label, r.name) AS label, COALESCE(r.color, \'#6c757d\') AS color, COUNT(u.id) AS user_count')
            ->join('users u', 'u.role_id = r.id AND u.deleted_at IS NULL AND u.status = "active"', 'left')
            ->groupBy('r.id')
            ->get()
            ->getResultArray();

        $total = array_sum(array_column($rows, 'user_count')) ?: 1;

        foreach ($rows as &$row) {
            $row['adoption_pct'] = round(($row['user_count'] / $total) * 100, 1);
        }

        return $rows;
    }
}
