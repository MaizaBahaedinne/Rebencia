<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\LeadModel;
use App\Models\UserModel;
use App\Models\PropertyModel;
use App\Models\VisitModel;
use App\Libraries\NotificationService;

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

        // Scope hiérarchique
        $scope = $this->getDataScope();
        switch ($scope['type']) {
            case 'organization':
                $filters['organization_id'] = $scope['value'];
                break;
            case 'agency':
                $filters['agency_id'] = $scope['value'];
                break;
            case 'own':
                $filters['assigned_to'] = $scope['value'];
                break;
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
                $filters['assigned_to'] ?? null,
                $filters['agency_id']      ?? null,
                $filters['organization_id'] ?? null
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
        $newId = $this->model->getInsertID();

        $this->log->activity('lead.create', 'leads', 'lead', $newId, 'Création lead');

        // Notifier l'agence responsable du bien lié
        if (! empty($post['property_id'])) {
            $this->notifyPropertyAgency(
                (int) $post['property_id'],
                ($post['first_name'] ?? '') . ' ' . ($post['last_name'] ?? ''),
                $newId,
                'Nouveau lead'
            );
        }

        return redirect()->to('/admin/leads/' . $newId)
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
            'canEdit'           => $this->canEditLead($lead),
        ]);
    }

    /** Formulaire édition. */
    public function edit(int $id): string
    {
        $this->requirePermission('leads.edit');
        $lead = $this->findOrFail($id);

        if (! $this->canEditLead($lead)) {
            return redirect()->to('/admin/leads/' . $id)
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce lead.');
        }

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
        $lead = $this->findOrFail($id);

        if (! $this->canEditLead($lead)) {
            return redirect()->to('/admin/leads/' . $id)
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce lead.');
        }

        $post = $this->request->getPost();
        $this->model->update($id, $this->buildLeadData($post, false));

        $this->log->activity('lead.update', 'leads', 'lead', $id, 'Modification lead');

        return redirect()->to('/admin/leads/' . $id)->with('success', 'Lead mis à jour.');
    }

    /** Changement de statut avec logique métier par étape. */
    public function updateStatus(int $id)
    {
        $this->requirePermission('leads.edit');

        $newStatus     = $this->request->getPost('status');
        $validStatuses = ['new', 'contacted', 'interested', 'visit_done', 'negotiating', 'won', 'lost'];
        $lead          = $this->findOrFail($id);

        if (! $this->canEditLead($lead)) {
            return redirect()->to('/admin/leads/' . $id)
                ->with('error', 'Vous n\'êtes pas autorisé à modifier ce lead.');
        }

        if (! in_array($newStatus, $validStatuses, true)) {
            return redirect()->back()->with('error', 'Statut invalide.');
        }

        switch ($newStatus) {

            // ── Contacté : note obligatoire + notif agence ───────────
            case 'contacted':
                $note = trim($this->request->getPost('contact_note') ?? '');
                if (empty($note)) {
                    return redirect()->back()->with('error', 'Une note est requise pour passer au statut « Contacté ».');
                }
                $this->model->changeStatus($id, 'contacted', $this->auth->id(), $note);
                $this->model->addNote($id, $this->auth->id(), $note);
                if (! empty($lead['property_id'])) {
                    $this->notifyPropertyAgency(
                        (int) $lead['property_id'],
                        $lead['first_name'] . ' ' . $lead['last_name'],
                        $id,
                        'Lead contacté',
                        $lead['property_title'] ?? null
                    );
                }
                break;

            // ── Intéressé : RDV de visite obligatoire ────────────────
            case 'interested':
                $visitDate = trim($this->request->getPost('visit_date') ?? '');
                $visitTime = trim($this->request->getPost('visit_time') ?? '');
                if (empty($visitDate)) {
                    return redirect()->back()->with('error', 'Veuillez choisir une date pour le RDV de visite.');
                }
                $visitNotes = 'Depuis lead #' . $id . ' — ' . $lead['first_name'] . ' ' . $lead['last_name'];
                $extra      = trim($this->request->getPost('visit_notes') ?? '');
                if ($extra !== '') {
                    $visitNotes .= ' — ' . $extra;
                }
                $visitModel = new VisitModel();
                $visitModel->insert([
                    'property_id' => $lead['property_id'] ?: null,
                    'agent_id'    => $lead['assigned_to'] ?: null,
                    'visit_date'  => $visitDate,
                    'visit_time'  => $visitTime ?: null,
                    'status'      => 'planifiee',
                    'notes'       => $visitNotes,
                    'created_by'  => $this->auth->id(),
                ]);
                $rdvNote = 'RDV de visite planifié le ' . date('d/m/Y', strtotime($visitDate))
                         . ($visitTime ? ' à ' . $visitTime : '');
                $this->model->changeStatus($id, 'interested', $this->auth->id(), $rdvNote);
                $this->model->addNote($id, $this->auth->id(), $rdvNote);
                break;

            // ── Visite effectuée : auto-avance en Négociation ────────
            case 'visit_done':
                $this->model->changeStatus($id, 'visit_done',   $this->auth->id(), 'Visite effectuée');
                $this->model->changeStatus($id, 'negotiating',  $this->auth->id(), 'Passage automatique en négociation après visite effectuée');
                $this->log->activity('lead.status', 'leads', 'lead', $id, 'Visite effectuée → Négociation (auto)');
                return redirect()->to('/admin/leads/' . $id)
                    ->with('success', 'Visite effectuée — lead passé automatiquement en Négociation.');

            // ── Conclu (won) ─────────────────────────────────────────
            case 'won':
                $this->model->changeStatus($id, 'won', $this->auth->id(), 'Lead conclu');
                break;

            // ── Perdu : raison obligatoire ───────────────────────────
            case 'lost':
                $reason = trim($this->request->getPost('lost_reason') ?? '');
                $detail = trim($this->request->getPost('lost_detail') ?? '');
                if (empty($reason)) {
                    return redirect()->back()->with('error', 'Veuillez indiquer la raison de la perte.');
                }
                $noteText = 'Raison de la perte : ' . $reason;
                if ($detail !== '') {
                    $noteText .= ' — ' . $detail;
                }
                $this->model->changeStatus($id, 'lost', $this->auth->id(), $noteText);
                $this->model->addNote($id, $this->auth->id(), $noteText);
                break;

            default:
                $this->model->changeStatus($id, $newStatus, $this->auth->id());
        }

        $this->log->activity('lead.status', 'leads', 'lead', $id, "Statut → {$newStatus}");
        return redirect()->to('/admin/leads/' . $id)->with('success', 'Statut mis à jour avec succès.');
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

    /** Ajout de note. */
    public function addNote(int $id)
    {
        $this->requirePermission('leads.edit');

        // Accepte 'note' (form normal) ou 'content' (appel AJAX legacy)
        $content = trim($this->request->getPost('note') ?? $this->request->getPost('content') ?? '');
        if (empty($content)) {
            if ($this->request->isAJAX()) {
                return $this->json(['error' => 'Note vide'], 422);
            }
            return redirect()->back()->with('error', 'La note ne peut pas être vide.');
        }

        $this->model->addNote($id, $this->auth->id(), $content);

        if ($this->request->isAJAX()) {
            return $this->json(['success' => true, 'author' => session()->get('user_name'), 'created_at' => date('Y-m-d H:i:s')]);
        }
        return redirect()->to('/admin/leads/' . $id)->with('success', 'Note ajoutée.');
    }

    /** Suppression (soft). */
    public function delete(int $id)
    {
        $this->requirePermission('leads.delete');
        $this->model->delete($id);
        $this->log->activity('lead.delete', 'leads', 'lead', $id, 'Suppression lead');

        return redirect()->to('/admin/leads')->with('success', 'Lead supprimé.');
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Notifie tous les membres actifs de l'agence responsable d'un bien.
     */
    private function notifyPropertyAgency(
        int    $propertyId,
        string $leadName,
        int    $leadId,
        string $title   = 'Nouveau lead',
        ?string $propTitle = null
    ): void {
        $db = \Config\Database::connect();

        // Récupère l'agent du bien + son agence
        $prop = $db->query(
            'SELECT p.title, p.agent_id, u.agency_id
             FROM properties p
             LEFT JOIN users u ON u.id = p.agent_id
             WHERE p.id = ? AND p.deleted_at IS NULL LIMIT 1',
            [$propertyId]
        )->getRowArray();

        if (empty($prop['agency_id'])) {
            return;
        }

        $users = $db->query(
            'SELECT id FROM users WHERE agency_id = ? AND status = ? AND deleted_at IS NULL',
            [$prop['agency_id'], 'active']
        )->getResultArray();

        if (empty($users)) {
            return;
        }

        $userIds   = array_column($users, 'id');
        $bienTitle = $propTitle ?? $prop['title'] ?? 'Bien #' . $propertyId;
        $message   = "{$leadName} est intéressé(e) par « {$bienTitle} »";

        (new NotificationService())->send(
            $userIds,
            $title,
            $message,
            'lead',
            base_url("admin/leads/{$leadId}")
        );
    }

    private function findOrFail(int $id): array
    {
        $lead = $this->model->findWithDetails($id);
        if (! $lead) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Lead #{$id} introuvable");
        }
        return $lead;
    }

    /**
     * Un utilisateur peut modifier un lead si :
     *  - Il est admin/superadmin (niveau ≤ 2)
     *  - Il est PDG/DG (niveau 3) — organisation complète
     *  - Il est Directeur Agence (niveau 4) — son agence
     *  - Il est lui-même l'agent assigné au lead (niveau ≥ 5)
     */
    private function canEditLead(array $lead): bool
    {
        if (! $this->auth->hasPermission('leads.edit')) {
            return false;
        }

        $level = $this->auth->getHierarchyLevel();

        // Niveaux 1-4 (Admin, PDG, Directeur Agence) → accès complet
        // Niveau 0 (session legacy) et 5+ (Collaborateur) → uniquement ses propres leads
        if ($level >= 1 && $level <= 4) {
            return true;
        }

        // Niveau 5+ (collaborateur/expert) → uniquement ses propres leads
        return (int) ($lead['assigned_to'] ?? 0) === (int) $this->auth->id();
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
