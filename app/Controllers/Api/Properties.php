<?php

namespace App\Controllers\Api;

class Properties extends ApiController
{
    protected $propertyModel;

    public function __construct()
    {
        parent::__construct();
        $this->propertyModel = model('PropertyModel');
    }

    /**
     * GET /api/properties
     * List all properties
     */
    public function index()
    {
        // Check authentication
        $user = $this->authenticate();
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        // Check rate limit
        if (!$this->checkRateLimit($user->user_id)) {
            return $this->fail('Limite de requêtes dépassée', 429);
        }

        // Get query parameters
        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        $minPrice = $this->request->getGet('min_price');
        $maxPrice = $this->request->getGet('max_price');

        // Build query
        $builder = $this->propertyModel;

        if ($type) {
            $builder->where('type', $type);
        }
        if ($status) {
            $builder->where('status', $status);
        }
        if ($minPrice) {
            $builder->where('price >=', $minPrice);
        }
        if ($maxPrice) {
            $builder->where('price <=', $maxPrice);
        }

        $properties = $builder->paginate($perPage, 'default', $page);
        $pager = $builder->pager;

        return $this->respond([
            'success' => true,
            'data' => $properties,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $pager->getTotal(),
                'total_pages' => $pager->getPageCount()
            ]
        ]);
    }

    /**
     * GET /api/properties/{id}
     * Get single property
     */
    public function show($id = null)
    {
        $user = $this->authenticate();
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        if (!$this->checkRateLimit($user->user_id)) {
            return $this->fail('Limite de requêtes dépassée', 429);
        }

        $property = $this->propertyModel->find($id);

        if (!$property) {
            return $this->failNotFound('Propriété non trouvée');
        }

        // Get images
        $mediaModel = model('PropertyMediaModel');
        $property['images'] = $mediaModel->getForProperty($id);

        return $this->respond([
            'success' => true,
            'data' => $property
        ]);
    }

    /**
     * POST /api/properties
     * Create new property
     */
    public function create()
    {
        $user = $this->authenticate();
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        if (!$this->checkRateLimit($user->user_id)) {
            return $this->fail('Limite de requêtes dépassée', 429);
        }

        $data = $this->request->getJSON(true);
        $data['user_id'] = $user->user_id;

        if (!$this->propertyModel->insert($data)) {
            return $this->fail([
                'success' => false,
                'errors' => $this->propertyModel->errors()
            ]);
        }

        $propertyId = $this->propertyModel->getInsertID();
        $property = $this->propertyModel->find($propertyId);

        return $this->respondCreated([
            'success' => true,
            'message' => 'Propriété créée avec succès',
            'data' => $property
        ]);
    }

    /**
     * PUT /api/properties/{id}
     * Update property
     */
    public function update($id = null)
    {
        $user = $this->authenticate();
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        if (!$this->checkRateLimit($user->user_id)) {
            return $this->fail('Limite de requêtes dépassée', 429);
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            return $this->failNotFound('Propriété non trouvée');
        }

        $data = $this->request->getJSON(true);

        if (!$this->propertyModel->update($id, $data)) {
            return $this->fail([
                'success' => false,
                'errors' => $this->propertyModel->errors()
            ]);
        }

        $property = $this->propertyModel->find($id);

        return $this->respond([
            'success' => true,
            'message' => 'Propriété mise à jour avec succès',
            'data' => $property
        ]);
    }

    /**
     * DELETE /api/properties/{id}
     * Delete property
     */
    public function delete($id = null)
    {
        $user = $this->authenticate();
        if (!$user) {
            return $this->failUnauthorized('Token invalide ou manquant');
        }

        if (!$this->checkRateLimit($user->user_id)) {
            return $this->fail('Limite de requêtes dépassée', 429);
        }

        $property = $this->propertyModel->find($id);
        if (!$property) {
            return $this->failNotFound('Propriété non trouvée');
        }

        if ($this->propertyModel->delete($id)) {
            return $this->respondDeleted([
                'success' => true,
                'message' => 'Propriété supprimée avec succès'
            ]);
        }

        return $this->fail('Erreur lors de la suppression');
    }
}
