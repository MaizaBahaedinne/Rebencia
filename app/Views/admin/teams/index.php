<?php $perms = session()->get('permissions') ?? []; ?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-diagram-3-fill text-primary me-2"></i>Gestion des équipes</h4>
        <p class="text-muted small mb-0"><?= count($teams) ?> équipe(s) — membres, performances et organisation</p>
    </div>
    <?php if (in_array('agencies.create', $perms)): ?>
    <a href="<?= base_url('admin/agencies/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Nouvelle agence / équipe
    </a>
    <?php endif; ?>
</div>

<!-- Flash messages -->
<?php if (session()->has('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <i class="bi bi-check-circle me-2"></i><?= esc(session('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->has('error')): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <i class="bi bi-exclamation-triangle me-2"></i><?= esc(session('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Stats globales -->
<?php
$totalMembers    = array_sum(array_column($teams, 'members_count'));
$totalProperties = array_sum(array_column($teams, 'properties_count'));
$totalLeads      = array_sum(array_column($teams, 'leads_count'));
$totalWon        = array_sum(array_column($teams, 'leads_won'));
?>
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 bg-primary bg-opacity-10">
                    <i class="bi bi-diagram-3-fill text-primary fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= count($teams) ?></div>
                    <div class="text-muted small">Équipes</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 bg-success bg-opacity-10">
                    <i class="bi bi-people-fill text-success fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $totalMembers ?></div>
                    <div class="text-muted small">Membres au total</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 bg-info bg-opacity-10">
                    <i class="bi bi-house-fill text-info fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $totalProperties ?></div>
                    <div class="text-muted small">Biens gérés</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-3 p-2 bg-warning bg-opacity-10">
                    <i class="bi bi-trophy-fill text-warning fs-4"></i>
                </div>
                <div>
                    <div class="fw-bold fs-5"><?= $totalWon ?></div>
                    <div class="text-muted small">Leads gagnés</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Grille des équipes -->
<?php if (empty($teams)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-diagram-3 fs-1 opacity-25 d-block mb-3"></i>
        <p class="mb-0">Aucune équipe trouvée.</p>
    </div>
</div>
<?php else: ?>
<div class="row g-4">
    <?php foreach ($teams as $team): ?>
    <div class="col-12 col-md-6 col-xl-4">
        <div class="card border-0 shadow-sm h-100 team-card">
            <div class="card-body">
                <!-- Header équipe -->
                <div class="d-flex align-items-start gap-3 mb-3">
                    <div class="team-avatar flex-shrink-0">
                        <?php if ($team['logo']): ?>
                        <img src="<?= base_url('uploads/' . esc($team['logo'])) ?>"
                             alt="<?= esc($team['name']) ?>"
                             class="rounded-3 object--fit-cover"
                             style="width:52px;height:52px;object-fit:cover;">
                        <?php else: ?>
                        <div class="rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                             style="width:52px;height:52px;">
                            <i class="bi bi-buildings text-primary fs-4"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h6 class="mb-0 fw-bold text-truncate"><?= esc($team['name']) ?></h6>
                            <?php if ($team['is_active']): ?>
                            <span class="badge bg-success-subtle text-success" style="font-size:.65rem;">Actif</span>
                            <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;">Inactif</span>
                            <?php endif; ?>
                        </div>
                        <?php if ($team['city']): ?>
                        <div class="text-muted small mt-1">
                            <i class="bi bi-geo-alt me-1"></i><?= esc($team['city']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Stats équipe -->
                <div class="row g-2 text-center mb-3">
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-light">
                            <div class="fw-bold text-primary"><?= $team['members_count'] ?></div>
                            <div style="font-size:.65rem;" class="text-muted">Membres</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-light">
                            <div class="fw-bold text-info"><?= $team['properties_count'] ?></div>
                            <div style="font-size:.65rem;" class="text-muted">Biens</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-light">
                            <div class="fw-bold text-warning"><?= $team['leads_count'] ?></div>
                            <div style="font-size:.65rem;" class="text-muted">Leads</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-2 rounded-3 bg-light">
                            <div class="fw-bold text-success"><?= $team['leads_won'] ?></div>
                            <div style="font-size:.65rem;" class="text-muted">Gagnés</div>
                        </div>
                    </div>
                </div>

                <!-- Taux de conversion -->
                <?php
                $rate = $team['leads_count'] > 0
                    ? round($team['leads_won'] / $team['leads_count'] * 100)
                    : 0;
                $barColor = $rate >= 50 ? 'bg-success' : ($rate >= 25 ? 'bg-warning' : 'bg-danger');
                ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Taux de conversion</span>
                        <span class="fw-semibold"><?= $rate ?>%</span>
                    </div>
                    <div class="progress" style="height:5px;">
                        <div class="progress-bar <?= $barColor ?>" style="width:<?= $rate ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-top-0 pt-0 pb-3 px-3">
                <a href="<?= base_url('admin/teams/' . $team['id']) ?>"
                   class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-eye me-1"></i>Voir l'équipe
                </a>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.team-card { transition: transform .18s ease, box-shadow .18s ease; }
.team-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(108,99,255,.12) !important; }
</style>
