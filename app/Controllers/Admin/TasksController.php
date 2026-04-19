<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TaskModel;
use App\Models\UserModel;

class TasksController extends BaseController
{
    protected TaskModel $model;

    public function __construct()
    {
        $this->model = new TaskModel();
    }

    /** GET /admin/tasks */
    public function index(): string
    {
        // Si la table n'existe pas encore (migration en attente)
        $db = \Config\Database::connect();
        if (! $db->tableExists('tasks')) {
            return $this->render('admin/tasks/index', [
                'page_title' => 'Suivi des tâches',
                'tasks'      => [],
                'stats'      => [],
                'filters'    => [],
                'users'      => [],
                'types'      => TaskModel::TYPES,
                'statuses'   => TaskModel::STATUSES,
                'priorities' => TaskModel::PRIORITIES,
                'migration_pending' => true,
            ]);
        }

        $this->requirePermission('tasks.view');

        $filters = [
            'status'      => $this->request->getGet('status'),
            'type'        => $this->request->getGet('type'),
            'priority'    => $this->request->getGet('priority'),
            'assigned_to' => $this->request->getGet('assigned_to'),
            'search'      => $this->request->getGet('search'),
        ];

        return $this->render('admin/tasks/index', [
            'page_title' => 'Suivi des tâches',
            'tasks'      => $this->model->getFiltered($filters),
            'stats'      => $this->model->getStats(),
            'filters'    => $filters,
            'users'      => (new UserModel())->getWithRole(['status' => 'active']),
            'types'      => TaskModel::TYPES,
            'statuses'   => TaskModel::STATUSES,
            'priorities' => TaskModel::PRIORITIES,
        ]);
    }

    /** GET /admin/tasks/create */
    public function create(): string
    {
        $this->requirePermission('tasks.create');

        return $this->render('admin/tasks/form', [
            'page_title' => 'Nouvelle tâche',
            'task'       => [],
            'users'      => (new UserModel())->getWithRole(['status' => 'active']),
            'types'      => TaskModel::TYPES,
            'statuses'   => TaskModel::STATUSES,
            'priorities' => TaskModel::PRIORITIES,
        ]);
    }

    /** POST /admin/tasks/store */
    public function store()
    {
        $this->requirePermission('tasks.create');

        if (! $this->validate([
            'title'    => 'required|min_length[3]|max_length[255]',
            'type'     => 'required|in_list[bug,feature,improvement,task,question]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'status'   => 'required|in_list[backlog,todo,in_progress,review,done,cancelled]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'reference'   => $this->model->generateReference(),
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
            'status'      => $this->request->getPost('status'),
            'priority'    => $this->request->getPost('priority'),
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'labels'      => $this->request->getPost('labels'),
            'created_by'  => $this->auth->id(),
        ];

        $id = $this->model->insert($data);

        $this->log->activity('tasks.create', 'tasks', 'task', $id, "Création tâche : {$data['title']}");

        return redirect()->to(base_url("admin/tasks/{$id}"))->with('success', 'Tâche créée.');
    }

    /** GET /admin/tasks/:id */
    public function show(int $id): string
    {
        $this->requirePermission('tasks.view');

        $task = $this->model->getWithDetails($id);
        if (! $task) {
            return redirect()->to(base_url('admin/tasks'))->with('error', 'Tâche introuvable.');
        }

        return $this->render('admin/tasks/show', [
            'page_title' => $task['reference'] . ' — ' . $task['title'],
            'task'       => $task,
            'users'      => (new UserModel())->getWithRole(['status' => 'active']),
            'types'      => TaskModel::TYPES,
            'statuses'   => TaskModel::STATUSES,
            'priorities' => TaskModel::PRIORITIES,
        ]);
    }

    /** GET /admin/tasks/:id/edit */
    public function edit(int $id): string
    {
        $this->requirePermission('tasks.edit');

        $task = $this->model->find($id);
        if (! $task) {
            return redirect()->to(base_url('admin/tasks'))->with('error', 'Tâche introuvable.');
        }

        return $this->render('admin/tasks/form', [
            'page_title' => 'Modifier — ' . $task['title'],
            'task'       => $task,
            'users'      => (new UserModel())->getWithRole(['status' => 'active']),
            'types'      => TaskModel::TYPES,
            'statuses'   => TaskModel::STATUSES,
            'priorities' => TaskModel::PRIORITIES,
        ]);
    }

    /** POST /admin/tasks/:id/update */
    public function update(int $id)
    {
        $this->requirePermission('tasks.edit');

        if (! $this->validate([
            'title'    => 'required|min_length[3]|max_length[255]',
            'type'     => 'required|in_list[bug,feature,improvement,task,question]',
            'priority' => 'required|in_list[low,medium,high,critical]',
            'status'   => 'required|in_list[backlog,todo,in_progress,review,done,cancelled]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->model->update($id, [
            'title'       => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'type'        => $this->request->getPost('type'),
            'status'      => $this->request->getPost('status'),
            'priority'    => $this->request->getPost('priority'),
            'assigned_to' => $this->request->getPost('assigned_to') ?: null,
            'due_date'    => $this->request->getPost('due_date') ?: null,
            'labels'      => $this->request->getPost('labels'),
        ]);

        $this->log->activity('tasks.update', 'tasks', 'task', $id, 'Mise à jour tâche #' . $id);

        return redirect()->to(base_url("admin/tasks/{$id}"))->with('success', 'Tâche mise à jour.');
    }

    /** POST /admin/tasks/:id/status — AJAX */
    public function updateStatus(int $id)
    {
        $this->requirePermission('tasks.edit');

        $status = $this->request->getPost('status');
        if (! array_key_exists($status, TaskModel::STATUSES)) {
            return $this->json(['error' => 'Statut invalide'], 400);
        }

        $this->model->update($id, ['status' => $status]);

        return $this->json(['success' => true]);
    }

    /** POST /admin/tasks/:id/comment */
    public function addComment(int $id)
    {
        $this->requirePermission('tasks.view');

        $content = trim($this->request->getPost('content') ?? '');
        if ($content === '') {
            return redirect()->back()->with('error', 'Commentaire vide.');
        }

        $this->model->addComment($id, $this->auth->id(), $content);

        return redirect()->to(base_url("admin/tasks/{$id}") . '#comments')->with('success', 'Commentaire ajouté.');
    }

    /** POST /admin/tasks/:id/delete */
    public function delete(int $id)
    {
        $this->requirePermission('tasks.delete');

        $this->model->delete($id);
        $this->log->activity('tasks.delete', 'tasks', 'task', $id, 'Suppression tâche #' . $id);

        return redirect()->to(base_url('admin/tasks'))->with('success', 'Tâche supprimée.');
    }
}
