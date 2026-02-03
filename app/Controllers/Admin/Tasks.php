<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Tasks extends BaseController
{
    protected $taskModel;
    protected $userModel;

    public function __construct()
    {
        $this->taskModel = model('TaskModel');
        $this->userModel = model('UserModel');
    }

    /**
     * Kanban board view
     */
    public function index()
    {
        $userId = $this->request->getGet('user_id') ?: session()->get('user_id');
        
        $data = [
            'title' => 'Gestion des Tâches',
            'page_title' => 'Tâches',
            'tasksByStatus' => $this->taskModel->getByStatus($userId),
            'statistics' => $this->taskModel->getStatistics($userId),
            'users' => $this->userModel->findAll()
        ];

        return view('admin/tasks/kanban', $data);
    }

    /**
     * Create task
     */
    public function create()
    {
        $data = [
            'title' => 'Nouvelle Tâche',
            'page_title' => 'Créer une Tâche',
            'users' => $this->userModel->findAll(),
            'properties' => model('PropertyModel')->findAll(),
            'clients' => model('ClientModel')->findAll()
        ];

        return view('admin/tasks/form', $data);
    }

    /**
     * Store task
     */
    public function store()
    {
        $data = $this->request->getPost();
        $data['created_by'] = session()->get('user_id');

        if (!$this->taskModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->taskModel->errors());
        }

        // Create notification for assigned user
        $notificationModel = model('NotificationModel');
        $notificationModel->insert([
            'user_id' => $data['assigned_to'],
            'type' => 'info',
            'title' => 'Nouvelle tâche assignée',
            'message' => 'Une nouvelle tâche vous a été assignée: ' . $data['title'],
            'link' => '/admin/tasks'
        ]);

        return redirect()->to('admin/tasks')
            ->with('success', 'Tâche créée avec succès');
    }

    /**
     * Update task status (AJAX)
     */
    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $updateData = ['status' => $status];
        
        if ($status === 'completed') {
            $updateData['completed_at'] = date('Y-m-d H:i:s');
        }

        if ($this->taskModel->update($id, $updateData)) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false], 400);
    }

    /**
     * Delete task
     */
    public function delete($id)
    {
        if (!$this->taskModel->delete($id)) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression');
        }

        return redirect()->to('admin/tasks')
            ->with('success', 'Tâche supprimée avec succès');
    }

    /**
     * My tasks list
     */
    public function myTasks()
    {
        $userId = session()->get('user_id');
        
        $data = [
            'title' => 'Mes Tâches',
            'page_title' => 'Mes Tâches',
            'tasks' => $this->taskModel->getUserTasks($userId),
            'overdue' => $this->taskModel->getOverdue($userId),
            'statistics' => $this->taskModel->getStatistics($userId)
        ];

        return view('admin/tasks/my_tasks', $data);
    }
}
