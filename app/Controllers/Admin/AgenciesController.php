<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AgencyModel;
use App\Models\ZoneModel;

/**
 * AgenciesController – CRUD Agences immobilières.
 *
 * Règles métier :
 *   - Seuls super_admin / admin peuvent créer, modifier, supprimer.
 *   - Un directeur peut voir son agence (agencies.view).
 *   - Les biens et utilisateurs sont privés par agence.
 *   - Les clients sont partagés entre toutes les agences.
 */
class AgenciesController extends BaseController
{
    protected AgencyModel $model;

    public function __construct()
    {
        $this->model = new AgencyModel();
    }

    // --------------------------------------------------------
    // Liste
    // --------------------------------------------------------
    public function index(): string
    {
        $this->requirePermission('agencies.view');

        $filters = [
            'search'    => $this->request->getGet('search'),
            'is_active' => $this->request->getGet('is_active'),
        ];

        // Directeur : ne voit que son agence
        if ($this->auth->hasRole('director') && ! $this->auth->hasRole('admin') && ! $this->auth->hasRole('super_admin')) {
            $agencyId = session()->get('agency_id');
            $agencies = $agencyId ? [$this->model->findDetail((int) $agencyId)] : [];
            $agencies = array_filter($agencies); // retire les null
        } else {
            $agencies = $this->model->getList($filters);
        }

        return $this->render('admin/agencies/index', [
            'page_title' => 'Agences',
            'agencies'   => $agencies,
            'filters'    => $filters,
        ]);
    }

    // --------------------------------------------------------
    // Formulaire création
    // --------------------------------------------------------
    public function create(): string
    {
        $this->requirePermission('agencies.create');

        return $this->render('admin/agencies/form', [
            'page_title' => 'Nouvelle agence',
            'agency'     => [],
            'zones'      => (new ZoneModel())->getByType('pays'),
        ]);
    }

    // --------------------------------------------------------
    // Enregistrement
    // --------------------------------------------------------
    public function store()
    {
        $this->requirePermission('agencies.create');

        $rules = [
            'name'  => 'required|min_length[2]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[191]',
            'phone' => 'permit_empty|max_length[30]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));
        $slug = $this->model->generateSlug($name);

        $logoPath = $this->_uploadLogo();

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'email'       => $this->request->getPost('email') ?: null,
            'phone'       => $this->request->getPost('phone') ?: null,
            'address'     => $this->request->getPost('address') ?: null,
            'city'        => $this->request->getPost('city') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'zone_id'     => $this->request->getPost('zone_id') ?: null,
            'is_active'   => (int) ($this->request->getPost('is_active') ?? 1),
            'logo'        => $logoPath,
        ];

        $id = $this->model->insert($data);

        $this->log->activity('create', 'agencies', 'agency', $id, "Agence créée : {$name}");

        return redirect()->to(base_url('admin/agencies/' . $id))->with('success', "Agence « {$name} » créée avec succès.");
    }

    // --------------------------------------------------------
    // Détail
    // --------------------------------------------------------
    public function show(int $id): string
    {
        $this->requirePermission('agencies.view');

        $agency = $this->model->findDetail($id);
        if (! $agency) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Agence introuvable.");
        }

        // Directeur : accès uniquement à son agence
        $this->_assertAgencyAccess($agency);

        return $this->render('admin/agencies/show', [
            'page_title'   => $agency['name'],
            'agency'       => $agency,
            'members'      => $this->model->getMembers($id),
            'properties'   => $this->model->getProperties($id, 12),
        ]);
    }

    // --------------------------------------------------------
    // Formulaire édition
    // --------------------------------------------------------
    public function edit(int $id): string
    {
        $this->requirePermission('agencies.edit');

        $agency = $this->model->findDetail($id);
        if (! $agency) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Agence introuvable.");
        }

        $this->_assertAgencyAccess($agency);

        return $this->render('admin/agencies/form', [
            'page_title' => 'Modifier : ' . $agency['name'],
            'agency'     => $agency,
            'zones'      => (new ZoneModel())->getByType('pays'),
        ]);
    }

    // --------------------------------------------------------
    // Mise à jour
    // --------------------------------------------------------
    public function update(int $id)
    {
        $this->requirePermission('agencies.edit');

        $agency = $this->model->find($id);
        if (! $agency) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Agence introuvable.");
        }

        $rules = [
            'name'  => 'required|min_length[2]|max_length[150]',
            'email' => 'permit_empty|valid_email|max_length[191]',
            'phone' => 'permit_empty|max_length[30]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = trim($this->request->getPost('name'));
        $slug = $this->model->generateSlug($name, $id);

        $logoPath = $this->_uploadLogo($agency['logo'] ?? null);

        $data = [
            'name'        => $name,
            'slug'        => $slug,
            'email'       => $this->request->getPost('email') ?: null,
            'phone'       => $this->request->getPost('phone') ?: null,
            'address'     => $this->request->getPost('address') ?: null,
            'city'        => $this->request->getPost('city') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'zone_id'     => $this->request->getPost('zone_id') ?: null,
            'is_active'   => (int) ($this->request->getPost('is_active') ?? 1),
            'logo'        => $logoPath,
        ];

        $this->model->update($id, $data);

        $this->log->activity('update', 'agencies', 'agency', $id, "Agence modifiée : {$name}");

        return redirect()->to(base_url('admin/agencies/' . $id))->with('success', "Agence mise à jour.");
    }

    // --------------------------------------------------------
    // Toggle actif / inactif
    // --------------------------------------------------------
    public function toggleStatus(int $id)
    {
        $this->requirePermission('agencies.edit');

        $agency = $this->model->find($id);
        if (! $agency) {
            return $this->json(['error' => 'Agence introuvable.'], 404);
        }

        $newStatus = (int) ! $agency['is_active'];
        $this->model->update($id, ['is_active' => $newStatus]);
        $this->log->activity('toggle_status', 'agencies', 'agency', $id,
            "Agence {$agency['name']} : " . ($newStatus ? 'activée' : 'désactivée'));

        return redirect()->to(base_url('admin/agencies'))->with('success',
            "Statut de l'agence « {$agency['name']} » mis à jour.");
    }

    // --------------------------------------------------------
    // Suppression (soft-delete)
    // --------------------------------------------------------
    public function delete(int $id)
    {
        $this->requirePermission('agencies.delete');

        $agency = $this->model->find($id);
        if (! $agency) {
            return redirect()->to(base_url('admin/agencies'))->with('error', 'Agence introuvable.');
        }

        // Vérifier qu'il n'y a pas de membres actifs
        $members = $this->model->getMembers($id);
        if (! empty($members)) {
            return redirect()->to(base_url('admin/agencies'))->with('error',
                "Impossible de supprimer : l'agence a encore " . count($members) . " membre(s). Réaffectez-les d'abord.");
        }

        $this->model->delete($id);
        $this->log->activity('delete', 'agencies', 'agency', $id, "Agence supprimée : {$agency['name']}");

        return redirect()->to(base_url('admin/agencies'))->with('success', "Agence supprimée.");
    }

    // --------------------------------------------------------
    // Privé : upload logo
    // --------------------------------------------------------
    private function _uploadLogo(?string $existingLogo = null): ?string
    {
        $file = $this->request->getFile('logo');
        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return $existingLogo;
        }

        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml'];
        if (! in_array($file->getMimeType(), $allowed, true)) {
            return $existingLogo;
        }

        $newName  = 'agency_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file->getExtension();
        $uploadDir = FCPATH . 'uploads/agencies/';

        if (! is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $file->move($uploadDir, $newName);
        return 'uploads/agencies/' . $newName;
    }

    // --------------------------------------------------------
    // Privé : vérifier accès directeur
    // --------------------------------------------------------
    private function _assertAgencyAccess(array $agency): void
    {
        // Super admin & admin voient tout
        if ($this->auth->hasPermission('agencies.create')) {
            return;
        }
        // Directeur : vérifier que c'est bien son agence
        if ((int) session()->get('agency_id') !== (int) $agency['id']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Accès refusé à cette agence.");
        }
    }
}
