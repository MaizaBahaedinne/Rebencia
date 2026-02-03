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

        // Set session
        session()->set([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'agency_id' => $user['agency_id'],
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
