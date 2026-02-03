<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Workflows extends BaseController
{
    protected $workflowModel;
    protected $instanceModel;
    protected $historyModel;

    public function __construct()
    {
        $this->workflowModel = model('WorkflowModel');
        $this->instanceModel = model('WorkflowInstanceModel');
        $this->historyModel = model('WorkflowHistoryModel');
    }

    /**
     * Pipeline Kanban view
     */
    public function pipeline($entityType = 'property')
    {
        $workflow = $this->workflowModel->getDefaultForEntity($entityType);
        
        if (!$workflow) {
            return redirect()->back()->with('error', 'Aucun workflow configuré pour ce type');
        }

        $stages = json_decode($workflow['stages'], true);
        
        // Get instances grouped by stage
        $pipeline = [];
        foreach ($stages as $stage) {
            $instances = $this->instanceModel->getByStage($workflow['id'], $stage);
            
            // Enrich with entity data
            foreach ($instances as &$instance) {
                $instance['entity_data'] = $this->getEntityData($instance['entity_type'], $instance['entity_id']);
            }
            
            $pipeline[$stage] = $instances;
        }

        $data = [
            'title' => 'Pipeline - ' . ucfirst($entityType),
            'page_title' => 'Pipeline de Vente',
            'workflow' => $workflow,
            'stages' => $stages,
            'pipeline' => $pipeline,
            'entityType' => $entityType
        ];

        return view('admin/workflows/pipeline', $data);
    }

    /**
     * Move card to another stage (AJAX)
     */
    public function moveStage()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $instanceId = $this->request->getPost('instance_id');
        $newStage = $this->request->getPost('new_stage');
        $notes = $this->request->getPost('notes');
        $userId = session()->get('user_id');

        $result = $this->instanceModel->moveToStage($instanceId, $newStage, $userId, $notes);

        return $this->response->setJSON([
            'success' => $result,
            'message' => $result ? 'Étape mise à jour' : 'Erreur lors de la mise à jour'
        ]);
    }

    /**
     * Get entity data
     */
    private function getEntityData($entityType, $entityId)
    {
        switch ($entityType) {
            case 'property':
                $model = model('PropertyModel');
                $data = $model->find($entityId);
                return [
                    'title' => $data['title'] ?? 'N/A',
                    'reference' => $data['reference'] ?? 'N/A',
                    'image' => $data['image'] ?? null,
                    'price' => $data['price'] ?? 0
                ];
            
            case 'client':
                $model = model('ClientModel');
                $data = $model->find($entityId);
                return [
                    'title' => $data['full_name'] ?? 'N/A',
                    'reference' => $data['email'] ?? 'N/A',
                    'image' => null,
                    'price' => null
                ];
            
            case 'transaction':
                $model = model('TransactionModel');
                $data = $model->find($entityId);
                return [
                    'title' => $data['reference'] ?? 'N/A',
                    'reference' => $data['reference'] ?? 'N/A',
                    'image' => null,
                    'price' => $data['amount'] ?? 0
                ];
            
            default:
                return [];
        }
    }

    /**
     * Workflow management index
     */
    public function index()
    {
        $workflows = $this->workflowModel->findAll();

        $data = [
            'title' => 'Gestion des Workflows',
            'page_title' => 'Workflows',
            'workflows' => $workflows
        ];

        return view('admin/workflows/index', $data);
    }

    /**
     * Create workflow
     */
    public function create()
    {
        $data = [
            'title' => 'Créer un Workflow',
            'page_title' => 'Nouveau Workflow'
        ];

        return view('admin/workflows/create', $data);
    }

    /**
     * Store workflow
     */
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[3]|max_length[200]',
            'entity_type' => 'required|in_list[property,client,transaction]',
            'stages' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $stages = $this->request->getPost('stages');
        $stagesArray = array_filter(array_map('trim', explode(',', $stages)));

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'entity_type' => $this->request->getPost('entity_type'),
            'stages' => json_encode($stagesArray),
            'is_default' => $this->request->getPost('is_default') ? 1 : 0,
            'is_active' => 1
        ];

        if ($this->workflowModel->insert($data)) {
            return redirect()->to('/admin/workflows')->with('success', 'Workflow créé avec succès');
        }

        return redirect()->back()->withInput()->with('error', 'Erreur lors de la création du workflow');
    }
}
