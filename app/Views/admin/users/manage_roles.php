<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-tag"></i> <?= esc($page_title) ?>
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-user"></i> <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> 
                <span class="badge badge-info"><?= esc($user['username']) ?></span>
            </p>
        </div>
        <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
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

    <div class="row">
        <!-- Current Roles -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-shield"></i> Rôles Actuels
                    </h6>
                </div>
                <div class="card-body">
                    <?php if (empty($user['roles'])): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> Aucun rôle assigné
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Rôle</th>
                                        <th>Niveau</th>
a                                        <th>Par Défaut</th>
                                        <th>Actif</th>
                                        <th>Assigné le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($user['roles'] as $role): ?>
                                    <tr class="<?= $role['is_active'] == 1 ? 'table-success' : '' ?>">
                                        <td>
                                            <strong><?= esc($role['display_name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><code><?= esc($role['name']) ?></code></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $role['level'] >= 90 ? 'danger' : ($role['level'] >= 50 ? 'warning' : 'info') ?>">
                                                Niveau <?= esc($role['level']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (($role['is_default'] ?? 0) == 1): ?>
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-star"></i> Par défaut
                                                </span>
                                            <?php else: ?>
                                                <form action="<?= base_url('admin/users/set-default-role/' . $user['id']) ?>" method="post" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="role_id" value="<?= $role['role_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Définir comme rôle par défaut">
                                                        <i class="far fa-star"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($role['is_active'] == 1): ?>
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Actif
                                                </span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($role['assigned_at'])) ?>
                                            </small>
                                        </td>
                                        <td class="text-nowrap">
                                            <?php if (count($user['roles']) > 1 && ($role['is_default'] ?? 0) != 1): ?>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        onclick="confirmRemove(<?= $user['id'] ?>, <?= $role['role_id'] ?>, '<?= esc($role['display_name']) ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="button" class="btn btn-sm btn-secondary" disabled 
                                                        title="<?= ($role['is_default'] ?? 0) == 1 ? 'Rôle par défaut protégé' : 'Au moins un rôle requis' ?>">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Add New Role -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle"></i> Ajouter un Rôle
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/users/assign-role/' . $user['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="role_id">Sélectionner un Rôle</label>
                            <select class="form-control" id="role_id" name="role_id" required>
                                <option value="">-- Choisir un rôle --</option>
                                <?php 
                                $assignedRoleIds = array_column($user['roles'] ?? [], 'role_id');
                                foreach ($allRoles as $role): 
                                    if (!in_array($role['id'], $assignedRoleIds)):
                                ?>
                                    <option value="<?= $role['id'] ?>">
                                        <?= esc($role['display_name']) ?> (Niveau <?= $role['level'] ?>)
                                    </option>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="set_active" name="set_active" value="1">
                                <label class="custom-control-label" for="set_active">
                                    Définir comme rôle actif
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Si coché, ce rôle deviendra le rôle actif de l'utilisateur
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox mb-2">
                                <input type="checkbox" class="custom-control-input" id="set_default" name="set_default" value="1">
                                <label class="custom-control-label" for="set_default">
                                    <i class="fas fa-star text-warning"></i> Définir comme rôle par défaut
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Le rôle par défaut est automatiquement chargé lors de la connexion
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> Ajouter le Rôle
                        </button>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-2">
                        <i class="fas fa-info-circle"></i> Fonctionnement
                    </h6>
                    <ul class="small mb-0">
                        <li>Un utilisateur peut avoir <strong>plusieurs rôles</strong></li>
                        <li><strong>Rôle par défaut</strong> <i class="fas fa-star text-warning"></i> : chargé automatiquement au login</li>
                        <li><strong>Rôle actif</strong> <i class="fas fa-check-circle text-success"></i> : utilisé actuellement dans la session</li>
                        <li>L'utilisateur peut <strong>switcher</strong> entre ses rôles</li>
                        <li>Les permissions sont basées sur le <strong>rôle actif</strong></li>
                        <li>Au moins <strong>un rôle</strong> doit être assigné</li>
                        <li>Le <strong>rôle par défaut</strong> ne peut pas être supprimé</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmRemove(userId, roleId, roleName) {
    if (confirm('Êtes-vous sûr de vouloir retirer le rôle "' + roleName + '" à cet utilisateur ?')) {
        window.location.href = '<?= base_url('admin/users/remove-role/') ?>' + userId + '/' + roleId;
    }
}
</script>
<?= $this->endSection() ?>
