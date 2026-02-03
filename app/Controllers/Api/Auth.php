<?php

namespace App\Controllers\Api;

class Auth extends ApiController
{
    protected $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = model('UserModel');
    }

    /**
     * POST /api/auth/login
     * Login and get JWT token
     */
    public function login()
    {
        $data = $this->request->getJSON(true);
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $this->fail([
                'success' => false,
                'message' => 'Email et mot de passe requis'
            ], 400);
        }

        // Find user
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            return $this->failUnauthorized('Identifiants invalides');
        }

        // Verify password
        if (!password_verify($password, $user['password'])) {
            return $this->failUnauthorized('Identifiants invalides');
        }

        // Check if active
        if ($user['status'] !== 'active') {
            return $this->fail([
                'success' => false,
                'message' => 'Compte inactif'
            ], 403);
        }

        // Generate JWT token
        helper('jwt');
        $token = generateJWT([
            'user_id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role']
        ]);

        // Update last login
        $this->userModel->update($user['id'], [
            'last_login' => date('Y-m-d H:i:s')
        ]);

        return $this->respond([
            'success' => true,
            'message' => 'Connexion réussie',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'role' => $user['role']
                ]
            ]
        ]);
    }

    /**
     * POST /api/auth/me
     * Get current user info
     */
    public function me()
    {
        $user = $this->authenticate();
        
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        $userData = $this->userModel->find($user->user_id);

        if (!$userData) {
            return $this->failNotFound('Utilisateur non trouvé');
        }

        unset($userData['password']);

        return $this->respond([
            'success' => true,
            'data' => $userData
        ]);
    }

    /**
     * POST /api/auth/refresh
     * Refresh JWT token
     */
    public function refresh()
    {
        $user = $this->authenticate();
        
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        helper('jwt');
        $token = generateJWT([
            'user_id' => $user->user_id,
            'email' => $user->email,
            'role' => $user->role
        ]);

        return $this->respond([
            'success' => true,
            'data' => [
                'token' => $token
            ]
        ]);
    }
}
