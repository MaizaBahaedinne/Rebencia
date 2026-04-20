<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PropertyModel;
use App\Models\PropertyCharacteristicModel;
use App\Models\PropertyTypeModel;
use App\Models\UserModel;
use App\Models\ZoneModel;

/**
 * PropertiesController – CRUD Biens Immobiliers.
 */
class PropertiesController extends BaseController
{
    protected PropertyModel $model;

    public function __construct()
    {
        $this->model = new PropertyModel();
    }

    /** Liste des biens. */
    public function index(): string
    {
        $this->requirePermission('properties.view');

        $filters = [
            'status'   => $this->request->getGet('status'),
            'type'     => $this->request->getGet('type'),
            'agent_id' => $this->request->getGet('agent_id'),
            'city'     => $this->request->getGet('city'),
            'search'   => $this->request->getGet('search'),
            'page'     => $this->request->getGet('page') ?? 1,
        ];

        // Expert : ne voit que ses biens
        if ($this->auth->hasRole('expert')) {
            $filters['agent_id'] = $this->auth->id();
        }

        $result = $this->model->getFiltered($filters);

        return $this->render('admin/properties/index', [
            'page_title'    => 'Biens Immobiliers',
            'result'        => $result,
            'filters'       => $filters,
            'agents'        => (new UserModel())->getWithRole(['status' => 'active']),
            'propertyTypes' => (new PropertyTypeModel())->getActive(),
        ]);
    }

    /** Formulaire création. */
    public function create(): string
    {
        $this->requirePermission('properties.create');
        $zoneModel = new ZoneModel();
        $charModel = new PropertyCharacteristicModel();

        return $this->render('admin/properties/form', [
            'page_title'      => 'Nouveau bien',
            'property'        => [],
            'agents'          => (new UserModel())->getWithRole(['status' => 'active']),
            'pays_list'       => $zoneModel->getByType('pays'),
            'characteristics' => $charModel->getActive(),
            'propertyTypes'   => (new PropertyTypeModel())->getActive(),
        ]);
    }

    /** Enregistrement. */
    public function store()
    {
        $this->requirePermission('properties.create');

        $rules = [
            'title'            => 'required|min_length[5]|max_length[255]',
            'type'             => 'required',
            'transaction_type' => 'required',
            'price'            => 'required|decimal',
            'agent_id'         => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $post = $this->request->getPost();
        $data = [
            'reference'        => $this->model->generateReference(),
            'agent_id'         => $post['agent_id'],
            'title'            => $post['title'],
            'description'      => $post['description'] ?? '',
            'type'             => $post['type'],
            'transaction_type' => $post['transaction_type'],
            'status'           => $post['status'] ?? 'available',
            'price'            => $post['price'],
            'surface'          => $post['surface'] ?? null,
            'rooms'            => $post['rooms'] ?? null,
            'bedrooms'         => $post['bedrooms'] ?? null,
            'bathrooms'        => $post['bathrooms'] ?? null,
            'floor'            => $post['floor'] ?? null,
            'total_floors'     => $post['total_floors'] ?? null,
            'parking'          => isset($post['parking']) ? 1 : 0,
            'furnished'        => isset($post['furnished']) ? 1 : 0,
            'address'          => $post['address'] ?? '',
            'city'             => $post['city'] ?? '',
            'zone'             => $post['zone'] ?? '',
            'latitude'         => $post['latitude'] ?? null,
            'longitude'        => $post['longitude'] ?? null,
            'features'         => $this->buildFeaturesJson($post),
        ];

        $this->model->insert($data);
        $id = $this->model->getInsertID();

        // Upload images si présentes
        $this->handleImageUploads($id);

        $this->log->activity('property.create', 'properties', 'property', $id, 'Création bien : ' . $data['title']);

        return redirect()->to('/admin/properties/' . $id)->with('success', 'Bien créé avec succès.');
    }

    /** Détail d'un bien. */
    public function show(int $id): string
    {
        $this->requirePermission('properties.view');
        $property = $this->findOrFail($id);

        return $this->render('admin/properties/show', [
            'page_title' => $property['reference'] . ' – ' . $property['title'],
            'property'   => $property,
        ]);
    }

    /** Formulaire édition. */
    public function edit(int $id): string
    {
        $this->requirePermission('properties.edit');
        $property  = $this->findOrFail($id);
        $zoneModel = new ZoneModel();

        // Tenter de retrouver la ville par son nom pour pré-sélectionner la cascade
        $villePreselect = null;
        if (! empty($property['city'])) {
            $found = $zoneModel->where('type', 'ville')->like('name', $property['city'], 'none')->first();
            $villePreselect = $found ? $found : null;
        }

        $charModel = new PropertyCharacteristicModel();

        return $this->render('admin/properties/form', [
            'page_title'      => 'Modifier – ' . $property['title'],
            'property'        => $property,
            'agents'          => (new UserModel())->getWithRole(['status' => 'active']),
            'pays_list'       => $zoneModel->getByType('pays'),
            'ville_preselect' => $villePreselect,
            'characteristics' => $charModel->getActive($property['type'] ?? null),
            'propertyTypes'   => (new PropertyTypeModel())->getActive(),
        ]);
    }

    /** Mise à jour. */
    public function update(int $id)
    {
        $this->requirePermission('properties.edit');
        $property = $this->findOrFail($id);

        $post   = $this->request->getPost();
        $fields = ['title','description','type','transaction_type','status','price',
                   'surface','rooms','bedrooms','bathrooms','floor','total_floors',
                   'address','city','zone','latitude','longitude','agent_id'];

        $data = [];
        foreach ($fields as $f) {
            $data[$f] = $post[$f] ?? $property[$f];
        }
        $data['parking']   = isset($post['parking']) ? 1 : 0;
        $data['furnished'] = isset($post['furnished']) ? 1 : 0;
        $data['features']  = $this->buildFeaturesJson($post);

        // Log changes
        foreach (['status', 'price', 'agent_id'] as $f) {
            if ((string) $data[$f] !== (string) $property[$f]) {
                $this->model->logChange($id, $this->auth->id(), $f, $property[$f], $data[$f]);
            }
        }

        $this->model->update($id, $data);
        $this->handleImageUploads($id);

        $this->log->activity('property.update', 'properties', 'property', $id, 'Modification bien');

        return redirect()->to('/admin/properties/' . $id)->with('success', 'Bien mis à jour.');
    }

    /** Publication / dépublication. */
    public function publish(int $id)
    {
        $this->requirePermission('properties.publish');

        $property = $this->model->find($id);
        if (! $property) {
            return $this->json(['error' => 'Introuvable'], 404);
        }

        $newState = $property['is_published'] ? 0 : 1;
        $this->model->update($id, [
            'is_published' => $newState,
            'published_at' => $newState ? date('Y-m-d H:i:s') : null,
            'published_by' => $newState ? $this->auth->id() : null,
        ]);

        $this->log->activity('property.publish', 'properties', 'property', $id,
            $newState ? 'Bien publié' : 'Bien dépublié');

        return $this->json(['success' => true, 'is_published' => $newState]);
    }

    /** Suppression (soft). */
    public function delete(int $id)
    {
        $this->requirePermission('properties.delete');

        $this->model->delete($id);
        $this->log->activity('property.delete', 'properties', 'property', $id, 'Suppression bien');

        return redirect()->to('/admin/properties')->with('success', 'Bien supprimé.');
    }

    /** Upload image additionnelle. */
    public function uploadImage(int $id)
    {
        $this->requirePermission('properties.edit');

        $file = $this->request->getFile('image');
        if (! $file || ! $file->isValid()) {
            return $this->json(['error' => 'Fichier invalide'], 422);
        }

        $path = $this->saveImage($file, $id);
        if (! $path) {
            return $this->json(['error' => 'Erreur upload'], 500);
        }

        return $this->json(['success' => true, 'path' => $path]);
    }

    /** Suppression d'une image. */
    public function deleteImage(int $imageId)
    {
        $this->requirePermission('properties.edit');

        $img = $this->db->table('property_images')->where('id', $imageId)->get()->getRowArray();
        if ($img) {
            @unlink(FCPATH . $img['path']);
            $this->db->table('property_images')->where('id', $imageId)->delete();
        }

        return $this->json(['success' => true]);
    }

    // --------------------------------------------------------
    // Méthodes privées
    // --------------------------------------------------------

    private function findOrFail(int $id): array
    {
        $p = $this->model->findWithImages($id);
        if (! $p) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien #{$id} introuvable");
        }
        return $p;
    }

    /**
     * Construit le JSON `features` depuis le formulaire.
     * Chaque caractéristique poste ses valeurs sous `feat[key]`.
     */
    private function buildFeaturesJson(array $post): ?string
    {
        $raw = $post['feat'] ?? null;
        if (empty($raw) || ! is_array($raw)) {
            return null;
        }
        $features = [];
        foreach ($raw as $key => $value) {
            // Ignorer les clés vides ou valeurs vides
            $key = preg_replace('/[^a-z0-9_]/', '', strtolower((string) $key));
            if ($key === '') continue;
            // boolean : la valeur est '1' si cochée, absente sinon
            $features[$key] = $value;
        }
        return ! empty($features) ? json_encode($features) : null;
    }

    private function handleImageUploads(int $propertyId): void
    {
        $files = $this->request->getFiles('images');
        if (empty($files) || empty($files['images'])) {
            return;
        }

        foreach ($files['images'] as $file) {
            if ($file->isValid() && ! $file->hasMoved()) {
                $path = $this->saveImage($file, $propertyId);
            }
        }
    }

    private function saveImage(\CodeIgniter\HTTP\Files\UploadedFile $file, int $propertyId): ?string
    {
        $allowed = ['image/jpeg', 'image/png', 'image/webp'];
        if (! in_array($file->getMimeType(), $allowed)) {
            return null;
        }

        $name      = $file->getRandomName();
        $uploadDir = FCPATH . 'uploads/properties/' . $propertyId . '/';
        @mkdir($uploadDir, 0755, true);
        $file->move($uploadDir, $name);

        $path = 'uploads/properties/' . $propertyId . '/' . $name;

        // Vérifier s'il existe déjà une image primaire
        $hasPrimary = $this->db->table('property_images')
            ->where('property_id', $propertyId)
            ->where('is_primary', 1)
            ->countAllResults();

        $this->db->table('property_images')->insert([
            'property_id' => $propertyId,
            'filename'    => $name,
            'path'        => $path,
            'is_primary'  => $hasPrimary === 0 ? 1 : 0,
            'sort_order'  => 0,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return $path;
    }
}
