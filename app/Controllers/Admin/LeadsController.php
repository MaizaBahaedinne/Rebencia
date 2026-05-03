<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\UserModel;
use App\Models\PropertyModel;

/**
 * LeadsController – CRM Leads Rebencia.
 */
class LeadsController extends BaseController
{
    protected LeadModel $model;

    public function __construct()
    {
        $this->model = new LeadModel();
    }

    /** Liste des leads avec pipeline. */
    public function index(): string
    {
        $this->requirePermission('leads.view');

        $filters = [
            'status'      => $this->request->getGet('status'),
            'assigned_to' => $this->request->getGet('assigned_to'),
            'priority'    => $this->request->getGet('priority'),
            'search'      => $this->request->getGet('search'),
            'page'        => $this->request->getGet('page') ?? 1,
        ];

        // Collaborateur : seulement ses leads
        if ($this->auth->hasRole('collaborator')) {
            $filters['assigned_to'] = $this->auth->id();
        }

        // Restriction agence : rôles sans permission agencies.create ne voient que leur agence
        $agencyId = (int) session()->get('agency_id');
        if ($agencyId && ! $this->auth->hasPermission('agencies.create')) {
            $filters['agency_id'] = $agencyId;
        }

        $result = $this->model->getFiltered($filters);

        return $this->render('admin/leads/index', [
            'page_title' => 'Leads / CRM',
            'leads'      => $result['data'],
            'total'      => $result['total'],
            'page'       => $result['page'],
            'pages'      => $result['pages'],
            'per_page'   => $result['per_page'],
            'filters'    => $filters,
            'agents'     => (new UserModel())->getWithRole(['status' => 'active']),
            'pipeline'   => $this->model->getPipeline(
                $this->auth->hasRole('collaborator') ? $this->auth->id() : null,
                $filters['agency_id'] ?? null
            ),
        ]);
    }

    /** Formulaire création lead. */
    public function create(): string
    {
        $this->requirePermission('leads.create');

        return $this->render('admin/leads/form', [
            'page_title' => 'Nouveau lead',
            'lead'       => [],
            'agents'     => (new UserModel())->getWithRole(['status' => 'active']),
            'properties' => (new PropertyModel())->getFiltered(['status' => 'available'])['data'],
            'zones'      => $this->getZonesList(),
        ]);
    }

    /** Enregistrement. */
    public function store()
    {
        $this->requirePermission('leads.create');

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'phone'      => 'required|min_length[8]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();
        $id   = $this->model->insert($this->buildLeadData($post, true));

        $this->log->activity('lead.create', 'leads', 'lead', $this->model->getInsertID(), 'Création lead');

        return redirect()->to('/admin/leads/' . $this->model->getInsertID())
            ->with('success', 'Lead créé avec succès.');
    }

    /** Détail lead. */
    public function show(int $id): string
    {
        $this->requirePermission('leads.view');
        $lead = $this->findOrFail($id);

        $propertyModel = new PropertyModel();

        $linkedProperty = null;
        if (!empty($lead['property_id'])) {
            $linkedProperty = $propertyModel->findWithImages((int) $lead['property_id']);
        }

        $similarProperties = $propertyModel->getSimilarProperties($lead);

        return $this->render('admin/leads/show', [
            'page_title'        => $lead['first_name'] . ' ' . $lead['last_name'],
            'lead'              => $lead,
            'agents'            => (new UserModel())->getWithRole(['status' => 'active']),
            'notes'             => $lead['lead_notes'],
            'statusHistory'     => $lead['status_history'],
            'linkedProperty'    => $linkedProperty,
            'similarProperties' => $similarProperties,
        ]);
    }

    /** Formulaire édition. */
    public function edit(int $id): string
    {
        $this->requirePermission('leads.edit');
        $lead = $this->findOrFail($id);

        // Décoder les JSON pour la vue
        $lead['property_types_arr']  = json_decode($lead['property_types']   ?? '[]', true) ?: [];
        $lead['desired_zone_ids_arr'] = json_decode($lead['desired_zone_ids'] ?? '[]', true) ?: [];

        return $this->render('admin/leads/form', [
            'page_title' => 'Modifier lead – ' . $lead['first_name'],
            'lead'       => $lead,
            'agents'     => (new UserModel())->getWithRole(['status' => 'active']),
            'properties' => (new PropertyModel())->getFiltered(['status' => 'available'])['data'],
            'zones'      => $this->getZonesList(),
        ]);
    }

    /** Mise à jour. */
    public function update(int $id)
    {
        $this->requirePermission('leads.edit');
        $this->findOrFail($id);

        $post = $this->request->getPost();
        $this->model->update($id, $this->buildLeadData($post, false));

        $this->log->activity('lead.update', 'leads', 'lead', $id, 'Modification lead');

        return redirect()->to('/admin/leads/' . $id)->with('success', 'Lead mis à jour.');
    }

    /** Changement de statut (AJAX). */
    public function updateStatus(int $id)
    {
        $this->requirePermission('leads.edit');

        $newStatus = $this->request->getPost('status');
        $validStatuses = ['new', 'contacted', 'visit', 'negotiation', 'sold', 'lost'];

        if (! in_array($newStatus, $validStatuses, true)) {
            return $this->json(['error' => 'Statut invalide'], 422);
        }

        $this->model->changeStatus($id, $newStatus, $this->auth->id(), $this->request->getPost('notes') ?? '');
        $this->log->activity('lead.status', 'leads', 'lead', $id, "Statut → {$newStatus}");

        return $this->json(['success' => true, 'status' => $newStatus]);
    }

    /** Assignation à un agent (AJAX). */
    public function assign(int $id)
    {
        $this->requirePermission('leads.assign');

        $agentId = (int) $this->request->getPost('agent_id');
        $this->model->update($id, ['assigned_to' => $agentId ?: null]);
        $this->log->activity('lead.assign', 'leads', 'lead', $id, "Assigné → agent #{$agentId}");

        return $this->json(['success' => true]);
    }

    /** Ajout de note (AJAX). */
    public function addNote(int $id)
    {
        $this->requirePermission('leads.edit');

        $content = trim($this->request->getPost('content') ?? '');
        if (empty($content)) {
            return $this->json(['error' => 'Note vide'], 422);
        }

        $this->model->addNote($id, $this->auth->id(), $content);

        return $this->json(['success' => true, 'author' => session()->get('user_name'), 'created_at' => date('Y-m-d H:i:s')]);
    }

    /** Suppression (soft). */
    public function delete(int $id)
    {
        $this->requirePermission('leads.delete');
        $this->model->delete($id);
        $this->log->activity('lead.delete', 'leads', 'lead', $id, 'Suppression lead');

        return redirect()->to('/admin/leads')->with('success', 'Lead supprimé.');
    }

    // --------------------------------------------------------
    private function findOrFail(int $id): array
    {
        $lead = $this->model->findWithDetails($id);
        if (! $lead) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Lead #{$id} introuvable");
        }
        return $lead;
    }

    /**
     * Construit le tableau de données lead depuis un POST.
     * $isCreate = true ajoute les champs réservés à la création.
     */
    private function buildLeadData(array $post, bool $isCreate): array
    {
        $types = $post['property_types'] ?? [];
        $zoneIds = $post['desired_zone_ids'] ?? [];

        $data = [
            'first_name'         => $post['first_name'],
            'last_name'          => $post['last_name'],
            'email'              => ($post['email']            ?? '') ?: null,
            'phone'              => $post['phone'],
            'source'             => ($post['source']           ?? '') ?: 'website',
            'assigned_to'        => ($post['assigned_to']      ?? '') ?: null,
            'property_id'        => ($post['property_id']      ?? '') ?: null,
            'budget_min'         => ($post['budget_min']       ?? '') ?: null,
            'budget_max'         => ($post['budget_max']       ?? '') ?: null,
            'property_type'      => is_array($types) && count($types) ? $types[0] : (($post['property_type'] ?? '') ?: null),
            'property_types'     => !empty($types) ? json_encode(array_values((array) $types)) : null,
            'transaction_type'   => ($post['transaction_type'] ?? '') ?: 'sale',
            'priority'           => ($post['priority']         ?? '') ?: 'medium',
            'notes'              => $post['notes'] ?? '',
            'next_follow_up'     => ($post['next_follow_up']   ?? '') ?: null,
            'desired_surface'    => ($post['desired_surface']  ?? '') ?: null,
            'desired_location'   => ($post['desired_location'] ?? '') ?: null,
            'desired_zone_ids'   => !empty($zoneIds) ? json_encode(array_values(array_filter(array_map('intval', (array) $zoneIds)))) : null,
            'surface_min'        => ($post['surface_min']      ?? '') ?: null,
            'surface_max'        => ($post['surface_max']      ?? '') ?: null,
            'rooms_min'          => ($post['rooms_min']        ?? '') ?: null,
            'bedrooms_min'       => ($post['bedrooms_min']     ?? '') ?: null,
            'bathrooms_min'      => ($post['bathrooms_min']    ?? '') ?: null,
            'floor_min'          => isset($post['floor_min']) && $post['floor_min'] !== '' ? (int) $post['floor_min'] : null,
            'floor_max'          => isset($post['floor_max']) && $post['floor_max'] !== '' ? (int) $post['floor_max'] : null,
            'wants_parking'      => isset($post['wants_parking'])  ? 1 : 0,
            'wants_elevator'     => isset($post['wants_elevator']) ? 1 : 0,
            'wants_garden'       => isset($post['wants_garden'])   ? 1 : 0,
            'wants_pool'         => isset($post['wants_pool'])     ? 1 : 0,
            'wants_terrace'      => isset($post['wants_terrace'])  ? 1 : 0,
            'construction_state' => ($post['construction_state']   ?? '') ?: 'any',
            'furnished'          => ($post['furnished']            ?? '') ?: 'any',
            'orientation'        => ($post['orientation']          ?? '') ?: null,
            'target_date'        => ($post['target_date']          ?? '') ?: null,
        ];

        if ($isCreate) {
            $data['status'] = 'new';
        }

        return $data;
    }

    /** Retourne la liste des zones (villes/quartiers) pour le sélecteur. */
    private function getZonesList(): array
    {
        try {
            $db = \Config\Database::connect();
            return $db->query('SELECT id, name, parent_id FROM zones WHERE is_active = 1 ORDER BY parent_id ASC, name ASC')->getResultArray();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
