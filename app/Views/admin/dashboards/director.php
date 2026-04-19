<!-- DASHBOARD DIRECTEUR -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Tableau de bord Directeur</h4>
        <p class="text-muted mb-0">Vision globale de l'agence – <?= date('d/m/Y') ?></p>
    </div>
    <span class="badge badge-director text-white px-3 py-2">
        <i class="bi bi-star-fill me-1"></i>Directeur d'Agence
    </span>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $user_stats['total'] ?></div>
                    <div class="text-muted small">Utilisateurs</div>
                    <div class="text-success small"><i class="bi bi-dot"></i><?= $user_stats['active'] ?> actifs</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-building-fill"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $property_stats['total'] ?></div>
                    <div class="text-muted small">Biens</div>
                    <div class="text-success small"><i class="bi bi-dot"></i><?= $property_stats['available'] ?> disponibles</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-person-lines-fill"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $lead_stats['total'] ?></div>
                    <div class="text-muted small">Leads</div>
                    <div class="text-warning small"><i class="bi bi-dot"></i><?= $lead_stats['new'] ?> nouveaux</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-trophy-fill"></i>
                </div>
                <div>
                    <div class="fs-3 fw-bold"><?= $property_stats['sold'] ?></div>
                    <div class="text-muted small">Ventes</div>
                    <div class="text-danger small"><i class="bi bi-dot"></i><?= $property_stats['reserved'] ?> réservés</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <!-- Pipeline CRM -->
    <div class="col-12 col-xl-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-kanban text-primary"></i> Pipeline CRM
                <a href="<?= base_url('admin/leads') ?>" class="ms-auto btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body overflow-auto">
                <div class="d-flex gap-2" style="min-width:900px;">
                    <?php
                    $stages = [
                        'new'         => ['label' => 'Nouveau',      'color' => 'secondary'],
                        'contacted'   => ['label' => 'Contacté',     'color' => 'info'],
                        'visit'       => ['label' => 'Visite',       'color' => 'primary'],
                        'negotiation' => ['label' => 'Négociation',  'color' => 'warning'],
                        'sold'        => ['label' => 'Vendu',        'color' => 'success'],
                        'lost'        => ['label' => 'Perdu',        'color' => 'danger'],
                    ];
                    foreach ($stages as $key => $stage) :
                        $leads = $lead_pipeline[$key] ?? [];
                    ?>
                    <div class="pipeline-col">
                        <div class="d-flex align-items-center mb-2 gap-1">
                            <span class="badge bg-<?= $stage['color'] ?>"><?= count($leads) ?></span>
                            <span class="fw-semibold small text-muted"><?= $stage['label'] ?></span>
                        </div>
                        <?php if (empty($leads)) : ?>
                        <div class="text-muted text-center py-3 small bg-light rounded">Vide</div>
                        <?php endif; ?>
                        <?php foreach (array_slice($leads, 0, 4) as $lead) : ?>
                        <div class="pipeline-card border-<?= $stage['color'] ?>">
                            <div class="fw-semibold"><?= esc($lead['first_name']) ?> <?= esc($lead['last_name']) ?></div>
                            <?php if ($lead['property_title']) : ?>
                            <div class="text-muted" style="font-size:.78rem;"><?= esc($lead['property_title']) ?></div>
                            <?php endif; ?>
                            <div class="mt-1">
                                <?php $prioColors = ['high'=>'danger','medium'=>'warning','low'=>'secondary']; ?>
                                <span class="badge bg-<?= $prioColors[$lead['priority']] ?? 'secondary' ?> bg-opacity-75" style="font-size:.65rem;">
                                    <?= $lead['priority'] ?>
                                </span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Activité récente -->
    <div class="col-12 col-xl-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-warning"></i> Activité récente
                <a href="<?= base_url('admin/system/logs') ?>" class="ms-auto btn btn-sm btn-outline-secondary">Logs</a>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($recent_activity as $act) : ?>
                    <li class="list-group-item px-3 py-2">
                        <div class="d-flex align-items-start gap-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:32px;height:32px;font-size:.75rem;">
                                <?= strtoupper(substr($act['user_name'] ?? 'S', 0, 1)) ?>
                            </div>
                            <div style="min-width:0;">
                                <div class="small fw-semibold text-truncate"><?= esc($act['user_name'] ?? 'Système') ?></div>
                                <div class="small text-muted text-truncate"><?= esc($act['description']) ?></div>
                                <div class="text-muted" style="font-size:.7rem;"><?= date('d/m H:i', strtotime($act['created_at'])) ?></div>
                            </div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($recent_activity)) : ?>
                    <li class="list-group-item text-muted text-center py-4">Aucune activité récente</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Biens par statut -->
<div class="row g-3">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-fill text-success"></i> Résumé du portefeuille immobilier
                <a href="<?= base_url('admin/properties') ?>" class="ms-auto btn btn-sm btn-outline-success">Gérer les biens</a>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <?php
                    $propStatusMap = [
                        'available' => ['label'=>'Disponibles','color'=>'success','icon'=>'bi-check-circle'],
                        'reserved'  => ['label'=>'Réservés',  'color'=>'warning','icon'=>'bi-bookmark'],
                        'sold'      => ['label'=>'Vendus',    'color'=>'danger', 'icon'=>'bi-trophy'],
                        'published' => ['label'=>'Publiés',   'color'=>'primary','icon'=>'bi-megaphone'],
                    ];
                    foreach ($propStatusMap as $key => $meta) :
                    ?>
                    <div class="col-6 col-md-3 mb-3 mb-md-0">
                        <div class="display-6 fw-bold text-<?= $meta['color'] ?>"><?= $property_stats[$key] ?? 0 ?></div>
                        <div class="text-muted small"><i class="bi <?= $meta['icon'] ?> me-1"></i><?= $meta['label'] ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
