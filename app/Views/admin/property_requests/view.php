<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-envelope-open-text text-primary"></i> Détails de la demande
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/property-requests') ?>">Demandes</a></li>
                    <li class="breadcrumb-item active">Détails</li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('admin/property-requests') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8">
            <!-- Request Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations de la demande</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted">Type de demande</label>
                            <div>
                                <?php if ($request['request_type'] === 'visit'): ?>
                                    <span class="badge bg-info fs-6">
                                        <i class="fas fa-calendar-check"></i> Demande de visite
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-primary fs-6">
                                        <i class="fas fa-info-circle"></i> Demande d'information
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted">Date de la demande</label>
                            <div><strong><?= date('d/m/Y à H:i', strtotime($request['created_at'])) ?></strong></div>
                        </div>
                    </div>

                    <?php if ($request['request_type'] === 'visit' && $request['visit_date']): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="text-muted">Date souhaitée</label>
                                <div><strong><?= date('d/m/Y', strtotime($request['visit_date'])) ?></strong></div>
                            </div>
                            <?php if ($request['visit_time']): ?>
                                <div class="col-md-6">
                                    <label class="text-muted">Heure souhaitée</label>
                                    <div><strong><?= esc($request['visit_time']) ?></strong></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="text-muted">Message du client</label>
                        <div class="p-3 bg-light rounded">
                            <?= nl2br(esc($request['message'])) ?>
                        </div>
                    </div>

                    <?php if ($request['response']): ?>
                        <div class="mb-3">
                            <label class="text-muted">Réponse envoyée</label>
                            <div class="p-3 bg-light rounded border-start border-4 border-success">
                                <?= nl2br(esc($request['response'])) ?>
                                <div class="text-muted mt-2">
                                    <small>Répondu le <?= date('d/m/Y à H:i', strtotime($request['responded_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Client Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informations du client</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Nom complet</label>
                            <div><strong><?= esc($request['first_name'] . ' ' . $request['last_name']) ?></strong></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Téléphone</label>
                            <div>
                                <a href="tel:<?= esc($request['phone']) ?>" class="text-decoration-none">
                                    <i class="fas fa-phone text-success"></i> <strong><?= esc($request['phone']) ?></strong>
                                </a>
                            </div>
                        </div>
                        <?php if ($request['email']): ?>
                            <div class="col-md-6 mb-3">
                                <label class="text-muted">Email</label>
                                <div>
                                    <a href="mailto:<?= esc($request['email']) ?>" class="text-decoration-none">
                                        <i class="fas fa-envelope text-primary"></i> <?= esc($request['email']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Type</label>
                            <div><span class="badge bg-secondary"><?= ucfirst($request['type']) ?></span></div>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/clients/view/' . $request['client_id']) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-user"></i> Voir le profil complet
                    </a>
                </div>
            </div>

            <!-- Property Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Propriété concernée</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label class="text-muted">Référence</label>
                            <div><strong><?= esc($request['reference']) ?></strong></div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="text-muted">Titre</label>
                            <div><h5 class="mb-0"><?= esc($request['title']) ?></h5></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Prix</label>
                            <div><strong class="text-primary fs-5"><?= number_format($request['price'], 0, ',', ' ') ?> TND</strong></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted">Localisation</label>
                            <div><?= esc($request['address'] . ', ' . $request['city']) ?></div>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/properties/edit/' . $request['property_id']) ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-home"></i> Voir la propriété
                    </a>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Statut et Actions</h5>
                </div>
                <div class="card-body">
                    <form id="statusForm">
                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Statut actuel</label>
                            <select name="status" class="form-select" id="statusSelect">
                                <option value="pending" <?= $request['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                                <option value="contacted" <?= $request['status'] === 'contacted' ? 'selected' : '' ?>>Contacté</option>
                                <option value="scheduled" <?= $request['status'] === 'scheduled' ? 'selected' : '' ?>>Planifié</option>
                                <option value="completed" <?= $request['status'] === 'completed' ? 'selected' : '' ?>>Complété</option>
                                <option value="cancelled" <?= $request['status'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Réponse / Note</label>
                            <textarea name="response" class="form-control" rows="4" placeholder="Ajouter une réponse ou note..."><?= esc($request['response'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Mettre à jour
                        </button>
                    </form>

                    <div class="alert alert-success d-none mt-3" id="successAlert"></div>
                    <div class="alert alert-danger d-none mt-3" id="errorAlert"></div>
                </div>
            </div>

            <!-- Assign Agent Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Agent assigné</h5>
                </div>
                <div class="card-body">
                    <?php if ($request['assigned_to']): ?>
                        <div class="mb-3">
                            <i class="fas fa-user-tie text-primary"></i>
                            <strong><?= esc($request['agent_first_name'] . ' ' . $request['agent_last_name']) ?></strong>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">Aucun agent assigné</p>
                    <?php endif; ?>

                    <form id="assignForm">
                        <input type="hidden" name="id" value="<?= $request['id'] ?>">
                        <div class="mb-3">
                            <select name="agent_id" class="form-select">
                                <option value="">Choisir un agent...</option>
                                <?php foreach ($agents as $agent): ?>
                                    <option value="<?= $agent['id'] ?>" <?= $request['assigned_to'] == $agent['id'] ? 'selected' : '' ?>>
                                        <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-user-plus"></i> Assigner
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions rapides</h5>
                </div>
                <div class="card-body">
                    <a href="tel:<?= esc($request['phone']) ?>" class="btn btn-success w-100 mb-2">
                        <i class="fas fa-phone"></i> Appeler le client
                    </a>
                    <?php if ($request['email']): ?>
                        <a href="mailto:<?= esc($request['email']) ?>" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-envelope"></i> Envoyer un email
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('properties/' . $request['reference']) ?>" target="_blank" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-external-link-alt"></i> Voir la propriété
                    </a>
                    <button onclick="confirmDelete(<?= $request['id'] ?>)" class="btn btn-danger w-100">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Update Status
document.getElementById('statusForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('admin/property-requests/update-status') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('successAlert').textContent = data.message;
            document.getElementById('successAlert').classList.remove('d-none');
            setTimeout(() => location.reload(), 1500);
        } else {
            document.getElementById('errorAlert').textContent = data.message;
            document.getElementById('errorAlert').classList.remove('d-none');
        }
    });
});

// Assign Agent
document.getElementById('assignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('<?= base_url('admin/property-requests/assign') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    });
});

// Delete
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette demande ?')) {
        window.location.href = '<?= base_url('admin/property-requests/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
