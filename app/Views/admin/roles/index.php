<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield"></i> Gestion des Rôles
        </h1>
        <div>
            <a href="<?= base_url('admin/roles/matrix') ?>" class="btn btn-info">
                <i class="fas fa-table"></i> Matrice des Permissions
            </a>
            <a href="<?= base_url('admin/roles/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nouveau Rôle
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Rôles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($roles) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Rôles Actifs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= count(array_filter($roles, fn($r) => ($r['status'] ?? 'active') === 'active')) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Permissions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalPermissions ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-key fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Utilisateurs</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalUsers ?? 0 ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Rôles</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="rolesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom Technique</th>
                            <th>Nom d'Affichage</th>
                            <th>Niveau</th>
                            <th>Utilisateurs</th>
                            <th>Permissions</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                        <tr>
                            <td><?= esc($role['id']) ?></td>
                            <td>
                                <code><?= esc($role['name']) ?></code>
                            </td>
                            <td>
                                <strong><?= esc($role['display_name'] ?? $role['name']) ?></strong>
                            </td>
                            <td>
                                <span class="badge badge-<?= $role['level'] >= 90 ? 'danger' : ($role['level'] >= 50 ? 'warning' : 'info') ?>">
                                    Niveau <?= esc($role['level']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-primary">
                                    <?= esc($role['user_count'] ?? 0) ?> utilisateurs
                                </span>
                            </td>
                            <td>
                                <?php 
                                $permCount = $role['permission_count'] ?? 0;
                                $color = $permCount > 20 ? 'success' : ($permCount > 10 ? 'info' : 'secondary');
                                ?>
                                <span class="badge badge-<?= $color ?>">
                                    <?= $permCount ?> permissions
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= esc(substr($role['description'] ?? 'Aucune description', 0, 50)) ?>
                                    <?= strlen($role['description'] ?? '') > 50 ? '...' : '' ?>
                                </small>
                            </td>
                            <td>
                                <?php
                                $status = $role['status'] ?? 'active';
                                $statusClass = $status === 'active' ? 'success' : 'secondary';
                                $statusText = $status === 'active' ? 'Actif' : 'Inactif';
                                ?>
                                <span class="badge badge-<?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                            <td class="text-nowrap">
                                <a href="<?= base_url('admin/roles/edit/' . $role['id']) ?>" 
                                   class="btn btn-sm btn-primary" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($role['name'] !== 'super_admin' && $role['name'] !== 'admin'): ?>
                                <button type="button" class="btn btn-sm btn-danger" 
                                        onclick="confirmDelete(<?= $role['id'] ?>, '<?= esc($role['display_name'] ?? $role['name']) ?>')" 
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <?php else: ?>
                                <button type="button" class="btn btn-sm btn-secondary" disabled title="Rôle système protégé">
                                    <i class="fas fa-lock"></i>
                                </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Role Hierarchy Guide -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Hiérarchie des Niveaux
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-left-danger mb-3">
                                <div class="card-body py-2">
                                    <h6 class="font-weight-bold text-danger mb-1">Niveau 90-100</h6>
                                    <p class="small mb-0 text-muted">Super Administrateur - Accès total</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-warning mb-3">
                                <div class="card-body py-2">
                                    <h6 class="font-weight-bold text-warning mb-1">Niveau 50-89</h6>
                                    <p class="small mb-0 text-muted">Administrateur / Manager</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-info mb-3">
                                <div class="card-body py-2">
                                    <h6 class="font-weight-bold text-info mb-1">Niveau 20-49</h6>
                                    <p class="small mb-0 text-muted">Agent / Employé</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-left-secondary mb-3">
                                <div class="card-body py-2">
                                    <h6 class="font-weight-bold text-secondary mb-1">Niveau 1-19</h6>
                                    <p class="small mb-0 text-muted">Consultant / Invité</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#rolesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json"
        },
        "order": [[3, "desc"]], // Sort by level descending
        "pageLength": 25
    });
});

function confirmDelete(roleId, roleName) {
    if (confirm('Êtes-vous sûr de vouloir supprimer le rôle "' + roleName + '" ?\n\nAttention: Les utilisateurs associés à ce rôle perdront leurs permissions.')) {
        window.location.href = '<?= base_url('admin/roles/delete/') ?>' + roleId;
    }
}
</script>
<?= $this->endSection() ?>
