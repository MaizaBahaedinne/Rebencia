<!-- DASHBOARD COORDINATEUR -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Tableau de bord Coordinateur</h4>
        <p class="text-muted mb-0">Suivi équipe & leads – <?= date('d/m/Y') ?></p>
    </div>
    <span class="badge badge-coordinator text-white px-3 py-2">
        <i class="bi bi-diagram-3 me-1"></i>Coordinateur
    </span>
</div>

<!-- KPIs leads -->
<div class="row g-3 mb-4">
    <?php
    $stageColors = ['new'=>'secondary','contacted'=>'info','visit'=>'primary',
                    'negotiation'=>'warning','sold'=>'success','lost'=>'danger'];
    $stageLabels = ['new'=>'Nouveau','contacted'=>'Contacté','visit'=>'Visite',
                    'negotiation'=>'Négociation','sold'=>'Vendu','lost'=>'Perdu'];
    ?>
    <?php foreach (['new', 'contacted', 'visit', 'negotiation'] as $st) : ?>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center py-3">
                <div class="fs-2 fw-bold text-<?= $stageColors[$st] ?>"><?= $lead_stats[$st] ?? 0 ?></div>
                <div class="text-muted small"><?= $stageLabels[$st] ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <!-- Pipeline -->
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-kanban text-success"></i> Pipeline leads
                <a href="<?= base_url('admin/leads') ?>" class="ms-auto btn btn-sm btn-outline-success">Gérer</a>
            </div>
            <div class="card-body overflow-auto">
                <div class="d-flex gap-2" style="min-width:800px;">
                    <?php foreach (['new','contacted','visit','negotiation'] as $st) :
                        $leads = $lead_pipeline[$st] ?? [];
                    ?>
                    <div class="pipeline-col">
                        <div class="mb-2">
                            <span class="badge bg-<?= $stageColors[$st] ?>"><?= count($leads) ?></span>
                            <span class="small fw-semibold text-muted ms-1"><?= $stageLabels[$st] ?></span>
                        </div>
                        <?php if (empty($leads)) : ?>
                        <div class="text-muted text-center small bg-light rounded py-2">Vide</div>
                        <?php else : ?>
                        <?php foreach (array_slice($leads, 0, 5) as $lead) : ?>
                        <a href="<?= base_url('admin/leads/' . $lead['id']) ?>" class="text-decoration-none">
                            <div class="pipeline-card border-<?= $stageColors[$st] ?>">
                                <div class="fw-semibold text-dark"><?= esc($lead['first_name']) ?> <?= esc($lead['last_name']) ?></div>
                                <div class="text-muted small"><?= $lead['phone'] ?></div>
                            </div>
                        </a>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Leads non assignés -->
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i> Non assignés
            </div>
            <div class="card-body p-0">
                <?php if (empty($unassigned_leads)) : ?>
                <p class="text-muted text-center py-4"><i class="bi bi-check-all"></i> Tous les leads sont assignés</p>
                <?php else : ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($unassigned_leads as $lead) : ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-semibold small"><?= esc($lead['first_name']) ?> <?= esc($lead['last_name']) ?></div>
                            <div class="text-muted" style="font-size:.75rem;"><?= $lead['phone'] ?></div>
                        </div>
                        <a href="<?= base_url('admin/leads/' . $lead['id']) ?>" class="btn btn-sm btn-outline-warning">Assigner</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
