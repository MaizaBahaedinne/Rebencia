<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ZoneModel;

/**
 * ZonesController – Gestion des zones géographiques.
 * Hiérarchie : Pays → Région → Ville → Code postal
 */
class ZonesController extends BaseController
{
    protected ZoneModel $model;

    /** Types valides et le type attendu pour leur parent. */
    private const PARENT_TYPE_MAP = [
        'region'      => 'pays',
        'ville'       => 'region',
        'code_postal' => 'ville',
    ];

    public function __construct()
    {
        $this->model = new ZoneModel();
    }

    // --------------------------------------------------------
    // LISTE
    // --------------------------------------------------------

    public function index(): string
    {
        $this->requirePermission('zones.view');

        $filters = [
            'type'      => $this->request->getGet('type'),
            'parent_id' => $this->request->getGet('parent_id'),
            'search'    => $this->request->getGet('search'),
        ];

        return $this->render('admin/zones/index', [
            'page_title' => 'Gestion des zones',
            'zones'      => $this->model->getWithParent($filters),
            'counts'     => $this->model->countByType(),
            'filters'    => $filters,
        ]);
    }

    // --------------------------------------------------------
    // CRÉATION
    // --------------------------------------------------------

    public function create(): string
    {
        $this->requirePermission('zones.create');

        return $this->render('admin/zones/form', [
            'page_title' => 'Nouvelle zone',
            'zone'       => [],
            'parents'    => $this->model->getWithParent(),
        ]);
    }

    public function store()
    {
        $this->requirePermission('zones.create');

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $type     = $this->request->getPost('type');
        $parentId = $this->request->getPost('parent_id') ?: null;

        if ($parentId !== null) {
            $parentId = (int) $parentId;
            if (! $this->isParentTypeValid($type, $parentId)) {
                return redirect()->back()->withInput()
                                 ->with('error', 'Le parent sélectionné est incompatible avec le type de zone.');
            }
        }

        if ($type === 'pays' && $parentId !== null) {
            return redirect()->back()->withInput()
                             ->with('error', 'Un pays ne peut pas avoir de parent.');
        }

        $id = $this->model->insert([
            'type'      => $type,
            'name'      => $this->request->getPost('name'),
            'code'      => $this->request->getPost('code') ?: null,
            'parent_id' => $parentId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->log->activity('create', 'zones', 'zone', $id,
            'Zone créée : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/zones/' . $id))
                         ->with('success', 'Zone créée avec succès.');
    }

    // --------------------------------------------------------
    // DÉTAIL
    // --------------------------------------------------------

    public function show(int $id): string
    {
        $this->requirePermission('zones.view');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        $parent   = $zone['parent_id'] ? $this->model->find((int) $zone['parent_id']) : null;
        $children = $this->model->getChildren($id);

        return $this->render('admin/zones/show', [
            'page_title' => 'Zone : ' . $zone['name'],
            'zone'       => $zone,
            'parent'     => $parent,
            'children'   => $children,
        ]);
    }

    // --------------------------------------------------------
    // ÉDITION
    // --------------------------------------------------------

    public function edit(int $id): string
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        return $this->render('admin/zones/form', [
            'page_title' => 'Modifier : ' . $zone['name'],
            'zone'       => $zone,
            'parents'    => $this->model->getWithParent(),
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Zone introuvable.');
        }

        if (! $this->validate($this->validationRules())) {
            return redirect()->back()->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $type     = $this->request->getPost('type');
        $parentId = $this->request->getPost('parent_id') ?: null;

        if ($parentId !== null) {
            $parentId = (int) $parentId;

            if ($parentId === $id) {
                return redirect()->back()->withInput()
                                 ->with('error', 'Une zone ne peut pas être son propre parent.');
            }

            if (! $this->isParentTypeValid($type, $parentId)) {
                return redirect()->back()->withInput()
                                 ->with('error', 'Le parent sélectionné est incompatible avec le type de zone.');
            }
        }

        if ($type === 'pays' && $parentId !== null) {
            return redirect()->back()->withInput()
                             ->with('error', 'Un pays ne peut pas avoir de parent.');
        }

        $this->model->update($id, [
            'type'      => $type,
            'name'      => $this->request->getPost('name'),
            'code'      => $this->request->getPost('code') ?: null,
            'parent_id' => $parentId,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        $this->log->activity('update', 'zones', 'zone', $id,
            'Zone modifiée : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/zones/' . $id))
                         ->with('success', 'Zone mise à jour.');
    }

    // --------------------------------------------------------
    // TOGGLE STATUT
    // --------------------------------------------------------

    public function toggleStatus(int $id)
    {
        $this->requirePermission('zones.edit');

        $zone = $this->model->find($id);
        if (! $zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable.');
        }

        $this->model->update($id, ['is_active' => $zone['is_active'] ? 0 : 1]);

        return redirect()->to(base_url('admin/zones'))
                         ->with('success', 'Statut de la zone mis à jour.');
    }

    // --------------------------------------------------------
    // SUPPRESSION
    // --------------------------------------------------------

    public function delete(int $id)
    {
        $this->requirePermission('zones.delete');

        $zone = $this->model->find($id);
        if (! $zone) {
            return redirect()->to(base_url('admin/zones'))->with('error', 'Zone introuvable.');
        }

        $children = $this->model->getChildren($id);
        if (! empty($children)) {
            return redirect()->to(base_url('admin/zones'))
                             ->with('error', 'Impossible de supprimer une zone ayant des sous-zones actives.');
        }

        $this->model->delete($id);

        $this->log->activity('delete', 'zones', 'zone', $id,
            'Zone supprimée : ' . $zone['name']);

        return redirect()->to(base_url('admin/zones'))
                         ->with('success', 'Zone supprimée.');
    }

    // --------------------------------------------------------
    // Méthodes privées
    // --------------------------------------------------------

    private function validationRules(): array
    {
        return [
            'type' => 'required|in_list[pays,region,ville,code_postal]',
            'name' => 'required|min_length[1]|max_length[150]',
            'code' => 'permit_empty|max_length[20]',
        ];
    }

    /**
     * Vérifie que le parent a le bon type selon la hiérarchie définie.
     */
    private function isParentTypeValid(string $type, int $parentId): bool
    {
        if (! isset(self::PARENT_TYPE_MAP[$type])) {
            return false; // 'pays' n'a pas de parent valide
        }

        $parent = $this->model->find($parentId);
        return $parent !== null && $parent['type'] === self::PARENT_TYPE_MAP[$type];
    }
}
