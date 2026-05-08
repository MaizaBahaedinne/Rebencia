<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * AuthFilter – Vérifie que l'utilisateur est connecté.
 * Redirige vers /login si la session est absente.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session()->get('logged_in')) {
            session()->set('redirect_url', current_url());
            return redirect()->to('/login')->with('error', 'Veuillez vous connecter pour accéder à cette page.');
        }

        // Vérifier le statut du compte
        if (session()->get('user_status') !== 'active') {
            session()->destroy();
            return redirect()->to('/login')->with('error', 'Votre compte est suspendu ou en attente de validation.');
        }

        // Rafraîchir les données critiques depuis la DB (permissions, niveau, agence)
        // → les changements de rôle/agence s'appliquent sans reconnexion
        $this->refreshSession();
    }

    /**
     * Recharge depuis la DB : permissions, hierarchy_level, agency_id, organization_id.
     */
    private function refreshSession(): void
    {
        $userId = (int) session()->get('user_id');
        if (! $userId) {
            return;
        }

        try {
            $db = \Config\Database::connect();

            // Permissions fraîches
            $permissions = $db->query(
                'SELECT p.name FROM permissions p
                 JOIN role_permissions rp ON rp.permission_id = p.id
                 JOIN users u ON u.role_id = rp.role_id
                 WHERE u.id = ?',
                [$userId]
            )->getResultArray();
            $permNames = array_column($permissions, 'name');

            // Données utilisateur + rôle
            $row = $db->query(
                'SELECT u.role_id, u.status, u.agency_id, u.organization_id,
                        r.hierarchy_level, r.name AS role_name, COALESCE(r.label, r.name) AS role_label
                 FROM users u
                 LEFT JOIN roles r ON r.id = u.role_id
                 WHERE u.id = ? LIMIT 1',
                [$userId]
            )->getRowArray();

            if (! $row) {
                return;
            }

            // Statut désactivé pendant la session ?
            if ($row['status'] !== 'active') {
                session()->destroy();
                return;
            }

            $agencyName = null;
            if (! empty($row['agency_id'])) {
                $ag = $db->query('SELECT name FROM agencies WHERE id = ? LIMIT 1', [(int) $row['agency_id']])->getRowArray();
                $agencyName = $ag['name'] ?? null;
            }

            session()->set([
                'user_status'     => $row['status'],
                'user_role'       => $row['role_name'],
                'user_role_label' => $row['role_label'],
                'user_role_id'    => $row['role_id'],
                'permissions'     => $permNames,
                'agency_id'       => $row['agency_id'] ? (int) $row['agency_id'] : null,
                'agency_name'     => $agencyName,
                'organization_id' => $row['organization_id'] ? (int) $row['organization_id'] : null,
                'hierarchy_level' => (int) ($row['hierarchy_level'] ?? 5),
            ]);
        } catch (\Throwable $e) {
            // En cas d'erreur DB on garde les données de session existantes
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après
    }
}
