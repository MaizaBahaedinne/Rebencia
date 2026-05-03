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

    /**
     * Retourne le scope de données selon la hiérarchie du rôle connecté.
     *
     * Retourne un tableau :
     *   ['type' => 'all']                              → SuperAdmin/Admin — aucun filtre
     *   ['type' => 'organization', 'value' => $orgId]  → PDG/DG — toutes les agences de l'org
     *   ['type' => 'agency',       'value' => $agId]   → Dir.Agence/Coord — une seule agence
     *   ['type' => 'own',          'value' => $userId] → Expert/Collab — données propres
     */
    protected function getDataScope(): array
    {
        $level = $this->auth->getHierarchyLevel();

        // Niveau 0 = session antérieure à la migration, on tombe sur le comportement legacy
        if ($level === 0) {
            $agencyId = (int) session()->get('agency_id');
            return $agencyId ? ['type' => 'agency', 'value' => $agencyId] : ['type' => 'all'];
        }

        // Niveaux 1-2 : SuperAdmin / Admin — tout voir
        if ($level <= 2) {
            return ['type' => 'all'];
        }

        // Niveau 3 : PDG / Directeur Général — voir leur organisation
        if ($level === 3) {
            $orgId = (int) session()->get('organization_id');
            return $orgId ? ['type' => 'organization', 'value' => $orgId] : ['type' => 'all'];
        }

        // Niveau 4 : Directeur Agence / Coordinateur — voir leur agence
        if ($level === 4) {
            $agencyId = (int) session()->get('agency_id');
            return $agencyId ? ['type' => 'agency', 'value' => $agencyId] : ['type' => 'all'];
        }

        // Niveau 5 : Expert / Collaborateur — uniquement leurs propres données
        return ['type' => 'own', 'value' => $this->auth->id()];
    }
}
