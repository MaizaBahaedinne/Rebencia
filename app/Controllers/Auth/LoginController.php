<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;

/**
 * LoginController – Authentification Rebencia.
 */
class LoginController extends BaseController
{
    /** Formulaire de connexion. */
    public function index(): string|\CodeIgniter\HTTP\RedirectResponse
    {
        if ($this->auth->check()) {
            return redirect()->to('/admin/dashboard');
        }

        return view('auth/login', ['page_title' => 'Connexion – Rebencia']);
    }

    /** Traitement de la connexion. */
    public function authenticate()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $result = $this->auth->attempt(
            $this->request->getPost('email'),
            $this->request->getPost('password')
        );

        if ($result !== true) {
            return redirect()->back()
                ->withInput()
                ->with('error', $result);
        }

        // Redirection post-login
        $redirect = session()->get('redirect_url') ?? '/admin/dashboard';
        session()->remove('redirect_url');

        return redirect()->to($redirect);
    }

    /** Déconnexion. */
    public function logout()
    {
        $this->auth->logout();
        return redirect()->to('/login')->with('success', 'Vous avez été déconnecté.');
    }
}
