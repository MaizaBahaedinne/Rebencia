<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ClientModel;
use App\Models\UserModel;
use App\Models\ZoneModel;
use App\Models\PropertyTypeModel;

class ClientsController extends BaseController
{
    private ClientModel $model;

    public function __construct()
    {
        $this->model = new ClientModel();
    }

    // ----------------------------------------------------------------
    // Liste
    // ----------------------------------------------------------------

    public function index(): string
    {
        $this->requirePermission('clients.view');

        $filters = [
            'client_type' => $this->request->getGet('client_type'),
            'status'      => $this->request->getGet('status'),
            'assigned_to' => $this->request->getGet('assigned_to'),
            'search'      => $this->request->getGet('search'),
            'page'        => $this->request->getGet('page') ?? 1,
        ];

        return $this->render('admin/clients/index', [
            'page_title'  => 'Clients',
            'result'      => $this->model->getFiltered($filters),
            'filters'     => $filters,
            'agents'      => (new UserModel())->getWithRole(['status' => 'active']),
            'typeCounts'  => $this->model->countByType(),
            'typeLabels'  => ClientModel::TYPE_LABELS,
            'statusLabels'=> ClientModel::STATUS_LABELS,
        ]);
    }

    // ----------------------------------------------------------------
    // Création
    // ----------------------------------------------------------------

    public function create(): string
    {
        $this->requirePermission('clients.create');

        return $this->render('admin/clients/form', [
            'page_title'       => 'Nouveau client',
            'client'           => [],
            'agents'           => (new UserModel())->getWithRole(['status' => 'active']),
            'pays_list'        => (new ZoneModel())->getByType('pays'),
            'propertyTypes'    => (new PropertyTypeModel())->getActive(),
            'typeLabels'       => ClientModel::TYPE_LABELS,
            'statusLabels'     => ClientModel::STATUS_LABELS,
            'sourceLabels'     => ClientModel::SOURCE_LABELS,
            'demandTypeLabels' => ClientModel::DEMAND_TYPE_LABELS,
            'urgencyLabels'    => ClientModel::URGENCY_LABELS,
            'budgetFlexLabels' => ClientModel::BUDGET_FLEXIBILITY_LABELS,
            'orientationLabels'=> ClientModel::ORIENTATION_LABELS,
            'featuresCatalog'  => ClientModel::FEATURES_CATALOG,
            'selectedPropTypes'=> [],
            'selectedZones'    => [],
            'selectedFeatures' => [],
        ]);
    }

    public function store()
    {
        $this->requirePermission('clients.create');

        if (! $this->validate([
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'phone'      => 'required|min_length[6]|max_length[30]',
            'client_type'=> 'required',
        ])) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $id = $this->model->insert($this->buildData());
        $newId = (int) $this->model->getInsertID();

        // Pivot : types de biens
        $propTypes = $this->request->getPost('search_prop_types') ?? [];
        $this->model->savePivotPropertyTypes($newId, array_map('intval', (array) $propTypes));

        // Pivot : zones recherchées
        $zoneIds = $this->request->getPost('search_zones') ?? [];
        $this->model->savePivotZones($newId, array_map('intval', (array) $zoneIds));

        // Pivot : caractéristiques
        $featReq = $this->request->getPost('features_obligatoire') ?? [];
        $featOpt = $this->request->getPost('features_optionnel')   ?? [];
        $this->model->savePivotFeatures($newId, (array) $featReq, (array) $featOpt);

        $this->log->activity('create', 'clients', 'client', $newId,
            'Création client : ' . $this->request->getPost('first_name') . ' ' . $this->request->getPost('last_name'));

        return redirect()->to(base_url('admin/clients/' . $newId))
            ->with('success', 'Client créé avec succès.');
    }

    // ----------------------------------------------------------------
    // Détail
    // ----------------------------------------------------------------

    public function show(int $id): string
    {
        $this->requirePermission('clients.view');

        $client = $this->model->findWithRelations($id);
        if (! $client) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Client introuvable.');
        }

        return $this->render('admin/clients/show', [
            'page_title'  => $client['first_name'] . ' ' . $client['last_name'],
            'client'      => $client,
            'typeLabels'  => ClientModel::TYPE_LABELS,
            'statusLabels'=> ClientModel::STATUS_LABELS,
            'sourceLabels'=> ClientModel::SOURCE_LABELS,
        ]);
    }

    // ----------------------------------------------------------------
    // Édition
    // ----------------------------------------------------------------

    public function edit(int $id): string
    {
        $this->requirePermission('clients.edit');

        $client = $this->model->findWithRelations($id);
        if (! $client) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Client introuvable.');
        }

        return $this->render('admin/clients/form', [
            'page_title'       => 'Modifier – ' . $client['first_name'] . ' ' . $client['last_name'],
            'client'           => $client,
            'agents'           => (new UserModel())->getWithRole(['status' => 'active']),
            'pays_list'        => (new ZoneModel())->getByType('pays'),
            'propertyTypes'    => (new PropertyTypeModel())->getActive(),
            'typeLabels'       => ClientModel::TYPE_LABELS,
            'statusLabels'     => ClientModel::STATUS_LABELS,
            'sourceLabels'     => ClientModel::SOURCE_LABELS,
            'demandTypeLabels' => ClientModel::DEMAND_TYPE_LABELS,
            'urgencyLabels'    => ClientModel::URGENCY_LABELS,
            'budgetFlexLabels' => ClientModel::BUDGET_FLEXIBILITY_LABELS,
            'orientationLabels'=> ClientModel::ORIENTATION_LABELS,
            'featuresCatalog'  => ClientModel::FEATURES_CATALOG,
            'selectedPropTypes'=> $this->model->getPivotPropertyTypes($id),
            'selectedZones'    => $this->model->getPivotZones($id),
            'selectedFeatures' => $this->model->getPivotFeatures($id),
        ]);
    }

    public function update(int $id)
    {
        $this->requirePermission('clients.edit');

        $client = $this->model->find($id);
        if (! $client) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Client introuvable.');
        }

        if (! $this->validate([
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name'  => 'required|min_length[2]|max_length[100]',
            'phone'      => 'required|min_length[6]|max_length[30]',
            'client_type'=> 'required',
        ])) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, $this->buildData());

        // Pivot : types de biens
        $propTypes = $this->request->getPost('search_prop_types') ?? [];
        $this->model->savePivotPropertyTypes($id, array_map('intval', (array) $propTypes));

        // Pivot : zones recherchées
        $zoneIds = $this->request->getPost('search_zones') ?? [];
        $this->model->savePivotZones($id, array_map('intval', (array) $zoneIds));

        // Pivot : caractéristiques
        $featReq = $this->request->getPost('features_obligatoire') ?? [];
        $featOpt = $this->request->getPost('features_optionnel')   ?? [];
        $this->model->savePivotFeatures($id, (array) $featReq, (array) $featOpt);

        $this->log->activity('update', 'clients', 'client', $id, 'Modification client');

        return redirect()->to(base_url('admin/clients/' . $id))
            ->with('success', 'Client mis à jour.');
    }

    // ----------------------------------------------------------------
    // Suppression
    // ----------------------------------------------------------------

    public function delete(int $id)
    {
        $this->requirePermission('clients.delete');

        $client = $this->model->find($id);
        if (! $client) {
            return redirect()->to(base_url('admin/clients'))
                ->with('error', 'Client introuvable.');
        }

        $this->model->delete($id);

        $this->log->activity('delete', 'clients', 'client', $id,
            'Suppression client : ' . $client['first_name'] . ' ' . $client['last_name']);

        return redirect()->to(base_url('admin/clients'))
            ->with('success', 'Client supprimé.');
    }

    // ----------------------------------------------------------------
    // AJAX : régions par pays
    // ----------------------------------------------------------------

    public function regionsByPays(int $paysId)
    {
        $zones = (new ZoneModel())->getByParent($paysId);
        return $this->json(array_values($zones));
    }

    public function villesByRegion(int $regionId)
    {
        $zones = (new ZoneModel())->getByParent($regionId);
        return $this->json(array_values($zones));
    }

    // ----------------------------------------------------------------
    // AJAX : zones search autocomplete
    // ----------------------------------------------------------------

    public function zonesSearch()
    {
        $q      = trim($this->request->getGet('q') ?? '');
        $type   = $this->request->getGet('type');
        $result = [];

        if (strlen($q) >= 2) {
            $db      = \Config\Database::connect();
            $builder = $db->table('zones')
                ->select('id, name, type')
                ->where('deleted_at', null)
                ->like('name', $q)
                ->limit(20);

            if ($type) {
                $builder->where('type', $type);
            }

            $typeLabels = [
                'pays'      => 'Pays',
                'region'    => 'Gouvernorat',
                'ville'     => 'Ville / Délégation',
                'quartier'  => 'Quartier',
            ];

            foreach ($builder->get()->getResultArray() as $row) {
                $result[] = [
                    'id'         => (int) $row['id'],
                    'name'       => $row['name'],
                    'type'       => $row['type'],
                    'type_label' => $typeLabels[$row['type']] ?? $row['type'],
                ];
            }
        }

        return $this->json($result);
    }

    // ----------------------------------------------------------------
    // Helper privé
    // ----------------------------------------------------------------

    private function buildData(): array
    {
        $post        = $this->request->getPost();
        $clientType  = $post['client_type'] ?? 'acheteur';

        $data = [
            'client_type'  => $clientType,
            'first_name'   => $post['first_name'],
            'last_name'    => $post['last_name'],
            'phone'        => $post['phone'],
            'email'        => $post['email'] ?: null,
            'profession'   => $post['profession'] ?: null,
            'company'      => $post['company'] ?: null,
            'address'      => $post['address'] ?: null,
            'zone_pays_id'   => $post['zone_pays_id']   ?: null,
            'zone_region_id' => $post['zone_region_id'] ?: null,
            'zone_ville_id'  => $post['zone_ville_id']  ?: null,
            'postal_code'  => $post['postal_code'] ?: null,
            'property_type_id' => $post['property_type_id'] ?: null,
            'status'       => $post['status'] ?? 'nouveau',
            'assigned_to'  => $post['assigned_to'] ?: null,
            'source'       => $post['source'] ?? 'autre',
            'notes'        => $post['notes'] ?: null,
            // Profil de demande
            'demand_type'       => $post['demand_type']       ?: null,
            'urgency'           => $post['urgency']           ?: null,
            'budget_flexibility'=> $post['budget_flexibility'] ?: null,
            'surface_min'       => $post['surface_min']       ?: null,
            'surface_max'       => $post['surface_max']       ?: null,
            'rooms_min'         => $post['rooms_min']         ?: null,
            'bedrooms_min'      => $post['bedrooms_min']      ?: null,
            'floor_preferred'   => $post['floor_preferred']   ?: null,
            'has_elevator'      => isset($post['has_elevator']) ? 1 : 0,
            'orientations'      => ! empty($post['orientations'])
                                    ? json_encode($post['orientations'])
                                    : null,
        ];

        // Champs spécifiques par type
        if (in_array($clientType, ['acheteur', 'locataire', 'investisseur'])) {
            $data['budget_min']   = $post['budget_min']   ?: null;
            $data['budget_max']   = $post['budget_max']   ?: null;
            $data['desired_zone'] = $post['desired_zone'] ?: null;
        }

        if ($clientType === 'proprietaire') {
            $data['owner_location'] = $post['owner_location'] ?: null;
            $data['desired_price']  = $post['desired_price']  ?: null;
        }

        return $data;
    }
}
