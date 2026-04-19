<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PermissionFilter – Vérifie qu'une permission spécifique est accordée.
 * Utilisation dans Routes.php : ['filter' => 'permission:permission.name']
 */
class PermissionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (empty($arguments)) {
            return;
        }

        $required = $arguments[0];
        $userPermissions = session()->get('permissions') ?? [];

        if (! in_array($required, $userPermissions, true)) {
            // Requête AJAX : retourner JSON 403
            if ($request->isAJAX()) {
                return service('response')
                    ->setStatusCode(403)
                    ->setJSON(['error' => 'Permission refusée : ' . $required]);
            }

            return redirect()->to('/admin/dashboard')
                ->with('error', "Vous n'avez pas la permission requise : {$required}");
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Rien à faire après
    }
}
