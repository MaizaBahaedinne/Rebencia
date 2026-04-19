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
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après
    }
}
