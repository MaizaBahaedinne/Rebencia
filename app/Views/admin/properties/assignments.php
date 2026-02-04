<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-exchange-alt me-2"></i>Gestion des Affectations
        </h1>
        <a href="<?= base_url('admin/properties') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour aux Propriétés
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <!-- Filtres -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtres</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/properties/assignments') ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="agency_id" class="form-label">Agence Actuelle</label>
                        <select class="form-select" id="agency_id" name="agency_id">
                            <option value="">-- Toutes --</option>
                            <?php foreach ($agencies as $agency): ?>
                                <option value="<?= $agency['id'] ?>" <?= $currentAgency == $agency['id'] ? 'selected' : '' ?>>
                                    <?= esc($agency['name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="agent_id" class="form-label">Agent Actuel</label>
                        <select class="form-select" id="agent_id" name="agent_id">
                            <option value="">-- Tous --</option>
                            <?php foreach ($agents as $agent): ?>
                                <option value="<?= $agent['id'] ?>" <?= $currentAgent == $agent['id'] ? 'selected' : '' ?>>
                                    <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">Statut</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">-- Tous --</option>
                            <option value="draft" <?= $currentStatus === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                            <option value="published" <?= $currentStatus === 'published' ? 'selected' : '' ?>>Publié</option>
                            <option value="sold" <?= $currentStatus === 'sold' ? 'selected' : '' ?>>Vendu</option>
                            <option value="rented" <?= $currentStatus === 'rented' ? 'selected' : '' ?>>Loué</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="<?= base_url('admin/properties/assignments') ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-redo me-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Réaffectation en masse -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-warning">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-users-cog me-2"></i>Réaffectation en Masse
            </h6>
        </div>
        <div class="card-body">
            <form id="reassignForm" method="post" action="<?= base_url('admin/properties/reassign') ?>">
                <?= csrf_field() ?>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">
                            <input type="checkbox" id="selectAll" class="form-check-input me-2">
                            <strong>Sélectionner tout (<span id="selectedCount">0</span> bien(s))</strong>
                        </label>
                    </div>

                    <div class="col-md-4">
                        <label for="new_agency_id" class="form-label">Nouvelle Agence</label>
                        <select class="form-select" id="new_agency_id" name="new_agency_id">
                            <option value="">-- Ne pas changer --</option>
                            <?php foreach ($agencies as $agency): ?>
                                <option value="<?= $agency['id'] ?>">
                                    <?= esc($agency['name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="new_agent_id" class="form-label">Nouvel Agent</label>
                        <select class="form-select" id="new_agent_id" name="new_agent_id">
                            <option value="">-- Ne pas changer --</option>
                            <?php foreach ($agents as $agent): ?>
                                <option value="<?= $agent['id'] ?>">
                                    <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning" id="reassignBtn" disabled>
                        <i class="fas fa-exchange-alt me-2"></i>Réaffecter les Biens Sélectionnés
                    </button>
                </div>

                <!-- Liste des biens -->
                <div class="table-responsive mt-4">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAllHeader" class="form-check-input">
                                </th>
                                <th>Référence</th>
                                <th>Titre</th>
                                <th>Zone</th>
                                <th>Type</th>
                                <th>Agence Actuelle</th>
                                <th>Agent Actuel</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($properties)): ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-search fa-3x mb-3 d-block"></i>
                                        Aucun bien trouvé avec ces filtres
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($properties as $property): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="property_ids[]" 
                                                   value="<?= $property['id'] ?>" 
                                                   class="form-check-input property-checkbox">
                                        </td>
                                        <td><strong><?= esc($property['reference']) ?></strong></td>
                                        <td>
                                            <a href="<?= base_url('admin/properties/view/' . $property['id']) ?>" target="_blank">
                                                <?= esc($property['title']) ?>
                                            </a>
                                        </td>
                                        <td><?= esc($property['zone_name']) ?></td>
                                        <td>
                                            <?php
                                            $types = [
                                                'apartment' => 'Appartement',
                                                'villa' => 'Villa',
                                                'house' => 'Maison',
                                                'land' => 'Terrain',
                                                'commercial' => 'Commercial',
                                                'office' => 'Bureau'
                                            ];
                                            ?>
                                            <span class="badge bg-info"><?= $types[$property['type']] ?? $property['type'] ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($property['agency_name'])): ?>
                                                <span class="badge bg-primary"><?= esc($property['agency_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Non assigné</span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($property['agent_name'])): ?>
                                                <span class="badge bg-success"><?= esc($property['agent_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Non assigné</span>
                                            <?php endif ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusBadges = [
                                                'draft' => 'secondary',
                                                'published' => 'success',
                                                'sold' => 'danger',
                                                'rented' => 'warning'
                                            ];
                                            $statusLabels = [
                                                'draft' => 'Brouillon',
                                                'published' => 'Publié',
                                                'sold' => 'Vendu',
                                                'rented' => 'Loué'
                                            ];
                                            ?>
                                            <span class="badge bg-<?= $statusBadges[$property['status']] ?? 'secondary' ?>">
                                                <?= $statusLabels[$property['status']] ?? $property['status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.property-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const selectedCount = document.getElementById('selectedCount');
    const reassignBtn = document.getElementById('reassignBtn');
    const form = document.getElementById('reassignForm');

    // Update count and button state
    function updateSelection() {
        const checked = document.querySelectorAll('.property-checkbox:checked').length;
        selectedCount.textContent = checked;
        reassignBtn.disabled = checked === 0;
        
        selectAll.checked = checked === checkboxes.length && checkboxes.length > 0;
        selectAllHeader.checked = selectAll.checked;
    }

    // Select all toggle
    selectAll.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        selectAllHeader.checked = this.checked;
        updateSelection();
    });

    selectAllHeader.addEventListener('change', function() {
        checkboxes.forEach(cb => cb.checked = this.checked);
        selectAll.checked = this.checked;
        updateSelection();
    });

    // Individual checkboxes
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelection);
    });

    // Form validation
    form.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.property-checkbox:checked').length;
        const newAgency = document.getElementById('new_agency_id').value;
        const newAgent = document.getElementById('new_agent_id').value;

        if (checked === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un bien');
            return false;
        }

        if (!newAgency && !newAgent) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une nouvelle agence ou un nouvel agent');
            return false;
        }

        if (!confirm(`Voulez-vous vraiment réaffecter ${checked} bien(s) ?`)) {
            e.preventDefault();
            return false;
        }
    });

    // Initialize
    updateSelection();
});
</script>

<?= $this->endSection() ?>
