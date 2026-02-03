<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Objectives extends BaseController
{
    protected $objectiveModel;

    public function __construct()
    {
        $this->objectiveModel = model('ObjectiveModel');
    }

    /**
     * Objectives dashboard
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $currentObjective = $this->objectiveModel->getCurrentObjective($userId);

        // Update achievements
        if ($currentObjective) {
            $this->objectiveModel->updateAchievements($userId);
            $currentObjective = $this->objectiveModel->getCurrentObjective($userId);
        }

        $data = [
            'title' => 'Mes Objectifs',
            'page_title' => 'Objectifs & KPI',
            'objective' => $currentObjective,
            'progress' => $currentObjective ? $this->objectiveModel->getProgress($currentObjective) : null,
            'leaderboard' => $this->objectiveModel->getLeaderboard()
        ];

        return view('admin/objectives/index', $data);
    }

    /**
     * Set objectives (Manager only)
     */
    public function setObjectives()
    {
        if (!in_array(session()->get('role'), ['admin', 'manager'])) {
            return redirect()->back()->with('error', 'Accès refusé');
        }

        $data = [
            'title' => 'Définir Objectifs',
            'page_title' => 'Gestion Objectifs',
            'users' => model('UserModel')->where('role', 'agent')->findAll(),
            'objectives' => $this->objectiveModel->where('year', date('Y'))
                                                 ->where('month', date('m'))
                                                 ->findAll()
        ];

        return view('admin/objectives/set', $data);
    }

    /**
     * Save objectives
     */
    public function save()
    {
        $data = $this->request->getPost();

        $objectiveData = [
            'user_id' => $data['user_id'],
            'period' => 'monthly',
            'year' => $data['year'],
            'month' => $data['month'],
            'target_properties' => $data['target_properties'],
            'target_clients' => $data['target_clients'],
            'target_deals' => $data['target_deals'],
            'target_revenue' => $data['target_revenue'],
            'status' => 'active'
        ];

        // Check if exists
        $existing = $this->objectiveModel->where('user_id', $data['user_id'])
                                        ->where('year', $data['year'])
                                        ->where('month', $data['month'])
                                        ->first();

        if ($existing) {
            $this->objectiveModel->update($existing['id'], $objectiveData);
        } else {
            $this->objectiveModel->insert($objectiveData);
        }

        return redirect()->back()->with('success', 'Objectifs enregistrés');
    }
}
