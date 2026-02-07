<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-users"></i> Gestion des Clients
    </h1>
    <a href="<?= base_url('admin/clients/create') ?>" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Nouveau Client
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="clientsTable">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Agent</th>
                        <th>Agence</th>
                        <th>Statut</th>
                        <th class="no-filter">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td>
                                    <?php if ($client['type'] === 'company'): ?>
                                        <?= esc($client['company_name']) ?>
                                    <?php else: ?>
                                        <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($client['email'] ?? '-') ?></td>
                                <td><?= esc($client['phone']) ?></td>
                                <td><?= esc($client['agent_name'] . ' ' . ($client['agent_lastname'] ?? '')) ?></td>
                                <td><?= esc($client['agency_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($client['status']) {
                                        'active' => 'success',
                                        'prospect' => 'info',
                                        'lead' => 'warning',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($client['status']) ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/clients/view/' . $client['id']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('admin/clients/edit/' . $client['id']) ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Aucun client trouvé
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#clientsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
            search: "Rechercher:",
            lengthMenu: "Afficher _MENU_ entrées",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            infoEmpty: "Affichage de 0 à 0 sur 0 entrées",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent"
            }
        },
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 6 }
        ],
        responsive: true,
        autoWidth: false
    });
});
</script>
<?= $this->endSection() ?>
