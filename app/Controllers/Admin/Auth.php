<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->has('user_id')) {
            return redirect()->to('/admin/dashboard');
        }

        return view('admin/auth/login');
    }

    public function attemptLogin()
    {
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $userModel = model('UserModel');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect');
        }

        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email ou mot de passe incorrect');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Votre compte est désactivé');
        }

        // Récupérer le rôle par défaut ou actif
        $defaultRole = $userModel->getDefaultRole($user['id']);
        if (!$defaultRole) {
            // Si pas de rôle par défaut, chercher n'importe quel rôle
            $userWithRoles = $userModel->getUserWithRoles($user['id']);
            if (!empty($userWithRoles['roles'])) {
                $defaultRole = $userWithRoles['roles'][0];
                // Définir comme actif
                $userModel->switchRole($user['id'], $defaultRole['role_id']);
            }
        }

        // Construire le nom complet
        $fullName = trim($user['first_name'] . ' ' . $user['last_name']);
        if (empty($fullName)) {
            $fullName = $user['username'] ?? $user['email'];
        }

        // Récupérer le nom de l'agence
        $agencyName = null;
        if ($user['agency_id']) {
            $agencyModel = model('AgencyModel');
            $agency = $agencyModel->find($user['agency_id']);
            $agencyName = $agency['name'] ?? null;
        }

        // Set session
        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'user_name' => $fullName,
            'email' => $user['email'],
            'user_avatar' => $user['avatar'] ?? null,
            'role_id' => $defaultRole['role_id'] ?? $user['role_id'],
            'role_name' => $defaultRole['name'] ?? null,
            'role_display_name' => $defaultRole['display_name'] ?? 'Utilisateur',
            'role_level' => $defaultRole['level'] ?? 0,
            'agency_id' => $user['agency_id'],
            'agency_name' => $agencyName,
            'is_logged_in' => true
        ]);

        // Update last login
        $userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        return redirect()->to('/admin/dashboard')->with('success', 'Connexion réussie');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login')->with('success', 'Déconnexion réussie');
    }
}
