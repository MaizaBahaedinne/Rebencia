<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-users-cog me-2"></i>Matrice des Permissions (Type SAP)
        </h1>
        <div>
            <form action="<?= base_url('admin/roles/sync-permissions') ?>" method="post" class="d-inline">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success me-2">
                    <i class="fas fa-sync me-2"></i>Synchroniser Modules
                </button>
            </form>
            <a href="<?= base_url('admin/roles') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Matrice Dynamique:</strong> Cette matrice s'adapte automatiquement aux modules du système. 
        Utilisez le bouton "Synchroniser Modules" pour détecter les nouveaux contrôleurs.
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-table me-2"></i>Matrice Rôles × Permissions (<?= count($roles) ?> rôles)
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                <table class="table table-bordered table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th style="min-width: 200px; position: sticky; left: 0; z-index: 10; background: #f8f9fc;">
                                <i class="fas fa-folder-open me-2"></i>Module / Permission
                            </th>
                            <?php foreach ($roles as $role): ?>
                                <th class="text-center" style="min-width: 120px;">
                                    <div class="d-flex flex-column align-items-center">
                                        <strong><?= esc($role['display_name']) ?></strong>
                                        <small class="text-muted">(Niveau <?= $role['level'] ?>)</small>
                                        <a href="<?= base_url('admin/roles/edit/' . $role['id']) ?>" 
                                           class="btn btn-sm btn-outline-primary mt-1">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    </div>
                                </th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($modules as $moduleName => $permissions): ?>
                            <!-- En-tête du module -->
                            <tr class="table-primary">
                                <td colspan="<?= count($roles) + 1 ?>" style="position: sticky; left: 0; z-index: 5;">
                                    <strong><i class="fas fa-layer-group me-2"></i><?= strtoupper($moduleName) ?></strong>
                                </td>
                            </tr>
                            
                            <!-- Permissions du module -->
                            <?php foreach ($permissions as $permission): ?>
                                <tr>
                                    <td style="position: sticky; left: 0; z-index: 5; background: white;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-key text-warning me-2"></i>
                                            <div>
                                                <div><strong><?= esc($permission['description']) ?></strong></div>
                                                <small class="text-muted"><?= esc($permission['name']) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <?php foreach ($roles as $role): ?>
                                        <td class="text-center align-middle">
                                            <?php 
                                            $hasPermission = isset($permissionsMatrix[$role['id']][$permission['id']]);
                                            $perm = $hasPermission ? $permissionsMatrix[$role['id']][$permission['id']] : null;
                                            ?>
                                            
                                            <?php if ($hasPermission): ?>
                                                <div class="btn-group-vertical btn-group-sm">
                                                    <?php if ($perm['can_create']): ?>
                                                        <span class="badge bg-success mb-1">C</span>
                                                    <?php endif ?>
                                                    <?php if ($perm['can_read']): ?>
                                                        <span class="badge bg-info mb-1">R</span>
                                                    <?php endif ?>
                                                    <?php if ($perm['can_update']): ?>
                                                        <span class="badge bg-warning mb-1">U</span>
                                                    <?php endif ?>
                                                    <?php if ($perm['can_delete']): ?>
                                                        <span class="badge bg-danger mb-1">D</span>
                                                    <?php endif ?>
                                                    <?php if ($perm['can_validate']): ?>
                                                        <span class="badge bg-primary mb-1">V</span>
                                                    <?php endif ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif ?>
                                        </td>
                                    <?php endforeach ?>
                                </tr>
                            <?php endforeach ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-light">
            <div class="row">
                <div class="col-md-12">
                    <strong>Légende des Actions:</strong>
                    <span class="badge bg-success ms-2">C</span> Create (Créer)
                    <span class="badge bg-info ms-2">R</span> Read (Lire)
                    <span class="badge bg-warning ms-2">U</span> Update (Modifier)
                    <span class="badge bg-danger ms-2">D</span> Delete (Supprimer)
                    <span class="badge bg-primary ms-2">V</span> Validate (Valider)
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Modules</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($modules) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Permissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $totalPerms = 0;
                                foreach ($modules as $perms) {
                                    $totalPerms += count($perms);
                                }
                                echo $totalPerms;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Rôles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($roles) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Affectations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php 
                                $totalAssignments = 0;
                                foreach ($permissionsMatrix as $rolePerms) {
                                    $totalAssignments += count($rolePerms);
                                }
                                echo $totalAssignments;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.sticky-top {
    position: sticky;
    top: 0;
    z-index: 9;
}
</style>

<?= $this->endSection() ?>
