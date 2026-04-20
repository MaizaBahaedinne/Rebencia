<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\VisitModel;
use App\Models\ClientModel;
use App\Models\UserModel;

class VisitsController extends BaseController
{
    private VisitModel $model;

    public function __construct()
    {
        $this->model = new VisitModel();
    }

    // ----------------------------------------------------------------
    // Liste
    // ----------------------------------------------------------------

    public function index(): string
    {
        $this->requirePermission('visits.view');

        $filters = [
            'status'    => $this->request->getGet('status'),
            'agent_id'  => $this->request->getGet('agent_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to'   => $this->request->getGet('date_to'),
            'search'    => $this->request->getGet('search'),
            'page'      => $this->request->getGet('page') ?? 1,
        ];

        return $this->render('admin/visits/index', [
            'page_title'   => 'Visites',
            'result'       => $this->model->getFiltered($filters),
            'filters'      => $filters,
            'agents'       => (new UserModel())->getWithRole(['status' => 'active']),
            'statusCounts' => $this->model->countByStatus(),
            'statusLabels' => VisitModel::STATUS_LABELS,
        ]);
    }

    // ----------------------------------------------------------------
    // Calendrier
    // ----------------------------------------------------------------

    public function calendar(): string
    {
        $this->requirePermission('visits.view');

        return $this->render('admin/visits/calendar', [
            'page_title' => 'Calendrier des visites',
            'agents'     => (new UserModel())->getWithRole(['status' => 'active']),
        ]);
    }

    /**
     * AJAX — événements pour FullCalendar.
     */
    public function calendarEvents()
    {
        $this->requirePermission('visits.view');

        $start   = substr($this->request->getGet('start') ?? date('Y-m-01'), 0, 10);
        $end     = substr($this->request->getGet('end')   ?? date('Y-m-t'),  0, 10);
        $agentId = (int) ($this->request->getGet('agent_id') ?? 0) ?: null;

        $rows   = $this->model->getForCalendar($start, $end, $agentId);
        $events = [];

        foreach ($rows as $v) {
            $color    = VisitModel::STATUS_COLORS_HEX[$v['status']] ?? '#6c757d';
            $startDt  = $v['visit_date'] . 'T' . $v['visit_time'];
            $endTs    = strtotime($startDt) + ((int) ($v['duration'] ?? 60)) * 60;

            $events[] = [
                'id'    => (string) $v['id'],
                'title' => $v['first_name'] . ' ' . $v['last_name'] . ' — ' . $v['property_title'],
                'start' => $startDt,
                'end'   => date('Y-m-d\TH:i:s', $endTs),
                'color' => $color,
                'extendedProps' => [
                    'agent'  => $v['agent_first'] . ' ' . $v['agent_last'],
                    'status' => $v['status'],
                    'city'   => $v['property_city'] ?? '',
                ],
                'url' => base_url('admin/visits/' . $v['id']),
            ];
        }

        return $this->json($events);
    }

    /**
     * AJAX — vérification disponibilité agent (GET).
     */
    public function checkAvailability()
    {
        $agentId   = (int) ($this->request->getGet('agent_id') ?? 0);
        $date      = $this->request->getGet('date')     ?? '';
        $time      = $this->request->getGet('time')     ?? '';
        $duration  = (int) ($this->request->getGet('duration')   ?? 60);
        $excludeId = (int) ($this->request->getGet('exclude_id') ?? 0) ?: null;

        if (! $agentId || ! $date || ! $time) {
            return $this->json(['available' => true]);
        }

        $conflict = $this->model->checkAgentConflict($agentId, $date, $time, $duration, $excludeId);
        return $this->json(['available' => ! $conflict]);
    }

    // ----------------------------------------------------------------
    // Création
    // ----------------------------------------------------------------

    public function create(): string
    {
        $this->requirePermission('visits.create');

        return $this->render('admin/visits/form', [
            'page_title'   => 'Nouvelle visite',
            'visit'        => [
                'client_id'   => $this->request->getGet('client_id'),
                'property_id' => $this->request->getGet('property_id'),
                'agent_id'    => $this->auth->id() ?: '',
                'duration'    => 60,
                'status'      => 'planifiee',
            ],
            'clients'      => $this->getClientsList(),
            'properties'   => $this->getPropertiesList(),
            'agents'       => (new UserModel())->getWithRole(['status' => 'active']),
            'statusLabels' => VisitModel::STATUS_LABELS,
        ]);
    }

    public function store()
    {
        $this->requirePermission('visits.create');

        if (! $this->validate([
            'client_id'   => 'required|is_natural_no_zero',
            'property_id' => 'required|is_natural_no_zero',
            'agent_id'    => 'required|is_natural_no_zero',
            'visit_date'  => 'required|valid_date',
            'visit_time'  => 'required',
        ])) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->buildData();

        // Vérification de disponibilité agent
        if ($this->model->checkAgentConflict(
            (int) $data['agent_id'],
            $data['visit_date'],
            $data['visit_time'],
            (int) $data['duration']
        )) {
            return redirect()->back()->withInput()
                ->with('error', 'Cet agent a déjà une visite prévue à ce créneau. Veuillez choisir un autre horaire ou un autre agent.');
        }

        $data['created_by'] = $this->auth->id() ?: null;
        $this->model->insert($data);

        $this->log->activity('create', 'visits', 'visit', $this->model->getInsertID(), 'Nouvelle visite planifiée');

        return redirect()->to(base_url('admin/visits'))
            ->with('success', 'Visite planifiée avec succès.');
    }

    // ----------------------------------------------------------------
    // Détail
    // ----------------------------------------------------------------

    public function show(int $id): string
    {
        $this->requirePermission('visits.view');

        $visit = $this->model->findWithRelations($id);
        if (! $visit) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Visite #$id introuvable");
        }

        return $this->render('admin/visits/show', [
            'page_title'     => 'Visite #' . $id,
            'visit'          => $visit,
            'statusLabels'   => VisitModel::STATUS_LABELS,
            'feedbackLabels' => VisitModel::FEEDBACK_LABELS,
        ]);
    }

    // ----------------------------------------------------------------
    // Modification
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        $this->requirePermission('visits.edit');

        $visit = $this->model->find($id);
        if (! $visit) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Visite #$id introuvable");
        }

        return $this->render('admin/visits/form', [
            'page_title'   => 'Modifier la visite #' . $id,
            'visit'        => $visit,
            'clients'      => $this->getClientsList(),
            'properties'   => $this->getPropertiesList(),
            'agents'       => (new UserModel())->getWithRole(['status' => 'active']),
            'statusLabels' => VisitModel::STATUS_LABELS,
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('visits.edit');

        $visit = $this->model->find($id);
        if (! $visit) {
            return redirect()->to(base_url('admin/visits'))->with('error', 'Visite introuvable.');
        }

        if (! $this->validate([
            'client_id'   => 'required|is_natural_no_zero',
            'property_id' => 'required|is_natural_no_zero',
            'agent_id'    => 'required|is_natural_no_zero',
            'visit_date'  => 'required|valid_date',
            'visit_time'  => 'required',
        ])) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $data = $this->buildData();

        // Vérification de disponibilité (exclure la visite courante)
        if ($this->model->checkAgentConflict(
            (int) $data['agent_id'],
            $data['visit_date'],
            $data['visit_time'],
            (int) $data['duration'],
            $id
        )) {
            return redirect()->back()->withInput()
                ->with('error', 'Cet agent a déjà une visite prévue à ce créneau. Veuillez choisir un autre horaire.');
        }

        $this->model->update($id, $data);
        $this->log->activity('update', 'visits', 'visit', $id, 'Visite modifiée');

        return redirect()->to(base_url('admin/visits/' . $id))
            ->with('success', 'Visite mise à jour avec succès.');
    }

    // ----------------------------------------------------------------
    // AJAX — Mise à jour du statut
    // ----------------------------------------------------------------

    public function updateStatus(int $id)
    {
        $this->requirePermission('visits.edit');

        $visit = $this->model->find($id);
        if (! $visit) {
            return $this->json(['ok' => false, 'error' => 'Visite introuvable'], 404);
        }

        $newStatus = $this->request->getPost('status');
        if (! array_key_exists($newStatus, VisitModel::STATUS_LABELS)) {
            return $this->json(['ok' => false, 'error' => 'Statut invalide'], 400);
        }

        $this->model->update($id, ['status' => $newStatus]);

        // Intégration CRM : visite effectuée → client actif
        if ($newStatus === 'effectuee') {
            (new ClientModel())->update($visit['client_id'], ['status' => 'actif']);
        }

        $this->log->activity('update', 'visits', 'visit', $id, 'Statut → ' . $newStatus);

        return $this->json(['ok' => true, 'status' => $newStatus]);
    }

    // ----------------------------------------------------------------
    // Feedback post-visite
    // ----------------------------------------------------------------

    public function feedback(int $id)
    {
        $this->requirePermission('visits.edit');

        $visit = $this->model->find($id);
        if (! $visit || $visit['status'] !== 'effectuee') {
            return redirect()->to(base_url('admin/visits/' . $id))
                ->with('error', 'Le feedback ne peut être enregistré que pour les visites effectuées.');
        }

        if (! $this->validate(['feedback' => 'required'])) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $fb = $this->request->getPost('feedback');

        $this->model->update($id, [
            'feedback'       => $fb,
            'feedback_notes' => $this->request->getPost('feedback_notes') ?? '',
        ]);

        // Intégration CRM : mise à jour statut client selon feedback
        $clientStatusMap = [
            'interesse'     => 'actif',
            'negociation'   => 'en_attente',
            'pas_interesse' => 'inactif',
        ];
        if (isset($clientStatusMap[$fb])) {
            (new ClientModel())->update($visit['client_id'], ['status' => $clientStatusMap[$fb]]);
        }

        $this->log->activity('update', 'visits', 'visit', $id, 'Feedback ajouté : ' . $fb);

        return redirect()->to(base_url('admin/visits/' . $id))
            ->with('success', 'Feedback enregistré et fiche client mise à jour.');
    }

    // ----------------------------------------------------------------
    // Suppression
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $this->requirePermission('visits.delete');

        $this->model->delete($id);
        $this->log->activity('delete', 'visits', 'visit', $id, 'Visite supprimée');

        return redirect()->to(base_url('admin/visits'))
            ->with('success', 'Visite supprimée.');
    }

    // ----------------------------------------------------------------
    // Helpers privés
    // ----------------------------------------------------------------

    private function buildData(): array
    {
        return [
            'client_id'   => (int) $this->request->getPost('client_id'),
            'property_id' => (int) $this->request->getPost('property_id'),
            'agent_id'    => (int) $this->request->getPost('agent_id'),
            'visit_date'  => $this->request->getPost('visit_date'),
            'visit_time'  => $this->request->getPost('visit_time'),
            'duration'    => (int) ($this->request->getPost('duration') ?? 60),
            'status'      => $this->request->getPost('status') ?? 'planifiee',
            'notes'       => $this->request->getPost('notes') ?? '',
        ];
    }

    private function getClientsList(): array
    {
        $db = \Config\Database::connect();
        return $db->table('clients')
            ->select('id, first_name, last_name, phone')
            ->where('deleted_at IS NULL')
            ->orderBy('last_name', 'ASC')
            ->get()->getResultArray();
    }

    private function getPropertiesList(): array
    {
        $db = \Config\Database::connect();
        return $db->table('properties')
            ->select('id, title, reference, city')
            ->where('deleted_at IS NULL')
            ->orderBy('title', 'ASC')
            ->get()->getResultArray();
    }
}
