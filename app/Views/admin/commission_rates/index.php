<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-percentage"></i> Gestion des Taux de Commission</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Taux de Commission</li>
                </ol>
            </nav>
        </div>
        <div class="btn-group">
            <a href="<?= base_url('admin/commission-rates/export') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-download me-2"></i>Exporter CSV
            </a>
            <?php if (session()->get('role_level') == 100): ?>
                <a href="<?= base_url('admin/commission-rates/reset-defaults') ?>" class="btn btn-outline-danger"
                   onclick="return confirm('Réinitialiser TOUS les taux aux valeurs par défaut ? Cette action est irréversible.')">
                    <i class="fas fa-redo me-2"></i>Valeurs Par Défaut
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Alertes -->
    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Taux Par Défaut (Admin) -->
    <?php if (session()->get('role_level') >= 100): ?>
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-gear me-2"></i>Taux Par Défaut du Système</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= base_url('admin/commission-rates/save-defaults') ?>" class="row g-3">
                <?= csrf_field() ?>
                
                <div class="col-md-6">
                    <label for="agent_commission_share_sale" class="form-label">
                        Split Ventes (Agent) - Par Défaut
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="agent_commission_share_sale" 
                               name="agent_commission_share_sale" step="0.01" min="0" max="100"
                               value="<?= number_format($defaults['agent_commission_share_sale'] ?? 50, 2) ?>" 
                               required>
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">Utilisé pour tout nouvel utilisateur (ventes)</small>
                </div>

                <div class="col-md-6">
                    <label for="agent_commission_share_rent" class="form-label">
                        Split Locations (Agent) - Par Défaut
                    </label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="agent_commission_share_rent" 
                               name="agent_commission_share_rent" step="0.01" min="0" max="100"
                               value="<?= number_format($defaults['agent_commission_share_rent'] ?? 50, 2) ?>" 
                               required>
                        <span class="input-group-text">%</span>
                    </div>
                    <small class="text-muted">Utilisé pour tout nouvel utilisateur (locations)</small>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Sauvegarder Taux Par Défaut
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Filtres -->
    <div class="card shadow-sm mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="filterStatus" class="form-label">Statut</label>
                    <select class="form-select" id="filterStatus" name="status">
                        <option value="">-- Tous --</option>
                        <option value="active" <?= $filterStatus === 'active' ? 'selected' : '' ?>>Actif</option>
                        <option value="inactive" <?= $filterStatus === 'inactive' ? 'selected' : '' ?>>Inactif</option>
                        <option value="suspended" <?= $filterStatus === 'suspended' ? 'selected' : '' ?>>Suspendu</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterRole" class="form-label">Rôle</label>
                    <select class="form-select" id="filterRole" name="role">
                        <option value="">-- Tous --</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= esc($role['display_name']) ?>" <?= $filterRole === $role['display_name'] ? 'selected' : '' ?>>
                                <?= esc($role['display_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterAgency" class="form-label">Agence</label>
                    <select class="form-select" id="filterAgency" name="agency">
                        <option value="">-- Toutes --</option>
                        <?php foreach ($agencies as $agency): ?>
                            <option value="<?= esc($agency['name']) ?>" <?= $filterAgency === $agency['name'] ? 'selected' : '' ?>>
                                <?= esc($agency['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users"></i> 
                <?= count($users) ?> Utilisateur<?= count($users) > 1 ? 's' : '' ?>
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 30%">Utilisateur</th>
                            <th style="width: 15%">Rôle</th>
                            <th style="width: 15%">Agence</th>
                            <th style="width: 12%" class="text-center">Split Ventes Agent</th>
                            <th style="width: 12%" class="text-center">Split Loc. Agent</th>
                            <th style="width: 8%" class="text-center">Excep.</th>
                            <th style="width: 8%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div>
                                    <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= esc($user['email']) ?></small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= esc($user['role_name'] ?? '-') ?></span>
                            </td>
                            <td>
                                <small><?= esc($user['agency_name'] ?? '-') ?></small>
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center commission-rate" 
                                       data-user-id="<?= $user['id'] ?>" data-field="agent_commission_share_sale"
                                       value="<?= number_format($user['agent_commission_share_sale'] ?? 50, 2) ?>" 
                                       min="0" max="100" step="0.01"
                                       style="width: 90px; margin: 0 auto;"
                                       title="% de la commission ventes allant à l'agent">
                            </td>
                            <td class="text-center">
                                <input type="number" class="form-control form-control-sm text-center commission-rate" 
                                       data-user-id="<?= $user['id'] ?>" data-field="agent_commission_share_rent"
                                       value="<?= number_format($user['agent_commission_share_rent'] ?? 50, 2) ?>" 
                                       min="0" max="100" step="0.01"
                                       style="width: 90px; margin: 0 auto;"
                                       title="% de la commission locations allant à l'agent">
                            </td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input commission-exception" type="checkbox" 
                                           data-user-id="<?= $user['id'] ?>"
                                           <?= $user['is_commission_exceptional'] ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i> Éditer
                                </a>
                            </td>
                        </tr>
                        <?php if ($user['is_commission_exceptional'] && $user['commission_exceptional_note']): ?>
                        <tr class="table-warning">
                            <td colspan="7">
                                <small class="text-muted">
                                    <strong><i class="fas fa-star"></i> Exception:</strong> 
                                    <?= esc($user['commission_exceptional_note']) ?>
                                </small>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (empty($users)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-search fa-3x mb-3"></i>
                    <p>Aucun utilisateur trouvé</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="updateToast" class="toast" role="alert">
        <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2"></i>
            <strong class="me-auto">Succès</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body" id="toastMessage">
            Taux mis à jour avec succès
        </div>
    </div>
</div>

<script>
// Mettre à jour les taux en temps réel
document.querySelectorAll('.commission-rate').forEach(input => {
    input.addEventListener('change', updateRate);
    input.addEventListener('blur', updateRate);
});

document.querySelectorAll('.commission-exception').forEach(checkbox => {
    checkbox.addEventListener('change', updateException);
});

function updateRate(e) {
    const input = e.target;
    const userId = input.dataset.userId;
    const field = input.dataset.field;
    const value = parseFloat(input.value);

    // Validation
    if (isNaN(value) || value < 0 || value > 100) {
        showToast('error', 'Le pourcentage doit être entre 0 et 100');
        input.focus();
        return;
    }

    // Requête AJAX
    fetch('<?= base_url('admin/commission-rates/update-rate') ?>/' + userId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            field: field,
            value: value,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', data.message);
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            setTimeout(() => {
                input.classList.remove('is-valid');
            }, 2000);
        } else {
            showToast('error', data.message);
            input.classList.add('is-invalid');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la mise à jour');
        input.classList.add('is-invalid');
    });
}

function updateException(e) {
    const checkbox = e.target;
    const userId = checkbox.dataset.userId;
    const value = checkbox.checked ? 1 : 0;

    fetch('<?= base_url('admin/commission-rates/update-rate') ?>/' + userId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            field: 'is_commission_exceptional',
            value: value,
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('success', 'Statut exceptionnel mis à jour');
            checkbox.classList.remove('is-invalid');
            checkbox.classList.add('is-valid');
            setTimeout(() => {
                checkbox.classList.remove('is-valid');
            }, 2000);
        } else {
            showToast('error', data.message);
            checkbox.checked = !checkbox.checked;
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showToast('error', 'Erreur lors de la mise à jour');
        checkbox.checked = !checkbox.checked;
    });
}

function showToast(type, message) {
    const toast = document.getElementById('updateToast');
    const header = toast.querySelector('.toast-header');
    const msgElement = document.getElementById('toastMessage');

    msgElement.textContent = message;

    if (type === 'success') {
        header.classList.remove('bg-danger');
        header.classList.add('bg-success');
    } else {
        header.classList.remove('bg-success');
        header.classList.add('bg-danger');
    }

    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
}
</script>

<style>
.commission-rate, .commission-exception {
    transition: all 0.2s ease;
}

.commission-rate.is-valid,
.commission-exception.is-valid {
    border-color: #198754 !important;
    background-color: #f8fff9 !important;
}

.commission-rate.is-invalid,
.commission-exception.is-invalid {
    border-color: #dc3545 !important;
    background-color: #fff8f8 !important;
}
</style>

<?= $this->endSection() ?>
