<?php

namespace App\Libraries;

use App\Models\UserModel;
use App\Models\ActivityLogModel;

/**
 * AuthLibrary – Gestion complète de l'authentification Rebencia.
 */
class AuthLibrary
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Tente de connecter un utilisateur.
     * Retourne true en cas de succès, message d'erreur sinon.
     */
    public function attempt(string $email, string $password): bool|string
    {
        $user = $this->userModel->findByEmail($email);

        if (! $user) {
            return 'Identifiants incorrects.';
        }

        if (! password_verify($password, $user['password_hash'])) {
            return 'Identifiants incorrects.';
        }

        if ($user['status'] !== 'active') {
            return 'Votre compte est ' . ($user['status'] === 'pending' ? 'en attente de validation.' : 'suspendu.');
        }

        $this->startSession($user);

        // Mise à jour du dernier login
        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => service('request')->getIPAddress(),
        ]);

        // Log d'activité
        $logLib = new LogLibrary();
        $logLib->activity('login', 'auth', 'user', $user['id'], 'Connexion réussie');

        return true;
    }

    /**
     * Initialise la session utilisateur avec ses permissions.
     */
    public function startSession(array $user): void
    {
        $permissions = $this->userModel->getPermissions($user['id']);

        session()->set([
            'logged_in'        => true,
            'user_id'          => $user['id'],
            'user_name'        => $user['first_name'] . ' ' . $user['last_name'],
            'user_email'       => $user['email'],
            'user_role'        => $user['role_name'],
            'user_role_label'  => $user['role_label'],
            'user_role_id'     => $user['role_id'],
            'user_avatar'      => $user['avatar'],
            'user_status'      => $user['status'],
            'permissions'      => $permissions,
            'agency_id'        => $user['agency_id'] ?? null,
            'agency_name'      => $user['agency_name'] ?? null,
        ]);
    }

    /**
     * Déconnecte l'utilisateur courant.
     */
    public function logout(): void
    {
        $userId = session()->get('user_id');

        if ($userId) {
            $logLib = new LogLibrary();
            $logLib->activity('logout', 'auth', 'user', $userId, 'Déconnexion');
        }

        session()->destroy();
    }

    /**
     * Retourne true si l'utilisateur possède la permission.
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = session()->get('permissions') ?? [];
        return in_array($permission, $permissions, true);
    }

    /**
     * Retourne true si l'utilisateur a le rôle spécifié.
     */
    public function hasRole(string $role): bool
    {
        return session()->get('user_role') === $role;
    }

    /**
     * Alias – vérifie si quelqu'un est connecté.
     */
    public function check(): bool
    {
        return (bool) session()->get('logged_in');
    }

    /**
     * Retourne l'ID de l'utilisateur connecté.
     */
    public function id(): int
    {
        return (int) session()->get('user_id');
    }
}
