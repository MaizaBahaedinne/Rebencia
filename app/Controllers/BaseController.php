<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Libraries\AuthLibrary;
use App\Libraries\LogLibrary;

/**
 * BaseController – Contrôleur parent Rebencia.
 * Fournit l'accès aux helpers, bibliothèques et méthodes communes.
 */
abstract class BaseController extends Controller
{
    protected AuthLibrary $auth;
    protected LogLibrary  $log;

    public function initController(
        RequestInterface  $request,
        ResponseInterface $response,
        LoggerInterface   $logger
    ): void {
        parent::initController($request, $response, $logger);

        $this->auth = new AuthLibrary();
        $this->log  = new LogLibrary();

        helper(['url', 'form', 'html', 'text']);
    }

    // --------------------------------------------------------
    // Helpers partagés
    // --------------------------------------------------------

    /**
     * Vérifie une permission ou abort 403.
     */
    protected function requirePermission(string $permission): void
    {
        if (! $this->auth->hasPermission($permission)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException(
                "Permission refusée : {$permission}"
            );
        }
    }

    /**
     * Retourne JSON pour les requêtes AJAX.
     */
    protected function json(array $data, int $code = 200): ResponseInterface
    {
        return $this->response->setStatusCode($code)->setJSON($data);
    }

    /**
     * Charge une vue admin dans le layout principal.
     */
    protected function render(string $view, array $data = []): string
    {
        $data['auth']       = $this->auth;
        $data['page_title'] = $data['page_title'] ?? 'Rebencia';
        $data['content']    = view($view, $data);

        return view('layouts/main', $data);
    }
}
