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
        'role_id', 'first_name', 'last_name', 'email', 'phone',
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
            ->select('u.*, r.name AS role_name, r.label AS role_label, r.color AS role_color')
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
                      r.name AS role_name, r.label AS role_label, r.color AS role_color')
            ->join('roles r', 'r.id = u.role_id')
            ->where('u.deleted_at IS NULL');

        if (! empty($filters['status'])) {
            $builder->where('u.status', $filters['status']);
        }
        if (! empty($filters['role_id'])) {
            $builder->where('u.role_id', $filters['role_id']);
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
            ->select('u.*, r.name AS role_name, r.label AS role_label, r.color AS role_color')
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
