<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\UserModel;

/**
 * RolesController – Matrice des rôles et permissions.
 */
class RolesController extends BaseController
{
    protected RoleModel      $model;
    protected PermissionModel $permModel;

    public function __construct()
    {
        $this->model     = new RoleModel();
        $this->permModel = new PermissionModel();
    }

    /**
     * Vue principale : matrice des rôles.
     */
    public function index(): string
    {
        $this->requirePermission('roles.view');

        return $this->render('admin/roles/index', [
            'page_title'   => 'Matrice des rôles',
            'roles'        => $this->model->getAllWithPermissions(),
            'permissions'  => $this->permModel->getAllGrouped(),
            'adoption'     => $this->model->getAdoptionStats(),
            'user_stats'   => (new UserModel())->getStats(),
        ]);
    }

    /**
     * Vue dédiée à la matrice (alias pour une URL propre).
     */
    public function matrix(): string
    {
        return $this->index();
    }

    /**
     * Mise à jour des permissions d'un rôle (AJAX/POST).
     */
    public function updatePermissions(int $roleId)
    {
        $this->requirePermission('roles.manage');

        $role = $this->model->find($roleId);
        if (! $role) {
            return $this->json(['error' => 'Rôle introuvable'], 404);
        }

        $permissionIds = $this->request->getPost('permissions') ?? [];
        $success = $this->model->syncPermissions($roleId, $permissionIds);

        if ($success) {
            $this->log->activity(
                'role.permissions.update', 'roles', 'role', $roleId,
                "Mise à jour permissions rôle : {$role['label']}"
            );
            return $this->json(['success' => true, 'message' => 'Permissions mises à jour.']);
        }

        return $this->json(['error' => 'Erreur lors de la mise à jour.'], 500);
    }
}
