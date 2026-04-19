<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyCharacteristicModel;

/**
 * PropertyCharacteristicsController – Gestion du catalogue de caractéristiques.
 *
 * Les valeurs par bien sont stockées dans properties.features (JSON).
 * Ce contrôleur gère uniquement le catalogue (clé, label, icône, type…).
 */
class PropertyCharacteristicsController extends BaseController
{
    private PropertyCharacteristicModel $model;

    public function __construct()
    {
        $this->model = new PropertyCharacteristicModel();
    }

    // ----------------------------------------------------------------
    // Liste
    // ----------------------------------------------------------------

    public function index(): string
    {
        $this->requirePermission('characteristics.view');

        return $this->render('admin/property_characteristics/index', [
            'page_title' => 'Caractéristiques des biens',
            'rows'       => $this->model->getAll(),
        ]);
    }

    // ----------------------------------------------------------------
    // Création
    // ----------------------------------------------------------------

    public function create(): string
    {
        $this->requirePermission('characteristics.create');

        return $this->render('admin/property_characteristics/form', [
            'page_title' => 'Nouvelle caractéristique',
            'row'        => [],
        ]);
    }

    public function store()
    {
        $this->requirePermission('characteristics.create');

        $rules = $this->validationRules();
        if (! $this->validate($rules)) {
            return $this->render('admin/property_characteristics/form', [
                'page_title'  => 'Nouvelle caractéristique',
                'row'         => $this->request->getPost(),
                'errors'      => $this->validator->getErrors(),
            ]);
        }

        $key = $this->request->getPost('key');
        if ($this->model->keyExists($key)) {
            return $this->render('admin/property_characteristics/form', [
                'page_title' => 'Nouvelle caractéristique',
                'row'        => $this->request->getPost(),
                'errors'     => ['key' => "La clé « {$key} » est déjà utilisée."],
            ]);
        }

        $this->model->insert($this->buildData());

        $this->log->activity('create', 'property_characteristics', 'characteristic', 0, "Création : {$key}");

        return redirect()->to(base_url('admin/property-characteristics'))
            ->with('success', 'Caractéristique créée avec succès.');
    }

    // ----------------------------------------------------------------
    // Édition
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        $this->requirePermission('characteristics.edit');

        $row = $this->model->find($id);
        if (! $row) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Caractéristique introuvable.");
        }

        return $this->render('admin/property_characteristics/form', [
            'page_title' => 'Modifier la caractéristique',
            'row'        => $row,
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('characteristics.edit');

        $row = $this->model->find($id);
        if (! $row) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Caractéristique introuvable.");
        }

        $rules = $this->validationRules();
        if (! $this->validate($rules)) {
            return $this->render('admin/property_characteristics/form', [
                'page_title' => 'Modifier la caractéristique',
                'row'        => array_merge($row, $this->request->getPost()),
                'errors'     => $this->validator->getErrors(),
            ]);
        }

        $key = $this->request->getPost('key');
        if ($this->model->keyExists($key, $id)) {
            return $this->render('admin/property_characteristics/form', [
                'page_title' => 'Modifier la caractéristique',
                'row'        => array_merge($row, $this->request->getPost()),
                'errors'     => ['key' => "La clé « {$key} » est déjà utilisée."],
            ]);
        }

        $this->model->update($id, $this->buildData());

        $this->log->activity('update', 'property_characteristics', 'characteristic', $id, "Modification : {$key}");

        return redirect()->to(base_url('admin/property-characteristics'))
            ->with('success', 'Caractéristique mise à jour.');
    }

    // ----------------------------------------------------------------
    // Suppression
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $this->requirePermission('characteristics.delete');

        $row = $this->model->find($id);
        if (! $row) {
            return $this->json(['error' => 'Introuvable'], 404);
        }

        $this->model->delete($id);

        $this->log->activity('delete', 'property_characteristics', 'characteristic', $id, "Suppression : {$row['key']}");

        return redirect()->to(base_url('admin/property-characteristics'))
            ->with('success', 'Caractéristique supprimée.');
    }

    // ----------------------------------------------------------------
    // Activation / désactivation rapide (AJAX)
    // ----------------------------------------------------------------

    public function toggle(int $id)
    {
        $this->requirePermission('characteristics.edit');

        $row = $this->model->find($id);
        if (! $row) {
            return $this->json(['error' => 'Introuvable'], 404);
        }

        $newState = $row['is_active'] ? 0 : 1;
        $this->model->update($id, ['is_active' => $newState]);

        return $this->json(['is_active' => $newState]);
    }

    // ----------------------------------------------------------------
    // Réordonnement drag-and-drop (AJAX)
    // ----------------------------------------------------------------

    public function reorder()
    {
        $this->requirePermission('characteristics.edit');

        $ids = $this->request->getJSON(true)['ids'] ?? [];
        foreach ($ids as $order => $id) {
            $this->model->update((int) $id, ['sort_order' => $order * 10]);
        }

        return $this->json(['success' => true]);
    }

    // ----------------------------------------------------------------
    // API : retourne les caractéristiques actives pour un type de bien (AJAX)
    // ----------------------------------------------------------------

    public function forType(string $type)
    {
        $this->requirePermission('characteristics.view');

        $rows = array_values($this->model->getActive($type));

        // Désérialiser JSON pour la réponse JS
        foreach ($rows as &$r) {
            $r['options']      = $r['options']      ? json_decode($r['options'],      true) : null;
            $r['applies_to']   = $r['applies_to']   ? json_decode($r['applies_to'],   true) : null;
            $r['required_for'] = $r['required_for'] ? json_decode($r['required_for'], true) : null;
        }
        unset($r);

        return $this->json($rows);
    }

    // ----------------------------------------------------------------
    // Internals
    // ----------------------------------------------------------------

    private function validationRules(): array
    {
        return [
            'key'        => 'required|min_length[2]|max_length[80]|alpha_dash',
            'label'      => 'required|min_length[2]|max_length[150]',
            'icon'       => 'required|max_length[60]',
            'type'       => 'required|in_list[boolean,number,text,select]',
            'sort_order' => 'required|integer|greater_than_equal_to[0]',
        ];
    }

    private function buildData(): array
    {
        // applies_to / required_for : cases à cocher → JSON array ou null
        $appliesTo   = $this->request->getPost('applies_to');
        $requiredFor = $this->request->getPost('required_for');

        // options : textarea JSON pour type=select
        $options = null;
        if ($this->request->getPost('type') === 'select') {
            $rawOptions = trim($this->request->getPost('options_text') ?? '');
            if ($rawOptions !== '') {
                // Accepte : ligne par ligne OU tableau JSON
                if ($rawOptions[0] === '[') {
                    $options = $rawOptions; // déjà JSON
                } else {
                    $lines = array_filter(array_map('trim', explode("\n", $rawOptions)));
                    $options = json_encode(array_values($lines));
                }
            }
        }

        return [
            'key'          => strtolower(trim($this->request->getPost('key'))),
            'label'        => $this->request->getPost('label'),
            'icon'         => $this->request->getPost('icon'),
            'type'         => $this->request->getPost('type'),
            'unit'         => $this->request->getPost('unit') ?: null,
            'options'      => $options,
            'applies_to'   => $appliesTo  ? json_encode($appliesTo)   : null,
            'required_for' => $requiredFor ? json_encode($requiredFor) : null,
            'sort_order'   => (int) $this->request->getPost('sort_order'),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }
}
