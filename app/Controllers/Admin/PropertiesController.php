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
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->model = new PropertyModel();
        $this->db    = \Config\Database::connect();
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
                // Un agent voit tous les biens de son agence (pas seulement les siens)
                $agencyId = (int) session()->get('agency_id');
                if ($agencyId) {
                    $filters['agency_id'] = $agencyId;
                } else {
                    $filters['agent_id'] = $scope['value'];
                }
                break;
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

        // Résoudre l'agency_id à partir de l'agent sélectionné (migration optionnelle)
        $agencyId = null;
        try {
            $agentRow = $this->db->table('users')->select('agency_id')->where('id', (int) $post['agent_id'])->get()->getRowArray();
            $agencyId = $agentRow['agency_id'] ?? null;
        } catch (\Throwable $e) { /* colonne absente */ }

        $data = [
            'reference'        => $this->model->generateReference(),
            'agent_id'         => $post['agent_id'],
            'agency_id'        => $agencyId,
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

        // Historique des modifications du bien
        $history = $this->db->table('property_history ph')
            ->select('ph.*, u.first_name AS user_first_name, u.last_name AS user_last_name')
            ->join('users u', 'u.id = ph.user_id', 'left')
            ->where('ph.property_id', $id)
            ->orderBy('ph.created_at', 'DESC')
            ->limit(20)
            ->get()->getResultArray();

        return $this->render('admin/properties/show', [
            'page_title' => $property['reference'] . ' – ' . $property['title'],
            'property'   => $property,
            'images'     => $property['images'] ?? [],
            'history'    => $history,
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

        // Champs texte/nullable : on accepte la chaîne vide (pour effacer une valeur optionnelle)
        $nullableFields  = ['description','surface','rooms','bedrooms','bathrooms',
                            'floor','total_floors','address','city','zone','latitude','longitude'];
        // Champs obligatoires : on refuse la chaîne vide (on garde l'ancienne valeur)
        $requiredFields  = ['title','type','transaction_type','status','price','agent_id'];

        $data = [];
        foreach (array_merge($requiredFields, $nullableFields) as $f) {
            $posted = $post[$f] ?? null;
            if (in_array($f, $requiredFields, true) && ($posted === null || $posted === '')) {
                $data[$f] = $property[$f];   // garde l'ancienne valeur si champ obligatoire vide
            } else {
                $data[$f] = $posted;          // null ou '' autorisés pour les champs optionnels
            }
        }
        $data['parking']   = isset($post['parking']) ? 1 : 0;
        $data['furnished'] = isset($post['furnished']) ? 1 : 0;
        // Ne réinitialiser features que si des données feat[] sont effectivement envoyées
        $newFeatures = $this->buildFeaturesJson($post);
        $data['features'] = $newFeatures !== null ? $newFeatures : $property['features'];

        // Log changes
        foreach (['status', 'price', 'agent_id'] as $f) {
            if ((string) $data[$f] !== (string) $property[$f]) {
                $this->model->logChange($id, $this->auth->id(), $f, $property[$f], $data[$f]);
            }
        }

        // Si l'agent change, mettre à jour l'agency_id (migration optionnelle)
        if (isset($data['agent_id']) && (string) $data['agent_id'] !== (string) $property['agent_id']) {
            try {
                $agentRow = $this->db->table('users')->select('agency_id')->where('id', (int) $data['agent_id'])->get()->getRowArray();
                $data['agency_id'] = $agentRow['agency_id'] ?? null;
            } catch (\Throwable $e) { /* colonne absente */ }
        }

        $this->model->update($id, $data);

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
