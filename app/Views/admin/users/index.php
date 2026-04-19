<!-- LISTE UTILISATEURS -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Gestion des utilisateurs</h4>
        <p class="text-muted mb-0"><?= count($users) ?> utilisateur(s) trouvé(s)</p>
    </div>
    <?php if (in_array('users.create', session()->get('permissions') ?? [])) : ?>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary">
        <i class="bi bi-person-plus-fill me-1"></i>Nouvel utilisateur
    </a>
    <?php endif; ?>
</div>

<!-- Filtres -->
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="role_id" class="form-select form-select-sm">
                    <option value="">Tous les rôles</option>
                    <?php foreach ($roles as $role) : ?>
                    <option value="<?= $role['id'] ?>" <?= $filters['role_id'] == $role['id'] ? 'selected' : '' ?>>
                        <?= esc($role['label']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Tous les statuts</option>
                    <option value="active"   <?= $filters['status'] === 'active'    ? 'selected' : '' ?>>Actif</option>
                    <option value="pending"  <?= $filters['status'] === 'pending'   ? 'selected' : '' ?>>En attente</option>
                    <option value="suspended"<?= $filters['status'] === 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                       value="<?= esc($filters['search']) ?>" placeholder="Rechercher nom, email...">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary w-100">Filtrer</button>
                <a href="<?= base_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Tableau -->
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th>Inscrit le</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)) : ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Aucun utilisateur trouvé.</td></tr>
                    <?php else : ?>
                    <?php foreach ($users as $user) : ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:36px;height:36px;background:<?= esc($user['role_color']) ?>;font-size:.875rem;">
                                    <?= strtoupper(substr($user['first_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <a href="<?= base_url('admin/users/' . $user['id']) ?>" class="fw-semibold text-decoration-none text-dark">
                                        <?= esc($user['first_name']) ?> <?= esc($user['last_name']) ?>
                                    </a>
                                    <?php if ($user['phone']) : ?>
                                    <div class="text-muted" style="font-size:.75rem;"><?= esc($user['phone']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="text-muted small"><?= esc($user['email']) ?></td>
                        <td>
                            <span class="badge text-white" style="background:<?= esc($user['role_color']) ?>;">
                                <?= esc($user['role_label']) ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $sMap = ['active'=>['success','Actif'],  'pending'=>['warning','En attente'], 'suspended'=>['danger','Suspendu']];
                            [$sBadge, $sLabel] = $sMap[$user['status']] ?? ['secondary', $user['status']];
                            ?>
                            <span class="badge bg-<?= $sBadge ?>"><?= $sLabel ?></span>
                        </td>
                        <td class="text-muted small">
                            <?= $user['last_login_at'] ? date('d/m/Y H:i', strtotime($user['last_login_at'])) : '–' ?>
                        </td>
                        <td class="text-muted small"><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td class="text-end">
                            <div class="btn-group btn-group-sm">
                                <a href="<?= base_url('admin/users/' . $user['id'] . '/edit') ?>"
                                   class="btn btn-outline-secondary" title="Modifier">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if ($user['id'] !== session()->get('user_id')) : ?>
                                <button class="btn btn-outline-danger" title="Supprimer"
                                        onclick="confirmDelete(<?= $user['id'] ?>, '<?= esc($user['first_name']) ?>')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal confirmation suppression -->
<form id="deleteForm" method="POST" action="" class="d-none">
    <?= csrf_field() ?>
</form>

<script>
function confirmDelete(id, name) {
    if (confirm('Supprimer l\'utilisateur "' + name + '" ?')) {
        const form = document.getElementById('deleteForm');
        form.action = '/admin/users/' + id + '/delete';
        form.classList.remove('d-none');
        form.submit();
    }
}
</script>
