<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield me-2"></i>Modifier Rôle
        </h1>
        <div>
            <a href="<?= base_url('admin/roles/matrix') ?>" class="btn btn-info me-2">
                <i class="fas fa-table me-2"></i>Voir la Matrice
            </a>
            <a href="<?= base_url('admin/roles') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <h6><i class="fas fa-exclamation-circle me-2"></i>Erreurs de validation:</h6>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <form action="<?= base_url('admin/roles/update/' . $role['id']) ?>" method="post">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-4">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations du Rôle</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom Technique <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= old('name', $role['name']) ?>" required>
                            <small class="text-muted">Sans espaces, minuscules, underscores autorisés</small>
                        </div>

                        <div class="mb-3">
                            <label for="display_name" class="form-label">Nom d'Affichage <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="display_name" name="display_name" 
                                   value="<?= old('display_name', $role['display_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="level" class="form-label">Niveau Hiérarchique <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="level" name="level" 
                                   value="<?= old('level', $role['level']) ?>" min="1" max="100" required>
                            <small class="text-muted">1=Plus haut niveau, 100=Plus bas niveau</small>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $role['description'] ?? '') ?></textarea>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                        <a href="<?= base_url('admin/roles') ?>" class="btn btn-outline-secondary w-100 mb-2">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="button" class="btn btn-outline-danger w-100" onclick="confirmDelete()">
                            <i class="fas fa-trash me-2"></i>Supprimer le Rôle
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-shield-alt me-2"></i>Permissions CRUD (Create, Read, Update, Delete, Validate)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Cochez les actions autorisées pour chaque permission. <strong>C=Create, R=Read, U=Update, D=Delete, V=Validate</strong>
                        </div>

                        <div class="mb-3">
                            <button type="button" class="btn btn-sm btn-success" onclick="selectAllPerms()">
                                <i class="fas fa-check-double me-1"></i>Tout Sélectionner
                            </button>
                            <button type="button" class="btn btn-sm btn-warning" onclick="deselectAllPerms()">
                                <i class="fas fa-times me-1"></i>Tout Désélectionner
                            </button>
                        </div>

                        <?php foreach ($modules as $moduleName => $permissions): ?>
                            <div class="mb-4">
                                <div class="card">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">
                                            <i class="fas fa-folder-open text-primary me-2"></i>
                                            <strong><?= strtoupper($moduleName) ?></strong>
                                        </h6>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-success" onclick="selectModulePerms('<?= $moduleName ?>')">
                                                <i class="fas fa-check-double"></i> Tout
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectModulePerms('<?= $moduleName ?>')">
                                                <i class="fas fa-times"></i> Rien
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body" data-module="<?= $moduleName ?>">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 40%;">Permission</th>
                                                        <th class="text-center">Create</th>
                                                        <th class="text-center">Read</th>
                                                        <th class="text-center">Update</th>
                                                        <th class="text-center">Delete</th>
                                                        <th class="text-center">Validate</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($permissions as $permission): ?>
                                                        <?php 
                                                        $permId = $permission['id'];
                                                        $hasPerm = isset($rolePermissions[$permId]);
                                                        $perm = $hasPerm ? $rolePermissions[$permId] : null;
                                                        ?>
                                                        <tr>
                                                            <td>
                                                                <strong><?= esc($permission['description']) ?></strong>
                                                                <br><small class="text-muted"><?= esc($permission['name']) ?></small>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" 
                                                                       name="permissions[<?= $permId ?>][create]" 
                                                                       class="form-check-input perm-checkbox"
                                                                       <?= ($hasPerm && $perm['can_create']) ? 'checked' : '' ?>>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" 
                                                                       name="permissions[<?= $permId ?>][read]" 
                                                                       class="form-check-input perm-checkbox"
                                                                       <?= ($hasPerm && $perm['can_read']) ? 'checked' : '' ?>>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" 
                                                                       name="permissions[<?= $permId ?>][update]" 
                                                                       class="form-check-input perm-checkbox"
                                                                       <?= ($hasPerm && $perm['can_update']) ? 'checked' : '' ?>>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" 
                                                                       name="permissions[<?= $permId ?>][delete]" 
                                                                       class="form-check-input perm-checkbox"
                                                                       <?= ($hasPerm && $perm['can_delete']) ? 'checked' : '' ?>>
                                                            </td>
                                                            <td class="text-center">
                                                                <input type="checkbox" 
                                                                       name="permissions[<?= $permId ?>][validate]" 
                                                                       class="form-check-input perm-checkbox"
                                                                       <?= ($hasPerm && $perm['can_validate']) ? 'checked' : '' ?>>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function selectAllPerms() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
}

function deselectAllPerms() {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
}

function selectModulePerms(moduleName) {
    const moduleCard = document.querySelector(`[data-module="${moduleName}"]`);
    if (moduleCard) {
        moduleCard.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = true);
    }
}

function deselectModulePerms(moduleName) {
    const moduleCard = document.querySelector(`[data-module="${moduleName}"]`);
    if (moduleCard) {
        moduleCard.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = false);
    }
}

function confirmDelete() {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce rôle ?')) {
        window.location.href = '<?= base_url('admin/roles/delete/' . $role['id']) ?>';
    }
}
</script>

<?= $this->endSection() ?>
