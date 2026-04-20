<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyTypeModel;

class PropertyTypesController extends BaseController
{
    private PropertyTypeModel $model;

    public function __construct()
    {
        $this->model = new PropertyTypeModel();
    }

    // ----------------------------------------------------------------
    // Liste
    // ----------------------------------------------------------------

    public function index(): string
    {
        $this->requirePermission('property_types.view');

        return $this->render('admin/property_types/index', [
            'page_title' => 'Types de bien',
            'rows'       => $this->model->getAll(),
        ]);
    }

    // ----------------------------------------------------------------
    // Création
    // ----------------------------------------------------------------

    public function create(): string
    {
        $this->requirePermission('property_types.create');

        return $this->render('admin/property_types/form', [
            'page_title' => 'Nouveau type de bien',
            'row'        => [],
        ]);
    }

    public function store()
    {
        $this->requirePermission('property_types.create');

        if (! $this->validate($this->validationRules())) {
            return $this->render('admin/property_types/form', [
                'page_title' => 'Nouveau type de bien',
                'row'        => $this->request->getPost(),
                'errors'     => $this->validator->getErrors(),
            ]);
        }

        $slug = $this->resolveSlug($this->request->getPost('slug'), $this->request->getPost('name'));

        if ($this->model->slugExists($slug)) {
            return $this->render('admin/property_types/form', [
                'page_title' => 'Nouveau type de bien',
                'row'        => $this->request->getPost(),
                'errors'     => ['slug' => "Le slug « {$slug} » est déjà utilisé."],
            ]);
        }

        $this->model->insert($this->buildData($slug));

        $this->log->activity('create', 'property_types', 'property_type', 0,
            'Création : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/property-types'))
            ->with('success', 'Type de bien créé avec succès.');
    }

    // ----------------------------------------------------------------
    // Édition
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        $this->requirePermission('property_types.edit');

        $row = $this->model->find($id);
        if (! $row) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Type de bien introuvable.');
        }

        return $this->render('admin/property_types/form', [
            'page_title' => 'Modifier le type de bien',
            'row'        => $row,
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('property_types.edit');

        $row = $this->model->find($id);
        if (! $row) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Type de bien introuvable.');
        }

        if (! $this->validate($this->validationRules())) {
            return $this->render('admin/property_types/form', [
                'page_title' => 'Modifier le type de bien',
                'row'        => array_merge($row, $this->request->getPost()),
                'errors'     => $this->validator->getErrors(),
            ]);
        }

        $slug = $this->resolveSlug($this->request->getPost('slug'), $this->request->getPost('name'));

        if ($this->model->slugExists($slug, $id)) {
            return $this->render('admin/property_types/form', [
                'page_title' => 'Modifier le type de bien',
                'row'        => array_merge($row, $this->request->getPost()),
                'errors'     => ['slug' => "Le slug « {$slug} » est déjà utilisé."],
            ]);
        }

        $this->model->update($id, $this->buildData($slug));

        $this->log->activity('update', 'property_types', 'property_type', $id,
            'Modification : ' . $this->request->getPost('name'));

        return redirect()->to(base_url('admin/property-types'))
            ->with('success', 'Type de bien mis à jour.');
    }

    // ----------------------------------------------------------------
    // Suppression
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $this->requirePermission('property_types.delete');

        $row = $this->model->find($id);
        if (! $row) {
            return redirect()->to(base_url('admin/property-types'))
                ->with('error', 'Type de bien introuvable.');
        }

        $this->model->delete($id);

        $this->log->activity('delete', 'property_types', 'property_type', $id,
            'Suppression : ' . $row['name']);

        return redirect()->to(base_url('admin/property-types'))
            ->with('success', 'Type de bien supprimé.');
    }

    // ----------------------------------------------------------------
    // Activation / désactivation rapide (AJAX)
    // ----------------------------------------------------------------

    public function toggle(int $id)
    {
        $this->requirePermission('property_types.edit');

        $row = $this->model->find($id);
        if (! $row) {
            return $this->json(['error' => 'Introuvable'], 404);
        }

        $newState = $row['is_active'] ? 0 : 1;
        $this->model->update($id, ['is_active' => $newState]);

        return $this->json(['is_active' => $newState]);
    }

    // ----------------------------------------------------------------
    // Helpers privés
    // ----------------------------------------------------------------

    private function validationRules(): array
    {
        return [
            'name' => 'required|max_length[100]',
        ];
    }

    /**
     * Génère un slug à partir du nom si le champ slug est vide.
     */
    private function resolveSlug(string $slugInput, string $name): string
    {
        $slug = trim($slugInput);
        if ($slug === '') {
            $slug = $name;
        }
        // Convertir en slug : minuscules, remplacer espaces/accents
        $slug = mb_strtolower($slug, 'UTF-8');
        // Remplacer les caractères accentués courants
        $accents = [
            'à'=>'a','â'=>'a','ä'=>'a','á'=>'a','ã'=>'a',
            'è'=>'e','é'=>'e','ê'=>'e','ë'=>'e',
            'ì'=>'i','í'=>'i','î'=>'i','ï'=>'i',
            'ò'=>'o','ó'=>'o','ô'=>'o','ö'=>'o','õ'=>'o',
            'ù'=>'u','ú'=>'u','û'=>'u','ü'=>'u',
            'ý'=>'y','ÿ'=>'y','ñ'=>'n','ç'=>'c',
        ];
        $slug = strtr($slug, $accents);
        // Remplacer tout ce qui n'est pas alphanumérique par un tiret
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        return trim($slug, '-');
    }

    private function buildData(string $slug): array
    {
        return [
            'name'        => $this->request->getPost('name'),
            'slug'        => $slug,
            'icon'        => $this->request->getPost('icon') ?: null,
            'description' => $this->request->getPost('description') ?: null,
            'is_active'   => (int) ($this->request->getPost('is_active') ?? 1),
        ];
    }
}
