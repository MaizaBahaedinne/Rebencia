<?php $this->extend('layouts/main'); ?>
<?php $this->section('content'); ?>

<?php
$pipelineSteps = [
    'new'         => ['Nouveau',      'primary'],
    'contacted'   => ['Contacté',     'info'],
    'interested'  => ['Intéressé',    'warning'],
    'visit_done'  => ['Visite faite', 'secondary'],
    'negotiating' => ['Négociation',  'dark'],
    'won'         => ['Conclu',       'success'],
    'lost'        => ['Perdu',        'danger'],
];
$currentStatus = $lead['status'] ?? 'new';
$statusKeys = array_keys($pipelineSteps);
$currentIdx  = array_search($currentStatus, $statusKeys);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= esc($lead['first_name'] . ' ' . $lead['last_name']) ?></h2>
        <small class="text-muted">Lead #<?= $lead['id'] ?> • Créé le <?= date('d/m/Y', strtotime($lead['created_at'])) ?></small>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= site_url('admin/leads/' . $lead['id'] . '/edit') ?>" class="btn btn-warning">
            <i class="bi bi-pencil me-1"></i> Modifier
        </a>
        <a href="<?= site_url('admin/leads') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Pipeline visuel -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center overflow-auto gap-1">
            <?php foreach ($pipelineSteps as $key => [$label, $color]): ?>
            <?php
            $idx = array_search($key, $statusKeys);
            if ($key === $currentStatus) $state = 'active';
            elseif ($idx < $currentIdx)  $state = 'done';
            else                          $state = 'pending';
            ?>
            <div class="d-flex align-items-center <?= $idx > 0 ? 'ms-1' : '' ?>">
                <?php if ($idx > 0): ?><div style="width:20px;height:2px;background:#dee2e6"></div><?php endif; ?>
                <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="status" value="<?= $key ?>">
                    <button type="submit" class="btn btn-sm <?= $state === 'active' ? 'btn-'.$color : ($state === 'done' ? 'btn-outline-'.$color : 'btn-outline-secondary') ?>"
                            <?= $state === 'active' ? 'disabled' : '' ?>>
                        <?= $label ?>
                    </button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Colonne gauche -->
    <div class="col-lg-8">
        <!-- Informations -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><strong><i class="bi bi-person me-2"></i>Contact</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="text-muted small">Prénom</label>
                        <p class="mb-0 fw-semibold"><?= esc($lead['first_name']) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Nom</label>
                        <p class="mb-0 fw-semibold"><?= esc($lead['last_name']) ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Email</label>
                        <p class="mb-0"><?= esc($lead['email'] ?? '—') ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Téléphone</label>
                        <p class="mb-0"><?= esc($lead['phone'] ?? '—') ?></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Source</label>
                        <p class="mb-0"><span class="badge bg-light text-dark border"><?= esc($lead['source'] ?? '—') ?></span></p>
                    </div>
                    <div class="col-sm-6">
                        <label class="text-muted small">Priorité</label>
                        <p class="mb-0">
                            <?php $pMap = ['low'=>['Faible','secondary'],'medium'=>['Normale','warning'],'high'=>['Haute','danger']]; ?>
                            <span class="badge bg-<?= $pMap[$lead['priority'] ?? 'medium'][1] ?>"><?= $pMap[$lead['priority'] ?? 'medium'][0] ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projet -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent"><strong><i class="bi bi-house me-2"></i>Projet immobilier</strong></div>
            <div class="card-body">
                <div class="row g-3">
                    <?php
                    $details = [
                        'Type transaction' => $lead['transaction_type'] ?? '—',
                        'Type de bien'     => $lead['property_type']    ?? '—',
                        'Budget min'       => !empty($lead['budget_min'])     ? number_format((float)$lead['budget_min'], 0, ',', ' ')    . ' TND' : '—',
                        'Budget max'       => !empty($lead['budget_max'])     ? number_format((float)$lead['budget_max'], 0, ',', ' ')    . ' TND' : '—',
                        'Surface souhait.' => !empty($lead['desired_surface']) ? $lead['desired_surface'] . ' m²' : '—',
                        'Localisation'     => $lead['desired_location'] ?? '—',
                    ];
                    foreach ($details as $label => $value):
                    ?>
                    <div class="col-sm-6">
                        <label class="text-muted small"><?= $label ?></label>
                        <p class="mb-0 fw-semibold"><?= esc($value) ?></p>
                    </div>
                    <?php endforeach; ?>
                    <?php if (!empty($lead['property_id'])): ?>
                    <div class="col-12">
                        <label class="text-muted small">Propriété liée</label>
                        <p class="mb-0">
                            <a href="<?= site_url('admin/properties/' . $lead['property_id']) ?>">
                                [<?= esc($lead['property_reference'] ?? '…') ?>] <?= esc($lead['property_title'] ?? '') ?>
                            </a>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notes existantes -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-chat-text me-2"></i>Notes</strong>
                <span class="badge bg-secondary"><?= count($notes ?? []) ?></span>
            </div>
            <div class="card-body" style="max-height:300px;overflow-y:auto">
                <?php if (empty($notes)): ?>
                    <p class="text-muted text-center mb-0">Aucune note</p>
                <?php else: ?>
                <?php foreach (array_reverse($notes) as $note): ?>
                <div class="border-start border-3 border-primary ps-3 mb-3">
                    <div class="d-flex justify-content-between">
                        <strong class="small"><?= esc(($note['author_first_name'] ?? '') . ' ' . ($note['author_last_name'] ?? '')) ?></strong>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($note['created_at'])) ?></small>
                    </div>
                    <p class="mb-0"><?= nl2br(esc($note['note'])) ?></p>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/note') ?>">
                    <?= csrf_field() ?>
                    <div class="input-group">
                        <textarea name="note" class="form-control" rows="2" placeholder="Ajouter une note…" required></textarea>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-send"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Statut + agent -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Statut actuel</label>
                    <div>
                        <span class="badge bg-<?= $pipelineSteps[$currentStatus][1] ?> fs-6">
                            <?= $pipelineSteps[$currentStatus][0] ?>
                        </span>
                    </div>
                </div>
                <div>
                    <label class="text-muted small">Assigné à</label>
                    <p class="mb-0 fw-semibold">
                        <?= !empty($lead['agent_first_name'])
                            ? esc($lead['agent_first_name'] . ' ' . $lead['agent_last_name'])
                            : '<span class="text-muted">Non assigné</span>' ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Historique statuts -->
        <?php if (!empty($statusHistory)): ?>
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent"><strong>Historique du pipeline</strong></div>
            <ul class="list-group list-group-flush">
                <?php foreach ($statusHistory as $h): ?>
                <li class="list-group-item py-2">
                    <div class="d-flex justify-content-between">
                        <span class="badge bg-<?= $pipelineSteps[$h['new_status']][1] ?? 'secondary' ?>">
                            <?= $pipelineSteps[$h['new_status']][0] ?? esc($h['new_status']) ?>
                        </span>
                        <small class="text-muted"><?= date('d/m H:i', strtotime($h['created_at'])) ?></small>
                    </div>
                    <?php if (!empty($h['user_first_name'])): ?>
                    <small class="text-muted">Par <?= esc($h['user_first_name'] . ' ' . $h['user_last_name']) ?></small>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->endSection(); ?>
