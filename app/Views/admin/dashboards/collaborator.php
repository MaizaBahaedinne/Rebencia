<!-- DASHBOARD COLLABORATEUR -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Bonjour, <?= esc(session()->get('user_name')) ?></h4>
        <p class="text-muted mb-0">Vos tâches du jour – <?= date('d/m/Y') ?></p>
    </div>
    <span class="badge badge-collaborator text-white px-3 py-2">
        <i class="bi bi-person-badge me-1"></i>Collaborateur
    </span>
</div>

<!-- KPIs minimalistes -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">
                <div class="fs-2 fw-bold text-warning"><?= count($my_leads) ?></div>
                <div class="text-muted small">Mes leads</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">
                <div class="fs-2 fw-bold text-primary"><?= count($my_properties) ?></div>
                <div class="text-muted small">Mes biens</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">
                <?php $urgentLeads = array_filter($my_leads, fn($l) => $l['priority'] === 'high'); ?>
                <div class="fs-2 fw-bold text-danger"><?= count($urgentLeads) ?></div>
                <div class="text-muted small">Priorité haute</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card stat-card shadow-sm">
            <div class="card-body text-center">
                <?php
                $today = date('Y-m-d');
                $followUps = array_filter($my_leads, fn($l) => ! empty($l['next_follow_up']) && $l['next_follow_up'] <= $today);
                ?>
                <div class="fs-2 fw-bold text-warning"><?= count($followUps) ?></div>
                <div class="text-muted small">À relancer</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <!-- Mes Leads -->
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-person-lines-fill text-warning"></i> Mes leads
                <a href="<?= base_url('admin/leads') ?>" class="ms-auto btn btn-sm btn-outline-warning">Voir tout</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($my_leads)) : ?>
                <p class="text-muted text-center py-4">Aucun lead assigné.</p>
                <?php else : ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 small">
                        <thead class="table-light"><tr><th>Nom</th><th>Statut</th><th>Priorité</th><th>Relance</th><th></th></tr></thead>
                        <tbody>
                        <?php
                        $stMap = ['new'=>'secondary','contacted'=>'info','visit'=>'primary','negotiation'=>'warning','sold'=>'success','lost'=>'danger'];
                        $stLbl = ['new'=>'Nouveau','contacted'=>'Contacté','visit'=>'Visite','negotiation'=>'Négociation','sold'=>'Vendu','lost'=>'Perdu'];
                        $prMap = ['high'=>'danger','medium'=>'warning','low'=>'secondary'];
                        ?>
                        <?php foreach ($my_leads as $lead) : ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($lead['first_name']) ?> <?= esc($lead['last_name']) ?></td>
                            <td><span class="badge bg-<?= $stMap[$lead['status']] ?? 'secondary' ?>"><?= $stLbl[$lead['status']] ?? $lead['status'] ?></span></td>
                            <td><span class="badge bg-<?= $prMap[$lead['priority']] ?? 'secondary' ?>"><?= $lead['priority'] ?></span></td>
                            <td class="<?= (! empty($lead['next_follow_up']) && $lead['next_follow_up'] <= $today) ? 'text-danger fw-bold' : 'text-muted' ?>">
                                <?= $lead['next_follow_up'] ? date('d/m', strtotime($lead['next_follow_up'])) : '–' ?>
                            </td>
                            <td><a href="<?= base_url('admin/leads/' . $lead['id']) ?>" class="btn btn-xs btn-outline-primary" style="font-size:.75rem;padding:.2rem .5rem;">Voir</a></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Mes Biens -->
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-semibold d-flex align-items-center gap-2">
                <i class="bi bi-building text-primary"></i> Mes biens
            </div>
            <div class="card-body p-0">
                <?php if (empty($my_properties)) : ?>
                <p class="text-muted text-center py-4">Aucun bien assigné.</p>
                <?php else : ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($my_properties as $p) :
                        $pSMap = ['available'=>'success','reserved'=>'warning','sold'=>'danger','inactive'=>'secondary'];
                        $pSLbl = ['available'=>'Disponible','reserved'=>'Réservé','sold'=>'Vendu','inactive'=>'Inactif'];
                    ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                        <div>
                            <div class="fw-semibold small"><?= esc($p['title']) ?></div>
                            <div class="text-muted" style="font-size:.75rem;"><?= esc($p['city']) ?> – <?= number_format($p['price'], 0, ',', ' ') ?> TND</div>
                        </div>
                        <span class="badge bg-<?= $pSMap[$p['status']] ?? 'secondary' ?>"><?= $pSLbl[$p['status']] ?? $p['status'] ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
