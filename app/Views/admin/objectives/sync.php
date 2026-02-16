<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0"><i class="fas fa-sync me-2"></i>Synchronisation Objectifs</h4>
    <div>
        <form method="get" class="d-inline-flex gap-2">
            <select name="period" class="form-select form-select-sm" style="width: 150px;" onchange="this.form.submit()">
                <?php foreach ($periods as $p => $label): ?>
                    <option value="<?= $p ?>" <?= $period === $p ? 'selected' : '' ?>>
                        <?= $label ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
        <button class="btn btn-sm btn-primary ms-2" id="sync-all-btn">
            <i class="fas fa-sync-alt"></i> Syncer Tous
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <?php if (empty($objectives)): ?>
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle"></i> Aucun objectif actif pour cette période
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Objectif</th>
                            <th class="text-center">CA HT</th>
                            <th class="text-center">Biens Louer</th>
                            <th class="text-center">Biens Vendre</th>
                            <th class="text-center">Contacts</th>
                            <th class="text-center">Transactions</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($objectives as $obj): ?>
                        <tr>
                            <td>
                                <div class="fw-bold">
                                    <?= $obj['type'] === 'personal' 
                                        ? esc($obj['user_first_name'] . ' ' . $obj['user_last_name'])
                                        : esc($obj['agency_name']) ?>
                                </div>
                                <small class="text-muted">
                                    <?= $obj['type'] === 'personal' ? 'Personnel' : 'Agence' ?>
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="stat-box">
                                    <small class="text-muted d-block">Actuellement</small>
                                    <strong class="text-primary">
                                        <?= number_format($obj['stats']['ca_current'] ?? 0, 0) ?> DT
                                    </strong>
                                    <br>
                                    <small class="text-muted">/ <?= number_format($obj['revenue_target'] ?? 0, 0) ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="stat-box">
                                    <small class="text-muted d-block">Actuellement</small>
                                    <strong class="text-info">
                                        <?= $obj['stats']['rent_current'] ?? 0 ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">/ <?= $obj['properties_rent_target'] ?? 0 ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="stat-box">
                                    <small class="text-muted d-block">Actuellement</small>
                                    <strong class="text-success">
                                        <?= $obj['stats']['sale_current'] ?? 0 ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">/ <?= $obj['properties_sale_target'] ?? 0 ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="stat-box">
                                    <small class="text-muted d-block">Actuellement</small>
                                    <strong class="text-warning">
                                        <?= $obj['stats']['contacts_current'] ?? 0 ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">/ <?= $obj['new_contacts_target'] ?? 0 ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="stat-box">
                                    <small class="text-muted d-block">Actuellement</small>
                                    <strong class="text-danger">
                                        <?= $obj['stats']['transactions_current'] ?? 0 ?>
                                    </strong>
                                    <br>
                                    <small class="text-muted">/ <?= $obj['transactions_target'] ?? 0 ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary sync-one-btn" 
                                        data-objective-id="<?= $obj['id'] ?>">
                                    <i class="fas fa-sync-alt"></i> Sync
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.stat-box {
    padding: 8px;
    background: #f8f9fa;
    border-radius: 4px;
}

.sync-one-btn {
    transition: all 0.2s;
}

.sync-one-btn.syncing {
    pointer-events: none;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Sync All
document.getElementById('sync-all-btn')?.addEventListener('click', async function() {
    const btn = this;
    const period = new URLSearchParams(window.location.search).get('period') || new Date().toISOString().slice(0, 7);
    
    btn.disabled = true;
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Synchronisation...';
    
    try {
        const formData = new FormData();
        formData.append('period', period);
        
        // Ajouter le CSRF token
        const csrfToken = document.querySelector('[name="<?= csrf_token() ?>"]')?.value 
                       || document.cookie.split('; ').find(row => row.startsWith('<?= csrf_cookie_name() ?>'))?.split('=')[1];
        if (csrfToken) {
            formData.append('<?= csrf_token() ?>', csrfToken);
        }
        
        const response = await fetch('/admin/objectives-sync/sync-all', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const result = await response.json();
        if (result.status === 'success') {
            btn.innerHTML = '<i class="fas fa-check text-success"></i> Synchronisé!';
            setTimeout(() => location.reload(), 1500);
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error(error);
        btn.innerHTML = '<i class="fas fa-exclamation text-danger"></i> Erreur';
        btn.disabled = false;
        setTimeout(() => {
            btn.innerHTML = originalHTML;
        }, 3000);
    }
});

// Sync Individual
document.querySelectorAll('.sync-one-btn').forEach(btn => {
    btn.addEventListener('click', async function() {
        const objectiveId = this.dataset.objectiveId;
        const originalHTML = this.innerHTML;
        
        this.classList.add('syncing');
        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        try {
            const formData = new FormData();
            
            // Ajouter le CSRF token
            const csrfToken = document.querySelector('[name="<?= csrf_token() ?>"]')?.value 
                           || document.cookie.split('; ').find(row => row.startsWith('<?= csrf_cookie_name() ?>'))?.split('=')[1];
            if (csrfToken) {
                formData.append('<?= csrf_token() ?>', csrfToken);
            }
            
            const response = await fetch(`/admin/objectives-sync/sync-one/${objectiveId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const result = await response.json();
            if (result.status === 'success') {
                this.innerHTML = '<i class="fas fa-check text-success"></i>';
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error(error);
            this.innerHTML = '<i class="fas fa-exclamation text-danger"></i>';
            this.disabled = false;
            this.classList.remove('syncing');
            setTimeout(() => {
                this.innerHTML = originalHTML;
            }, 2000);
        }
    });
});
</script>
<?= $this->endSection() ?>
