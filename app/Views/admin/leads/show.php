
<?php
$pipelineSteps = [
    'new'         => ['Nouveau',       'primary',   'bi-circle'],
    'contacted'   => ['Contacté',      'info',      'bi-telephone-check'],
    'interested'  => ['Intéressé',     'warning',   'bi-star'],
    'visit_done'  => ['Visite faite',  'secondary', 'bi-house-check'],
    'negotiating' => ['Négociation',   'dark',      'bi-chat-dots'],
    'won'         => ['Conclu',        'success',   'bi-trophy'],
    'lost'        => ['Perdu',         'danger',    'bi-x-circle'],
];
// Pipeline principal (sans 'lost' qui est un fallout)
$mainPipeline  = array_filter($pipelineSteps, fn($k) => $k !== 'lost', ARRAY_FILTER_USE_KEY);
$currentStatus = $lead['status'] ?? 'new';
$statusKeys    = array_keys($pipelineSteps);
$mainKeys      = array_keys($mainPipeline);
$currentIdx    = array_search($currentStatus, $statusKeys);
$isTerminal    = in_array($currentStatus, ['won', 'lost'], true);
$canEdit       = in_array('leads.edit', session()->get('permissions') ?? []);

// Mapping bouton → déclencheur (modal ou POST direct)
$stepActions = [
    'contacted'   => ['modal',  '#modalContacte',  'btn-info text-white'],
    'interested'  => ['modal',  '#modalInteresse', 'btn-warning text-dark'],
    'visit_done'  => ['direct', '',                'btn-secondary text-white'],
    'negotiating' => ['direct', '',                'btn-dark text-white'],
    'won'         => ['modal',  '#modalConclu',    'btn-success text-white'],
];
?>

<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-2">
    <div>
        <h2 class="mb-0"><?= esc($lead['first_name'] . ' ' . $lead['last_name']) ?></h2>
        <small class="text-muted">Lead #<?= $lead['id'] ?> &bull; Créé le <?= date('d/m/Y', strtotime($lead['created_at'])) ?></small>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <?php if ($canEdit && ! $isTerminal): ?>
        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalPerdu">
            <i class="bi bi-x-circle me-1"></i>Marquer comme perdu
        </button>
        <?php endif; ?>
        <?php if ($canEdit): ?>
        <a href="<?= site_url('admin/leads/' . $lead['id'] . '/edit') ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <a href="<?= site_url('admin/leads') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i>Retour
        </a>
    </div>
</div>

<?php foreach (['success','error','warning','info'] as $fType): ?>
<?php if (session()->has($fType)): ?>
<div class="alert alert-<?= $fType === 'error' ? 'danger' : $fType ?> alert-dismissible fade show">
    <i class="bi bi-<?= $fType === 'success' ? 'check-circle' : ($fType === 'error' ? 'exclamation-triangle' : 'info-circle') ?> me-2"></i>
    <?= session($fType) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php endforeach; ?>

<!-- ═══ Pipeline visuel ═══════════════════════════════════════════ -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3 px-4">
        <div class="d-flex align-items-center overflow-auto gap-0">
            <?php foreach ($mainPipeline as $key => [$label, $color, $icon]):
                $idx      = array_search($key, $statusKeys);
                $mainIdx  = array_search($key, $mainKeys);
                $curMainIdx = array_search($currentStatus, $mainKeys);
                if ($key === $currentStatus)           $state = 'active';
                elseif ($currentStatus === 'lost')     $state = 'pending';  // fallout status
                elseif ($idx < $currentIdx)            $state = 'done';
                else                                   $state = 'pending';
            ?>
            <div class="d-flex align-items-center">
                <?php if ($mainIdx > 0): ?>
                <div class="pipeline-connector" style="width:24px;height:2px;background:<?= $state === 'done' || $state === 'active' ? '#0d6efd' : '#dee2e6' ?>"></div>
                <?php endif; ?>

                <?php if ($state === 'active'): ?>
                    <!-- Étape actuelle : badge coloré -->
                    <span class="badge bg-<?= $color ?> d-flex align-items-center gap-1 py-2 px-3 fs-6">
                        <i class="bi <?= $icon ?>"></i><?= $label ?>
                    </span>

                <?php elseif ($state === 'done'): ?>
                    <!-- Étape passée : check vert -->
                    <span class="badge bg-success bg-opacity-15 text-success border border-success d-flex align-items-center gap-1 py-2 px-3">
                        <i class="bi bi-check-lg"></i><?= $label ?>
                    </span>

                <?php elseif ($state === 'pending' && $canEdit && ! $isTerminal): ?>
                    <!-- Prochaine étape : bouton d'action -->
                    <?php [$actionType, $modalTarget, $btnClass] = $stepActions[$key] ?? ['direct', '', 'btn-outline-secondary']; ?>
                    <?php if ($actionType === 'modal'): ?>
                        <button type="button"
                                class="btn btn-sm <?= $btnClass ?> d-flex align-items-center gap-1"
                                data-bs-toggle="modal" data-bs-target="<?= $modalTarget ?>">
                            <i class="bi <?= $icon ?>"></i><?= $label ?>
                        </button>
                    <?php else: ?>
                        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="<?= $key ?>">
                            <button type="submit" class="btn btn-sm <?= $btnClass ?> d-flex align-items-center gap-1">
                                <i class="bi <?= $icon ?>"></i><?= $label ?>
                            </button>
                        </form>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Étape future désactivée -->
                    <span class="badge bg-light text-muted border d-flex align-items-center gap-1 py-2 px-3">
                        <i class="bi <?= $icon ?>"></i><?= $label ?>
                    </span>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php if ($currentStatus === 'lost'): ?>
            <div class="d-flex align-items-center ms-3">
                <div class="pipeline-connector" style="width:24px;height:2px;background:#dc3545"></div>
                <span class="badge bg-danger d-flex align-items-center gap-1 py-2 px-3 fs-6">
                    <i class="bi bi-x-circle"></i>Perdu
                </span>
            </div>
            <?php endif; ?>
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
                            <i class="bi <?= $pipelineSteps[$currentStatus][2] ?> me-1"></i>
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
                    <?php if (!empty($h['notes'])): ?>
                    <p class="text-muted small mb-0 mt-1 fst-italic"><?= esc($h['notes']) ?></p>
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


<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- MODALS PIPELINE                                                  -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- ── Modal 1 : Contacté (note obligatoire) ── -->
<div class="modal fade" id="modalContacte" tabindex="-1" aria-labelledby="modalContacteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="contacted">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalContacteLabel">
                        <span class="badge bg-info text-white me-2"><i class="bi bi-telephone-check"></i></span>
                        Passer en &laquo;&nbsp;Contacté&nbsp;&raquo;
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        Une note résumant l&rsquo;échange est obligatoire.
                        L&rsquo;agence responsable du bien sera notifiée.
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Note de contact <span class="text-danger">*</span></label>
                        <textarea name="contact_note" class="form-control" rows="4" required
                            placeholder="Ex : Appelé le <?= date('d/m/Y') ?>, intéressé par l'appartement T3, rappel prévu sous 48h…"></textarea>
                    </div>
                    <div class="alert alert-info py-2 small mb-0">
                        <i class="bi bi-bell me-1"></i>
                        Une notification sera envoyée à tous les membres de l&rsquo;agence responsable du bien.
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="bi bi-check-lg me-1"></i>Confirmer le contact
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal 2 : Intéressé (RDV de visite) ── -->
<div class="modal fade" id="modalInteresse" tabindex="-1" aria-labelledby="modalInteresseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="interested">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalInteresseLabel">
                        <span class="badge bg-warning text-dark me-2"><i class="bi bi-star"></i></span>
                        Planifier un RDV de visite
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">
                        <i class="bi bi-info-circle me-1"></i>Un rendez-vous de visite sera créé dans le module Visites.
                    </p>
                    <?php if (!empty($lead['property_title'])): ?>
                    <div class="alert alert-light border small mb-3 py-2">
                        <i class="bi bi-building me-1 text-primary"></i>
                        Bien&nbsp;: <strong><?= esc($lead['property_title']) ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Date de visite <span class="text-danger">*</span></label>
                            <input type="date" name="visit_date" class="form-control" required
                                   min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d', strtotime('+3 days')) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label fw-semibold">Heure</label>
                            <input type="time" name="visit_time" class="form-control" value="10:00">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Notes pour la visite</label>
                            <textarea name="visit_notes" class="form-control" rows="2"
                                placeholder="Instructions, points à vérifier…"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning text-dark">
                        <i class="bi bi-calendar-plus me-1"></i>Planifier la visite
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal 3 : Conclu (placeholder transaction) ── -->
<div class="modal fade" id="modalConclu" tabindex="-1" aria-labelledby="modalConcluLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="won">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalConcluLabel">
                        <span class="badge bg-success me-2"><i class="bi bi-trophy"></i></span>
                        Marquer comme Conclu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <strong>Félicitations&nbsp;!</strong> Ce lead est sur le point d&rsquo;être conclu.
                    </div>
                    <div class="alert alert-warning py-2 small">
                        <i class="bi bi-tools me-1"></i>
                        <strong>Module Transaction</strong> en cours de développement.<br>
                        La fiche de transaction complète (compromis, commission, suivi) sera disponible prochainement.
                    </div>
                    <p class="text-muted small mb-0">En cliquant sur Confirmer, le lead passera au statut <strong>Conclu</strong>.</p>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-trophy me-1"></i>Confirmer le closing
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ── Modal 4 : Perdu (raison obligatoire) ── -->
<div class="modal fade" id="modalPerdu" tabindex="-1" aria-labelledby="modalPerduLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="<?= site_url('admin/leads/' . $lead['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="status" value="lost">
            <div class="modal-content">
                <div class="modal-header border-bottom-0">
                    <h5 class="modal-title" id="modalPerduLabel">
                        <span class="badge bg-danger me-2"><i class="bi bi-x-circle"></i></span>
                        Marquer comme Perdu
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Indiquez la raison de la perte pour améliorer le suivi CRM.</p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Raison <span class="text-danger">*</span></label>
                        <select name="lost_reason" id="lostReasonSelect" class="form-select" required
                                onchange="document.getElementById('lostDetailWrap').style.display = this.value === 'Autre' ? 'block' : 'none'">
                            <option value="">-- Sélectionner une raison --</option>
                            <option>Budget insuffisant</option>
                            <option>Pas de réponse du client</option>
                            <option>Bien vendu à un autre acheteur</option>
                            <option>Délai de décision trop long</option>
                            <option>Concurrent choisi</option>
                            <option>Projet immobilier abandonné</option>
                            <option>Prix trop élevé</option>
                            <option>Bien ne correspond pas aux critères</option>
                            <option>Client non sérieux</option>
                            <option>Autre</option>
                        </select>
                    </div>
                    <div id="lostDetailWrap" class="mb-3" style="display:none">
                        <label class="form-label fw-semibold">Précisions</label>
                        <textarea name="lost_detail" class="form-control" rows="3"
                            placeholder="Décrire la situation en détail…"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Confirmer la perte
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
