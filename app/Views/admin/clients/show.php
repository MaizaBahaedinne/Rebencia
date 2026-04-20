<?php
$perms   = session()->get('permissions') ?? [];
$tMeta   = $typeLabels[$client['client_type']] ?? ['label' => $client['client_type'], 'color' => 'secondary', 'icon' => 'bi-person'];
$sMeta   = $statusLabels[$client['status']] ?? ['label' => $client['status'], 'color' => 'secondary'];
$srcLbl  = $sourceLabels[$client['source']] ?? $client['source'];
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/clients') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <i class="bi <?= $tMeta['icon'] ?> me-2 text-<?= $tMeta['color'] ?>"></i>
            <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
        </h4>
        <span class="badge text-bg-<?= $tMeta['color'] ?> me-1"><?= $tMeta['label'] ?></span>
        <span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle">
            <?= $sMeta['label'] ?>
        </span>
    </div>
    <div class="ms-auto d-flex gap-2">
        <?php if (in_array('clients.edit', $perms)): ?>
        <a href="<?= base_url('admin/clients/' . $client['id'] . '/edit') ?>"
           class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <?php if (in_array('clients.delete', $perms)): ?>
        <form method="POST"
              action="<?= base_url('admin/clients/' . $client['id'] . '/delete') ?>"
              onsubmit="return confirm('Supprimer ce client ?')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger">
                <i class="bi bi-trash me-1"></i>Supprimer
            </button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="row g-4">

    <!-- Infos de base -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-person-vcard me-1 text-primary"></i> Coordonnées
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small fw-normal">Téléphone</dt>
                    <dd class="col-7 fw-semibold">
                        <a href="tel:<?= esc($client['phone']) ?>"><?= esc($client['phone']) ?></a>
                    </dd>

                    <?php if ($client['email']): ?>
                    <dt class="col-5 text-muted small fw-normal">Email</dt>
                    <dd class="col-7">
                        <a href="mailto:<?= esc($client['email']) ?>"><?= esc($client['email']) ?></a>
                    </dd>
                    <?php endif; ?>

                    <?php if ($client['profession']): ?>
                    <dt class="col-5 text-muted small fw-normal">Profession</dt>
                    <dd class="col-7"><?= esc($client['profession']) ?></dd>
                    <?php endif; ?>

                    <?php if ($client['company']): ?>
                    <dt class="col-5 text-muted small fw-normal">Entreprise</dt>
                    <dd class="col-7"><?= esc($client['company']) ?></dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Adresse -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-geo-alt me-1 text-info"></i> Adresse
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($client['address']): ?>
                    <dt class="col-5 text-muted small fw-normal">Rue</dt>
                    <dd class="col-7"><?= esc($client['address']) ?></dd>
                    <?php endif; ?>

                    <?php if ($client['pays_name']): ?>
                    <dt class="col-5 text-muted small fw-normal">Pays</dt>
                    <dd class="col-7"><?= esc($client['pays_name']) ?></dd>
                    <?php endif; ?>

                    <?php if ($client['region_name']): ?>
                    <dt class="col-5 text-muted small fw-normal">Région</dt>
                    <dd class="col-7"><?= esc($client['region_name']) ?></dd>
                    <?php endif; ?>

                    <?php if ($client['ville_name']): ?>
                    <dt class="col-5 text-muted small fw-normal">Ville</dt>
                    <dd class="col-7"><?= esc($client['ville_name']) ?></dd>
                    <?php endif; ?>

                    <?php if ($client['postal_code']): ?>
                    <dt class="col-5 text-muted small fw-normal">Code postal</dt>
                    <dd class="col-7"><code><?= esc($client['postal_code']) ?></code></dd>
                    <?php endif; ?>

                    <?php if (! $client['address'] && ! $client['pays_name'] && ! $client['ville_name']): ?>
                    <dd class="col-12 text-muted fst-italic small">Aucune adresse renseignée</dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Besoin immobilier -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-house-heart me-1 text-warning"></i> Besoin immobilier
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <?php if ($client['property_type_name']): ?>
                    <dt class="col-5 text-muted small fw-normal">Type de bien</dt>
                    <dd class="col-7">
                        <?php if ($client['property_type_icon']): ?>
                        <i class="bi <?= esc($client['property_type_icon']) ?> me-1 text-primary"></i>
                        <?php endif; ?>
                        <?= esc($client['property_type_name']) ?>
                    </dd>
                    <?php endif; ?>

                    <?php if (in_array($client['client_type'], ['acheteur', 'locataire', 'investisseur'])): ?>
                        <?php if ($client['budget_min'] || $client['budget_max']): ?>
                        <dt class="col-5 text-muted small fw-normal">Budget</dt>
                        <dd class="col-7">
                            <?php
                            $parts = [];
                            if ($client['budget_min']) $parts[] = 'Min : ' . number_format($client['budget_min'], 0, ',', ' ') . ' TND';
                            if ($client['budget_max']) $parts[] = 'Max : ' . number_format($client['budget_max'], 0, ',', ' ') . ' TND';
                            echo implode(' — ', $parts);
                            ?>
                        </dd>
                        <?php endif; ?>

                        <?php if ($client['desired_zone']): ?>
                        <dt class="col-5 text-muted small fw-normal">Zone recherchée</dt>
                        <dd class="col-7"><?= esc($client['desired_zone']) ?></dd>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if ($client['client_type'] === 'proprietaire'): ?>
                        <?php if ($client['owner_location']): ?>
                        <dt class="col-5 text-muted small fw-normal">Localisation</dt>
                        <dd class="col-7"><?= esc($client['owner_location']) ?></dd>
                        <?php endif; ?>

                        <?php if ($client['desired_price']): ?>
                        <dt class="col-5 text-muted small fw-normal">Prix souhaité</dt>
                        <dd class="col-7 fw-semibold"><?= number_format($client['desired_price'], 0, ',', ' ') ?> TND</dd>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (! $client['property_type_name'] && ! $client['budget_min'] && ! $client['desired_zone'] && ! $client['owner_location']): ?>
                    <dd class="col-12 text-muted fst-italic small">Aucun besoin renseigné</dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- CRM -->
    <div class="col-md-6">
        <div class="card shadow-sm h-100">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-diagram-3 me-1 text-success"></i> CRM
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-5 text-muted small fw-normal">Statut</dt>
                    <dd class="col-7">
                        <span class="badge bg-<?= $sMeta['color'] ?>-subtle text-<?= $sMeta['color'] ?> border border-<?= $sMeta['color'] ?>-subtle">
                            <?= $sMeta['label'] ?>
                        </span>
                    </dd>

                    <dt class="col-5 text-muted small fw-normal">Agent</dt>
                    <dd class="col-7">
                        <?= $client['agent_first'] ? esc($client['agent_first'] . ' ' . $client['agent_last']) : '<span class="text-muted">Non assigné</span>' ?>
                    </dd>

                    <dt class="col-5 text-muted small fw-normal">Source</dt>
                    <dd class="col-7"><?= esc($srcLbl) ?></dd>

                    <dt class="col-5 text-muted small fw-normal">Créé le</dt>
                    <dd class="col-7 small"><?= date('d/m/Y H\hi', strtotime($client['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <!-- Notes -->
    <?php if ($client['notes']): ?>
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white">
                <i class="bi bi-chat-text me-1 text-muted"></i> Notes
            </div>
            <div class="card-body">
                <p class="mb-0" style="white-space:pre-wrap;"><?= esc($client['notes']) ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
