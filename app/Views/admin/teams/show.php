<?php $perms = session()->get('permissions') ?? []; ?>

<!-- BREADCRUMB -->
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb small">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/teams') ?>">Équipes</a></li>
        <li class="breadcrumb-item active"><?= esc($team['name']) ?></li>
    </ol>
</nav>

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

<!-- EN-TÊTE ÉQUIPE -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center gap-4 flex-wrap">
            <!-- Logo -->
            <?php if ($team['logo']): ?>
            <img src="<?= base_url('uploads/' . esc($team['logo'])) ?>"
                 alt="<?= esc($team['name']) ?>"
                 class="rounded-3" style="width:72px;height:72px;object-fit:cover;">
            <?php else: ?>
            <div class="rounded-3 bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:72px;height:72px;">
                <i class="bi bi-buildings text-primary" style="font-size:2rem;"></i>
            </div>
            <?php endif; ?>

            <!-- Infos -->
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <h4 class="fw-bold mb-0"><?= esc($team['name']) ?></h4>
                    <?php if ($team['is_active']): ?>
                    <span class="badge bg-success">Active</span>
                    <?php else: ?>
                    <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                </div>
                <div class="text-muted small d-flex flex-wrap gap-3">
                    <?php if ($team['city']): ?>
                    <span><i class="bi bi-geo-alt me-1"></i><?= esc($team['city']) ?></span>
                    <?php endif; ?>
                    <?php if ($team['email']): ?>
                    <span><i class="bi bi-envelope me-1"></i><?= esc($team['email']) ?></span>
                    <?php endif; ?>
                    <?php if ($team['phone']): ?>
                    <span><i class="bi bi-telephone me-1"></i><?= esc($team['phone']) ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-flex gap-2">
                <?php if ($canManage): ?>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="bi bi-person-plus me-1"></i>Ajouter un membre
                </button>
                <?php endif; ?>
                <a href="<?= base_url('admin/teams/' . $team['id'] . '/orgchart') ?>" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-diagram-3-fill me-1"></i>Organigramme
                </a>
                <?php if (in_array('agencies.view', $perms)): ?>
                <a href="<?= base_url('admin/agencies/' . $team['id']) ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil me-1"></i>Modifier l'agence
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- KPI CARDS -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-primary"><?= count($members) ?></div>
                <div class="text-muted small"><i class="bi bi-people me-1"></i>Membres</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-info"><?= $team['properties_count'] ?></div>
                <div class="text-muted small"><i class="bi bi-house me-1"></i>Biens</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-warning"><?= $team['leads_count'] ?></div>
                <div class="text-muted small"><i class="bi bi-person-lines-fill me-1"></i>Leads</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center">
            <div class="card-body py-3">
                <div class="fs-3 fw-bold text-success"><?= $team['leads_won'] ?></div>
                <div class="text-muted small"><i class="bi bi-trophy me-1"></i>Leads gagnés</div>
            </div>
        </div>
    </div>
</div>

<!-- MEMBRES -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
        <span class="fw-semibold"><i class="bi bi-people-fill me-2 text-primary"></i>Membres de l'équipe</span>
        <span class="badge bg-primary-subtle text-primary"><?= count($members) ?> membre(s)</span>
    </div>
    <div class="card-body p-0">
        <?php if (empty($members)): ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-people fs-1 opacity-25 d-block mb-2"></i>
            <p class="mb-0 small">Aucun membre dans cette équipe.</p>
            <?php if ($canManage): ?>
            <button class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                <i class="bi bi-person-plus me-1"></i>Ajouter le premier membre
            </button>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Membre</th>
                        <th>Rôle</th>
                        <th class="text-center">Biens</th>
                        <th class="text-center">Leads</th>
                        <th class="text-center">Visites</th>
                        <th class="text-center">Gagnés</th>
                        <th class="text-center">Statut</th>
                        <?php if ($canManage): ?>
                        <th class="text-end">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($members as $member): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <?php if ($member['avatar']): ?>
                            <img src="<?= base_url('uploads/' . esc($member['avatar'])) ?>"
                                 class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                            <?php else: ?>
                            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width:36px;height:36px;font-size:.8rem;font-weight:600;color:var(--rb-primary);">
                                <?= strtoupper(substr($member['first_name'], 0, 1) . substr($member['last_name'], 0, 1)) ?>
                            </div>
                            <?php endif; ?>
                            <div>
                                <div class="fw-semibold small">
                                    <?= esc($member['first_name'] . ' ' . $member['last_name']) ?>
                                </div>
                                <div class="text-muted" style="font-size:.72rem;"><?= esc($member['email']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge rounded-pill"
                              style="background:<?= esc($member['role_color']) ?>20;color:<?= esc($member['role_color']) ?>;border:1px solid <?= esc($member['role_color']) ?>40;">
                            <?= esc($member['role_label']) ?>
                        </span>
                    </td>
                    <td class="text-center fw-semibold"><?= $member['properties_count'] ?></td>
                    <td class="text-center fw-semibold"><?= $member['leads_count'] ?></td>
                    <td class="text-center fw-semibold"><?= $member['visits_count'] ?></td>
                    <td class="text-center">
                        <span class="fw-bold text-success"><?= $member['leads_won'] ?></span>
                    </td>
                    <td class="text-center">
                        <?php
                        $statusMap = [
                            'active'    => ['bg-success', 'Actif'],
                            'pending'   => ['bg-warning text-dark', 'En attente'],
                            'suspended' => ['bg-danger', 'Suspendu'],
                        ];
                        [$badgeCls, $badgeLbl] = $statusMap[$member['status']] ?? ['bg-secondary', $member['status']];
                        ?>
                        <span class="badge <?= $badgeCls ?>"><?= $badgeLbl ?></span>
                    </td>
                    <?php if ($canManage): ?>
                    <td class="text-end">
                        <div class="d-flex gap-1 justify-content-end">
                            <a href="<?= base_url('admin/users/' . $member['id']) ?>"
                               class="btn btn-xs btn-outline-secondary" title="Voir le profil">
                                <i class="bi bi-person"></i>
                            </a>
                            <form method="post" action="<?= base_url('admin/teams/' . $team['id'] . '/remove-member') ?>"
                                  onsubmit="return confirm('Retirer <?= esc($member['first_name'] . ' ' . $member['last_name']) ?> de cette équipe ?')">
                                <?= csrf_field() ?>
                                <input type="hidden" name="user_id" value="<?= $member['id'] ?>">
                                <button type="submit" class="btn btn-xs btn-outline-danger" title="Retirer de l'équipe">
                                    <i class="bi bi-person-dash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ACTIVITÉ RÉCENTE -->
<?php if (!empty($recentActivity)): ?>
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <span class="fw-semibold"><i class="bi bi-clock-history me-2 text-primary"></i>Activité récente</span>
    </div>
    <div class="card-body p-0">
        <ul class="list-group list-group-flush">
            <?php foreach ($recentActivity as $log): ?>
            <li class="list-group-item py-2 px-3">
                <div class="d-flex align-items-start gap-2">
                    <div class="mt-1">
                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem;"><?= esc($log['module']) ?></span>
                    </div>
                    <div class="flex-grow-1">
                        <div class="small"><?= esc($log['description'] ?: $log['action']) ?></div>
                        <div class="text-muted" style="font-size:.72rem;">
                            <i class="bi bi-person me-1"></i><?= esc($log['first_name'] . ' ' . $log['last_name']) ?>
                            &nbsp;·&nbsp;
                            <i class="bi bi-clock me-1"></i><?= date('d/m/Y H:i', strtotime($log['created_at'])) ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>

<!-- MODAL AJOUTER UN MEMBRE -->
<?php if ($canManage): ?>
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2 text-primary"></i>Ajouter un membre</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="post" action="<?= base_url('admin/teams/' . $team['id'] . '/add-member') ?>">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <?php if (empty($available)): ?>
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                        Tous les utilisateurs sont déjà assignés à une équipe.
                    </div>
                    <?php else: ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sélectionner un utilisateur</label>
                        <input type="text" id="memberSearch" class="form-control form-control-sm mb-2"
                               placeholder="Rechercher par nom ou email...">
                        <div class="list-group" id="memberList" style="max-height:300px;overflow-y:auto;">
                            <?php foreach ($available as $u): ?>
                            <label class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 member-item">
                                <input class="form-check-input me-1" type="radio" name="user_id" value="<?= $u['id'] ?>" required>
                                <div>
                                    <div class="fw-semibold small"><?= esc($u['first_name'] . ' ' . $u['last_name']) ?></div>
                                    <div class="text-muted" style="font-size:.72rem;">
                                        <?= esc($u['email']) ?>
                                        &nbsp;·&nbsp;
                                        <span class="badge rounded-pill"
                                              style="background:<?= esc($u['role_color']) ?>20;color:<?= esc($u['role_color']) ?>;">
                                            <?= esc($u['role_label']) ?>
                                        </span>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button>
                    <?php if (!empty($available)): ?>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-person-plus me-1"></i>Ajouter
                    </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Recherche dans la liste des membres disponibles
document.getElementById('memberSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.member-item').forEach(function (el) {
        el.style.display = el.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
});
</script>
<?php endif; ?>

<style>
.btn-xs { padding: .2rem .45rem; font-size: .75rem; }
</style>
