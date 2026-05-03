<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $primaryKey    = 'id';
    protected $useTimestamps = true;
    protected $useSoftDeletes= true;
    protected $deletedField  = 'deleted_at';

    protected $allowedFields = [
        'role_id', 'agency_id', 'manager_id', 'first_name', 'last_name', 'email', 'phone',
        'password_hash', 'avatar', 'status',
        'last_login_at', 'last_login_ip', 'remember_token',
    ];

    protected $validationRules = [
        'email'     => 'required|valid_email',
        'first_name'=> 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'role_id'   => 'required|is_natural_no_zero',
    ];

    protected $hiddenFields = ['password_hash', 'remember_token'];

    // --------------------------------------------------------
    // Recherches
    // --------------------------------------------------------

    /**
     * Trouve un utilisateur par email (avec rôle joint).
     */
    public function findByEmail(string $email): ?array
    {
        return $this->db->table('users u')
            ->select('u.*, r.name AS role_name, COALESCE(r.label, r.name) AS role_label, COALESCE(r.color, \'#6c757d\') AS role_color')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.email', $email)
            ->where('u.deleted_at IS NULL')
            ->get()
            ->getRowArray();
    }

    /**
     * Retourne tous les utilisateurs avec leur rôle.
     */
    public function getWithRole(array $filters = []): array
    {
        $builder = $this->db->table('users u')
            ->select('u.id, u.first_name, u.last_name, u.email, u.phone,
                      u.status, u.avatar, u.last_login_at, u.created_at,
                      r.name AS role_name, COALESCE(r.label, r.name) AS role_label, COALESCE(r.color, \'#6c757d\') AS role_color')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.deleted_at IS NULL');

        // agency_id n'existe que si la migration add_agencies_module.sql a été appliquée
        try {
            $this->db->query('SELECT agency_id FROM users LIMIT 0');
            $builder->select('u.agency_id');
        } catch (\Throwable $e) {
            // colonne absente – migration non appliquée, on ignore silencieusement
        }

        if (! empty($filters['status'])) {
            $builder->where('u.status', $filters['status']);
        }
        if (! empty($filters['role_id'])) {
            $builder->where('u.role_id', $filters['role_id']);
        }
        if (! empty($filters['agency_id'])) {
            $builder->where('u.agency_id', $filters['agency_id']);
        }
        if (! empty($filters['organization_id'])) {
            $builder->where('u.organization_id', $filters['organization_id']);
        }
        if (! empty($filters['search'])) {
            $builder->groupStart()
                ->like('u.first_name', $filters['search'])
                ->orLike('u.last_name', $filters['search'])
                ->orLike('u.email', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('u.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Retourne un utilisateur avec rôle par ID.
     */
    public function findWithRole(int $id): ?array
    {
        return $this->db->table('users u')
            ->select('u.*, r.name AS role_name, COALESCE(r.label, r.name) AS role_label, COALESCE(r.color, \'#6c757d\') AS role_color')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.id', $id)
            ->where('u.deleted_at IS NULL')
            ->get()
            ->getRowArray();
    }

    /**
     * Retourne les permissions d'un utilisateur sous forme de tableau de noms.
     */
    public function getPermissions(int $userId): array
    {
        $user = $this->find($userId);
        if (! $user) {
            return [];
        }

        $rows = $this->db->table('permissions p')
            ->select('p.name')
            ->join('role_permissions rp', 'rp.permission_id = p.id')
            ->where('rp.role_id', $user['role_id'])
            ->get()
            ->getResultArray();

        return array_column($rows, 'name');
    }

    /**
     * Retourne tous les rôles assignés à un utilisateur (table user_roles).
     */
    public function getUserRoles(int $userId): array
    {
        return $this->db->table('user_roles ur')
            ->select('ur.role_id, r.name, COALESCE(r.label, r.name) AS label, COALESCE(r.color, \'#6c757d\') AS color')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('ur.user_id', $userId)
            ->orderBy('r.name')
            ->get()->getResultArray();
    }

    /**
     * Synchronise la table user_roles pour un utilisateur.
     * Met aussi à jour role_id (rôle principal = premier de la liste).
     */
    public function syncRoles(int $userId, array $roleIds): void
    {
        $roleIds = array_values(array_unique(array_filter(array_map('intval', $roleIds))));
        if (empty($roleIds)) {
            return;
        }

        $this->db->table('user_roles')->where('user_id', $userId)->delete();
        foreach ($roleIds as $rid) {
            $this->db->table('user_roles')->insert([
                'user_id' => $userId,
                'role_id' => $rid,
            ]);
        }

        // Le premier rôle sélectionné devient le rôle principal
        $this->db->table('users')->where('id', $userId)->update(['role_id' => $roleIds[0]]);
    }

    /**
     * Statistiques globales pour le dashboard.
     */
    public function getStats(): array
    {
        $total   = $this->where('deleted_at IS NULL')->countAllResults(false);
        $active  = $this->where('status', 'active')->where('deleted_at IS NULL')->countAllResults(false);
        $pending = $this->where('status', 'pending')->where('deleted_at IS NULL')->countAllResults(false);

        return compact('total', 'active', 'pending');
    }
}
