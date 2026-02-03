<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-folder-open text-info"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/transactions') ?>">Transactions</a></li>
                <li class="breadcrumb-item active">Documents</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= base_url('admin/transactions') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<!-- Transaction Info -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <p class="text-muted mb-1">Référence</p>
                <h5><?= esc($transaction['reference']) ?></h5>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Type</p>
                <h5><?= $transaction['type'] === 'sale' ? 'Vente' : 'Location' ?></h5>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Montant</p>
                <h5><?= number_format($transaction['amount'], 0, ',', ' ') ?> TND</h5>
            </div>
            <div class="col-md-3">
                <p class="text-muted mb-1">Statut</p>
                <h5>
                    <span class="badge bg-<?= $transaction['status'] === 'completed' ? 'success' : 'warning' ?>">
                        <?= ucfirst($transaction['status']) ?>
                    </span>
                </h5>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-upload"></i> Upload Nouveau Document</h5>
    </div>
    <div class="card-body">
        <?php if (session()->has('errors')): ?>
            <div class="alert alert-danger">
                <?php foreach(session('errors') as $error): ?>
                    <div><?= $error ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/documents/upload/' . $transaction['id']) ?>" method="POST" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Type de Document *</label>
                    <select name="document_type" class="form-select" required>
                        <option value="">Sélectionner...</option>
                        <option value="contract">Contrat</option>
                        <option value="title_deed">Titre Foncier</option>
                        <option value="id_copy">Copie CIN</option>
                        <option value="tax_document">Document Fiscal</option>
                        <option value="other">Autre</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fichier * (PDF, DOC, Image, Max 5MB)</label>
                    <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Description</label>
                    <input type="text" name="description" class="form-control" placeholder="Description du document">
                </div>
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Upload Document
                    </button>
                    <button type="button" class="btn btn-success" onclick="generateContract()">
                        <i class="fas fa-file-contract"></i> Générer Contrat
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Documents List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-file-alt"></i> Documents (<?= count($documents) ?>)</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Type</th>
                        <th>Nom Fichier</th>
                        <th>Description</th>
                        <th>Taille</th>
                        <th>Version</th>
                        <th>Uploadé par</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($documents)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-3x mb-3 d-block"></i>
                                Aucun document uploadé
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($documents as $doc): ?>
                            <?php
                            $typeLabels = [
                                'contract' => 'Contrat',
                                'title_deed' => 'Titre Foncier',
                                'id_copy' => 'Copie CIN',
                                'tax_document' => 'Document Fiscal',
                                'other' => 'Autre'
                            ];
                            $typeIcons = [
                                'contract' => 'fa-file-contract',
                                'title_deed' => 'fa-file-certificate',
                                'id_copy' => 'fa-id-card',
                                'tax_document' => 'fa-file-invoice-dollar',
                                'other' => 'fa-file'
                            ];
                            ?>
                            <tr>
                                <td>
                                    <i class="fas <?= $typeIcons[$doc['document_type']] ?> me-2"></i>
                                    <?= $typeLabels[$doc['document_type']] ?>
                                </td>
                                <td><?= esc($doc['file_name']) ?></td>
                                <td><?= esc($doc['description']) ?></td>
                                <td><?= number_format($doc['file_size'] / 1024, 2) ?> KB</td>
                                <td>
                                    <span class="badge bg-info">v<?= $doc['version'] ?></span>
                                </td>
                                <td><?= esc($doc['uploader_name']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($doc['created_at'])) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/documents/download/' . $doc['id']) ?>" 
                                       class="btn btn-sm btn-primary" title="Télécharger">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <button onclick="deleteDocument(<?= $doc['id'] ?>)" 
                                            class="btn btn-sm btn-danger" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function deleteDocument(id) {
    if (confirm('Voulez-vous vraiment supprimer ce document ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('admin/documents/delete/') ?>' + id;
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '_method';
        input.value = 'DELETE';
        form.appendChild(input);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function generateContract() {
    if (confirm('Générer un contrat automatique pour cette transaction ?')) {
        window.location.href = '<?= base_url('admin/documents/generate-contract/' . $transaction['id']) ?>';
    }
}
</script>
<?= $this->endSection() ?>
