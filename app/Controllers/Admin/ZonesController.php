<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ZoneModel;

/**
 * ZonesController – Gestion des zones géographiques.
 *
 * Hiérarchie : Pays → Région / État (optionnel) → Ville (+ code postal) → Quartier
 */
class ZonesController extends BaseController
{
    protected ZoneModel $model;

    private const VALID_TYPES = ['pays', 'region', 'ville', 'quartier'];

    public function __construct()
    {
        $this->model = new ZoneModel();
    }

    // ── LISTE ────────────────────────────────────────────────────────

    public function index(): string
    {
        $this->requirePermission('zones.view');

        $activeTab = $this->request->getGet('tab') ?? 'pays';
        if (! in_array($activeTab, self::VALID_TYPES)) {
            $activeTab = 'pays';
        }

        return $this->render('admin/zones/index', [
            'page_title'    => 'Zones géographiques',
            'counts'        => $this->model->countByType(),
            'pays_list'     => $this->model->getWithParent(['type' => 'pays']),
            'region_list'   => $this->model->getWithParent(['type' => 'region']),
            'ville_list'    => $this->model->getWithParent(['type' => 'ville']),
            'quartier_list' => $this->model->getWithParent(['type' => 'quartier']),
            'activeTab'     => $activeTab,
            'typeMeta'      => ZoneModel::TYPE_META,
        ]);
    }

    // ── CRÉATION ─────────────────────────────────────────────────────

    public function create(string $type = 'pays'): string
    {
        $this->requirePermission('zones.create');

        if (! in_array($type, self::VALID_TYPES)) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Type de zone invalide.');
        }

        return $this->render('admin/zones/form', [
            'page_title'   => 'Ajouter : ' . ZoneModel::TYPE_META[$type]['label'],
            'zone'         => [],
            'zoneType'     => $type,
            'pays_list'    => $this->model->getByType('pays'),
            'preselect'    => ['pays_id' => null, 'region_id' => null, 'ville_id' => null],
            'regions_list' => [],
            'villes_list'  => [],
        ]);
    }

    // ── STORE ────────────────────────────────────────────────────────

    public function store()
    {
        $this->requirePermission('zones.create');

        $type = $this->request->getPost('type');
        if (! in_array($type, self::VALID_TYPES)) {
            return redirect()->back()->withInput()->with('error', 'Type de zone invalide.');
        }

        if (! $this->validate($this->validationRules($type))) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->resolveParentId($type);

        $id = $this->model->insert([
            'type'      => $type,
            'name'      => trim($this->request->getPost('name')),
            'code'      => $this->request->getPost('code') ? trim($this->request->getPost('code')) : null,
            'parent_id' => $parentId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->log->activity('create', 'zones', 'zone', $id,
            'Zone créée : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/zones/' . $id))
                         ->with('success', ZoneModel::TYPE_META[$type]['label'] . ' créé(e) avec succès.');
    }

    // ── DÉTAIL ───────────────────────────────────────────────────────

    public function show(int $id): string
    {
        $this->requirePermission('zones.view');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        return $this->render('admin/zones/show', [
            'page_title' => $zone['name'],
            'zone'       => $zone,
            'chain'      => $this->model->getParentChain($zone),
            'children'   => $this->model->getChildren($id),
            'typeMeta'   => ZoneModel::TYPE_META,
        ]);
    }

    // ── ÉDITION ──────────────────────────────────────────────────────

    public function edit(int $id): string
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        $chain    = $this->model->getParentChain($zone);
        $paysId   = $chain['pays']   ? (int) $chain['pays']['id']   : null;
        $regionId = $chain['region'] ? (int) $chain['region']['id'] : null;
        $villeId  = $chain['ville']  ? (int) $chain['ville']['id']  : null;

        return $this->render('admin/zones/form', [
            'page_title'   => 'Modifier : ' . $zone['name'],
            'zone'         => $zone,
            'zoneType'     => $zone['type'],
            'pays_list'    => $this->model->getByType('pays'),
            'preselect'    => ['pays_id' => $paysId, 'region_id' => $regionId, 'ville_id' => $villeId],
            'regions_list' => $paysId   ? $this->model->getByParent($paysId)   : [],
            'villes_list'  => $regionId ? $this->model->getByParent($regionId) : ($paysId ? $this->model->getByParent($paysId) : []),
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        $type = $this->request->getPost('type');
        if (! in_array($type, self::VALID_TYPES)) {
            return redirect()->back()->withInput()->with('error', 'Type de zone invalide.');
        }

        if (! $this->validate($this->validationRules($type))) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->resolveParentId($type);

        if ($parentId === $id) {
            return redirect()->back()->withInput()
                             ->with('error', 'Une zone ne peut pas être son propre parent.');
        }

        $this->model->update($id, [
            'name'      => trim($this->request->getPost('name')),
            'code'      => $this->request->getPost('code') ? trim($this->request->getPost('code')) : null,
            'parent_id' => $parentId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->log->activity('update', 'zones', 'zone', $id,
            'Zone modifiée : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/zones/' . $id))
                         ->with('success', 'Zone mise à jour.');
    }

    // ── TOGGLE STATUT ────────────────────────────────────────────────

    public function toggleStatus(int $id)
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable.');
        }

        $this->model->update($id, ['is_active' => $zone['is_active'] ? 0 : 1]);

        return redirect()->back()->with('success', 'Statut mis à jour.');
    }

    // ── SUPPRESSION ──────────────────────────────────────────────────

    public function delete(int $id)
    {
        $this->requirePermission('zones.delete');

        $zone = $this->model->find($id);
        if (! $zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable.');
        }

        $children = $this->model->getChildren($id);
        if (! empty($children)) {
            return redirect()->back()
                             ->with('error', 'Impossible de supprimer : cette zone possède des sous-zones.');
        }

        $this->model->delete($id);

        $this->log->activity('delete', 'zones', 'zone', $id,
            'Zone supprimée : ' . $zone['name']);

        return redirect()->to(base_url('admin/zones?tab=' . $zone['type']))
                         ->with('success', 'Zone supprimée.');
    }

    // ── AJAX ─────────────────────────────────────────────────────────

    public function childrenJson(int $parentId)
    {
        $this->requirePermission('zones.view');
        return $this->json($this->model->getByParent($parentId));
    }

    // ── HELPERS PRIVÉS ───────────────────────────────────────────────

    /**
     * Résout le parent_id selon le type de zone.
     *   pays     → null
     *   region   → pays_id
     *   ville    → region_id si sélectionné, sinon pays_id
     *   quartier → ville_id
     */
    private function resolveParentId(string $type): ?int
    {
        return match ($type) {
            'pays'     => null,
            'region'   => $this->request->getPost('pays_id')
                            ? (int) $this->request->getPost('pays_id')
                            : null,
            'ville'    => $this->request->getPost('region_id')
                            ? (int) $this->request->getPost('region_id')
                            : ($this->request->getPost('pays_id')
                                ? (int) $this->request->getPost('pays_id')
                                : null),
            'quartier' => $this->request->getPost('ville_id')
                            ? (int) $this->request->getPost('ville_id')
                            : null,
            default    => null,
        };
    }

    private function validationRules(string $type): array
    {
        $rules = [
            'type' => 'required|in_list[pays,region,ville,quartier]',
            'name' => 'required|min_length[1]|max_length[150]',
        ];

        if (in_array($type, ['region', 'ville', 'quartier'])) {
            $rules['pays_id'] = 'required|is_natural_no_zero';
        }
        if ($type === 'quartier') {
            $rules['ville_id'] = 'required|is_natural_no_zero';
        }

        return $rules;
    }
}
