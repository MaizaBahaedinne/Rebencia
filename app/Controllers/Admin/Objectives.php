<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Objectives extends BaseController
{
    protected $objectiveModel;
    protected $userModel;
    protected $agencyModel;

    public function __construct()
    {
        $this->objectiveModel = model('ObjectiveModel');
        $this->userModel = model('UserModel');
        $this->agencyModel = model('AgencyModel');
    }

    public function index()
    {
        // Get filters
        $filters = [
            'type' => $this->request->getGet('type'),
            'user_id' => $this->request->getGet('user_id'),
            'agency_id' => $this->request->getGet('agency_id'),
            'period' => $this->request->getGet('period'),
            'status' => $this->request->getGet('status'),
        ];

        // Get objectives with details
        $objectives = $this->objectiveModel->getObjectivesWithDetails($filters);

        // Calculate progress for each objective
        foreach ($objectives as &$objective) {
            $objective['progress'] = $this->objectiveModel->calculateProgress($objective);
        }

        // Get users and agencies for filters
        $users = $this->userModel->where('status', 'active')->findAll();
        $agencies = $this->agencyModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Gestion des Objectifs',
            'objectives' => $objectives,
            'users' => $users,
            'agencies' => $agencies,
            'filters' => $filters
        ];

        return view('admin/objectives/index', $data);
    }

    public function create()
    {
        $users = $this->userModel->where('status', 'active')->findAll();
        $agencies = $this->agencyModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Créer un Objectif',
            'users' => $users,
            'agencies' => $agencies
        ];

        return view('admin/objectives/create', $data);
    }

    public function store()
    {
        $rules = [
            'type' => 'required|in_list[personal,agency]',
            'period' => 'required|regex_match[/^\d{4}-\d{2}$/]',
        ];

        if ($this->request->getPost('type') === 'personal') {
            $rules['user_id'] = 'required|integer';
        } else {
            $rules['agency_id'] = 'required|integer';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'type' => $this->request->getPost('type'),
            'period' => $this->request->getPost('period'),
            'revenue_target' => $this->request->getPost('revenue_target') ?: 0,
            'new_contacts_target' => $this->request->getPost('new_contacts_target') ?: 0,
            'properties_rent_target' => $this->request->getPost('properties_rent_target') ?: 0,
            'properties_sale_target' => $this->request->getPost('properties_sale_target') ?: 0,
            'transactions_target' => $this->request->getPost('transactions_target') ?: 0,
            'notes' => $this->request->getPost('notes'),
            'status' => 'active',
            'created_by' => session()->get('user_id')
        ];

        if ($this->request->getPost('type') === 'personal') {
            $data['user_id'] = $this->request->getPost('user_id');
            $data['agency_id'] = null;
        } else {
            $data['agency_id'] = $this->request->getPost('agency_id');
            $data['user_id'] = null;
        }

        // Check if objective already exists
        $existing = null;
        if ($data['type'] === 'personal' && $data['user_id']) {
            $existing = $this->objectiveModel->getByUserAndPeriod($data['user_id'], $data['period']);
        } elseif ($data['type'] === 'agency' && $data['agency_id']) {
            $existing = $this->objectiveModel->getByAgencyAndPeriod($data['agency_id'], $data['period']);
        }

        if ($existing) {
            return redirect()->back()->withInput()
                ->with('error', 'Un objectif existe déjà pour cette période');
        }

        if ($this->objectiveModel->insert($data)) {
            return redirect()->to('/admin/objectives')
                ->with('success', 'Objectif créé avec succès');
        }

        return redirect()->back()->withInput()
            ->with('error', 'Erreur lors de la création');
    }

    public function edit($id)
    {
        $objective = $this->objectiveModel->find($id);

        if (!$objective) {
            return redirect()->to('/admin/objectives')->with('error', 'Objectif non trouvé');
        }

        $users = $this->userModel->where('status', 'active')->findAll();
        $agencies = $this->agencyModel->where('status', 'active')->findAll();

        $data = [
            'title' => 'Modifier l\'Objectif',
            'objective' => $objective,
            'users' => $users,
            'agencies' => $agencies
        ];

        return view('admin/objectives/edit', $data);
    }

    public function update($id)
    {
        $objective = $this->objectiveModel->find($id);

        if (!$objective) {
            return redirect()->to('/admin/objectives')->with('error', 'Objectif non trouvé');
        }

        $data = [
            'revenue_target' => $this->request->getPost('revenue_target') ?: 0,
            'new_contacts_target' => $this->request->getPost('new_contacts_target') ?: 0,
            'properties_rent_target' => $this->request->getPost('properties_rent_target') ?: 0,
            'properties_sale_target' => $this->request->getPost('properties_sale_target') ?: 0,
            'transactions_target' => $this->request->getPost('transactions_target') ?: 0,
            'notes' => $this->request->getPost('notes'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->objectiveModel->update($id, $data)) {
            return redirect()->to('/admin/objectives')
                ->with('success', 'Objectif mis à jour avec succès');
        }

        return redirect()->back()->withInput()
            ->with('error', 'Erreur lors de la mise à jour');
    }

    public function delete($id)
    {
        if ($this->objectiveModel->delete($id)) {
            return redirect()->to('/admin/objectives')
                ->with('success', 'Objectif supprimé avec succès');
        }

        return redirect()->to('/admin/objectives')
            ->with('error', 'Erreur lors de la suppression');
    }

    public function refresh($id)
    {
        if ($this->objectiveModel->updateAchievedValues($id)) {
            return redirect()->back()
                ->with('success', 'Valeurs actualisées avec succès');
        }

        return redirect()->back()
            ->with('error', 'Erreur lors de l\'actualisation');
    }

    public function refreshAll()
    {
        $objectives = $this->objectiveModel->where('status', 'active')->findAll();
        
        $updated = 0;
        foreach ($objectives as $objective) {
            if ($this->objectiveModel->updateAchievedValues($objective['id'])) {
                $updated++;
            }
        }

        return redirect()->back()
            ->with('success', "$updated objectifs actualisés avec succès");
    }
}
