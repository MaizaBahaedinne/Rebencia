
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

        <!-- Bien demandé -->
        <?php if (!empty($linkedProperty)): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-transparent">
                <strong><i class="bi bi-building-check me-2 text-primary"></i>Bien demandé par le client</strong>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-start">
                    <?php
                    $coverImg = null;
                    foreach ($linkedProperty['images'] as $img) {
                        if ($img['is_primary']) { $coverImg = $img['path']; break; }
                    }
                    if (!$coverImg && !empty($linkedProperty['images'])) {
                        $coverImg = $linkedProperty['images'][0]['path'];
                    }
                    ?>
                    <?php if ($coverImg): ?>
                    <div class="col-md-4">
                        <img src="<?= base_url(esc($coverImg)) ?>"
                             class="img-fluid rounded" style="object-fit:cover;height:160px;width:100%;" alt="">
                    </div>
                    <?php endif; ?>
                    <div class="col-md-<?= $coverImg ? '8' : '12' ?>">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-secondary me-1"><?= esc($linkedProperty['reference']) ?></span>
                                <?php
                                $sMap = ['available'=>['Disponible','success'],'sold'=>['Vendu','danger'],'reserved'=>['Réservé','warning'],'rented'=>['Loué','info']];
                                [$sLabel,$sColor] = $sMap[$linkedProperty['status']] ?? [ucfirst($linkedProperty['status']),'secondary'];
                                ?>
                                <span class="badge bg-<?= $sColor ?>"><?= $sLabel ?></span>
                            </div>
                            <a href="<?= site_url('admin/properties/' . $linkedProperty['id']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye me-1"></i>Voir la fiche
                            </a>
                        </div>
                        <h6 class="mb-2"><?= esc($linkedProperty['title']) ?></h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt me-1"></i><?= esc($linkedProperty['city'] . (!empty($linkedProperty['zone']) ? ', ' . $linkedProperty['zone'] : '')) ?>
                        </p>
                        <div class="row g-2">
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Prix</div>
                                    <strong class="text-primary"><?= number_format((float)$linkedProperty['price'], 0, ',', ' ') ?> TND</strong>
                                </div>
                            </div>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Surface</div>
                                    <strong><?= esc($linkedProperty['surface']) ?> m²</strong>
                                </div>
                            </div>
                            <?php if (!empty($linkedProperty['rooms'])): ?>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Pièces</div>
                                    <strong><?= esc($linkedProperty['rooms']) ?></strong>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($linkedProperty['bedrooms'])): ?>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">Chambres</div>
                                    <strong><?= esc($linkedProperty['bedrooms']) ?></strong>
                                </div>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($linkedProperty['bathrooms'])): ?>
                            <div class="col-6 col-md-4">
                                <div class="border rounded p-2 text-center">
                                    <div class="text-muted small">SDB</div>
                                    <strong><?= esc($linkedProperty['bathrooms']) ?></strong>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

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

<!-- Propositions similaires -->
<?php if (!empty($similarProperties)): ?>
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
        <strong><i class="bi bi-stars me-2 text-warning"></i>Propositions similaires à la demande</strong>
        <span class="badge bg-secondary"><?= count($similarProperties) ?> bien<?= count($similarProperties) > 1 ? 's' : '' ?></span>
    </div>
    <div class="card-body">
        <?php if (!empty($lead['property_type']) || !empty($lead['budget_max']) || !empty($lead['desired_location'])): ?>
        <p class="text-muted small mb-3">
            Biens disponibles correspondant à :
            <?php if (!empty($lead['property_type'])): ?><span class="badge bg-light text-dark border me-1"><?= esc($lead['property_type']) ?></span><?php endif; ?>
            <?php if (!empty($lead['transaction_type'])): ?><span class="badge bg-light text-dark border me-1"><?= esc($lead['transaction_type']) ?></span><?php endif; ?>
            <?php if (!empty($lead['desired_location'])): ?><span class="badge bg-light text-dark border me-1"><i class="bi bi-geo-alt"></i> <?= esc($lead['desired_location']) ?></span><?php endif; ?>
            <?php if (!empty($lead['budget_max'])): ?><span class="badge bg-light text-dark border me-1">≤ <?= number_format((float)$lead['budget_max'], 0, ',', ' ') ?> TND</span><?php endif; ?>
        </p>
        <?php endif; ?>
        <div class="row g-3">
            <?php foreach ($similarProperties as $prop): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 border">
                    <?php if (!empty($prop['primary_image'])): ?>
                    <img src="<?= base_url(esc($prop['primary_image'])) ?>"
                         class="card-img-top" style="object-fit:cover;height:170px;" alt="">
                    <?php else: ?>
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:170px;">
                        <i class="bi bi-building text-secondary" style="font-size:3rem;"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="badge bg-light text-dark border small"><?= esc($prop['type']) ?></span>
                            <?php if ($prop['featured']): ?>
                            <i class="bi bi-star-fill text-warning small" title="En vedette"></i>
                            <?php endif; ?>
                        </div>
                        <h6 class="card-title small mb-1 fw-semibold"><?= esc($prop['title']) ?></h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-geo-alt me-1"></i><?= esc($prop['city']) ?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="text-primary"><?= number_format((float)$prop['price'], 0, ',', ' ') ?> TND</strong>
                            <?php if (!empty($prop['surface'])): ?>
                            <small class="text-muted"><?= esc($prop['surface']) ?> m²</small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent p-2">
                        <a href="<?= site_url('admin/properties/' . $prop['id']) ?>" class="btn btn-sm btn-outline-primary w-100">
                            <i class="bi bi-eye me-1"></i>Voir la fiche
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

