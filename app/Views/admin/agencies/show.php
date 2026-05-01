
<?php
$perms    = session()->get('permissions') ?? [];
$isActive = (bool) $agency['is_active'];
$propTypeLabels = [
    'apartment' => ['label' => 'Appartement', 'icon' => 'bi-building',       'color' => 'primary'],
    'house'     => ['label' => 'Maison',       'icon' => 'bi-house',          'color' => 'success'],
    'villa'     => ['label' => 'Villa',        'icon' => 'bi-house-fill',     'color' => 'warning'],
    'commercial'=> ['label' => 'Commercial',   'icon' => 'bi-shop',           'color' => 'danger'],
    'land'      => ['label' => 'Terrain',      'icon' => 'bi-map',            'color' => 'info'],
    'office'    => ['label' => 'Bureau',       'icon' => 'bi-briefcase',      'color' => 'secondary'],
];
$statusColors = [
    'available' => 'success', 'reserved' => 'warning',
    'sold' => 'danger', 'rented' => 'info', 'inactive' => 'secondary',
];
?>

<!-- En-tête -->
<div class="d-flex align-items-start gap-3 mb-4 flex-wrap">
    <a href="<?= base_url('admin/agencies') ?>" class="btn btn-sm btn-light mt-1"><i class="bi bi-arrow-left"></i></a>
    <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <?php if (! empty($agency['logo'])): ?>
            <img src="<?= base_url($agency['logo']) ?>" alt=""
                 style="width:40px;height:40px;object-fit:cover;border-radius:.5rem;">
            <?php endif; ?>
            <h4 class="fw-bold mb-0"><?= esc($agency['name']) ?></h4>
            <span class="badge <?= $isActive ? 'text-bg-success' : 'text-bg-secondary' ?>">
                <?= $isActive ? 'Active' : 'Inactive' ?>
            </span>
        </div>
        <div class="text-muted small mt-1">
            <?php if ($agency['city']): ?><i class="bi bi-geo-alt me-1"></i><?= esc($agency['city']) ?> &nbsp;<?php endif; ?>
            <?php if ($agency['zone_name']): ?><i class="bi bi-map me-1"></i>Zone : <?= esc($agency['zone_name']) ?><?php endif; ?>
        </div>
    </div>
    <div class="d-flex gap-2 flex-wrap mt-1">
        <?php if (in_array('agencies.edit', $perms)): ?>
        <a href="<?= base_url('admin/agencies/' . $agency['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-pencil me-1"></i>Modifier
        </a>
        <?php endif; ?>
        <?php if (in_array('agencies.delete', $perms)): ?>
        <form method="POST" action="<?= base_url('admin/agencies/' . $agency['id'] . '/delete') ?>"
              onsubmit="return confirm('Supprimer cette agence ?')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash me-1"></i>Supprimer</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if (session()->has('success')): ?>
<div class="alert alert-success alert-dismissible fade show">
    <?= session('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<!-- Onglets -->
<ul class="nav nav-tabs mb-0" style="border-bottom:2px solid #dee2e6;">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-infos" type="button">
            <i class="bi bi-info-circle me-1"></i>Informations
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-equipe" type="button">
            <i class="bi bi-people me-1"></i>Équipe
            <span class="badge text-bg-primary ms-1" style="font-size:.65rem;"><?= (int) $agency['users_count'] ?></span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tab-biens" type="button">
            <i class="bi bi-building me-1"></i>Biens
            <span class="badge text-bg-success ms-1" style="font-size:.65rem;"><?= (int) $agency['properties_count'] ?></span>
        </button>
    </li>
</ul>

<div class="tab-content pt-4">

<!-- TAB : Informations -->
<div class="tab-pane fade show active" id="tab-infos">
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header fw-semibold bg-white"><i class="bi bi-buildings me-1 text-primary"></i> Coordonnées</div>
            <div class="card-body">
                <dl class="row mb-0 g-2">
                    <?php if ($agency['email']): ?>
                    <dt class="col-4 text-muted fw-normal small">Email</dt>
                    <dd class="col-8"><a href="mailto:<?= esc($agency['email']) ?>"><?= esc($agency['email']) ?></a></dd>
                    <?php endif; ?>
                    <?php if ($agency['phone']): ?>
                    <dt class="col-4 text-muted fw-normal small">Téléphone</dt>
                    <dd class="col-8"><a href="tel:<?= esc($agency['phone']) ?>"><?= esc($agency['phone']) ?></a></dd>
                    <?php endif; ?>
                    <?php if ($agency['address']): ?>
                    <dt class="col-4 text-muted fw-normal small">Adresse</dt>
                    <dd class="col-8"><?= esc($agency['address']) ?></dd>
                    <?php endif; ?>
                    <?php if ($agency['city']): ?>
                    <dt class="col-4 text-muted fw-normal small">Ville</dt>
                    <dd class="col-8"><?= esc($agency['city']) ?></dd>
                    <?php endif; ?>
                    <?php if ($agency['zone_name']): ?>
                    <dt class="col-4 text-muted fw-normal small">Zone</dt>
                    <dd class="col-8"><span class="badge text-bg-info"><?= esc($agency['zone_name']) ?></span></dd>
                    <?php endif; ?>
                    <dt class="col-4 text-muted fw-normal small">Créée le</dt>
                    <dd class="col-8 small"><?= date('d/m/Y', strtotime($agency['created_at'])) ?></dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <!-- Stats -->
        <div class="row g-3">
            <div class="col-6">
                <div class="card shadow-sm text-center py-3">
                    <div class="display-6 fw-bold text-primary"><?= (int) $agency['users_count'] ?></div>
                    <div class="text-muted small"><i class="bi bi-people me-1"></i>Membres</div>
                </div>
            </div>
            <div class="col-6">
                <div class="card shadow-sm text-center py-3">
                    <div class="display-6 fw-bold text-success"><?= (int) $agency['properties_count'] ?></div>
                    <div class="text-muted small"><i class="bi bi-building me-1"></i>Biens</div>
                </div>
            </div>
        </div>

        <?php if ($agency['description']): ?>
        <div class="card shadow-sm mt-3">
            <div class="card-header fw-semibold bg-white"><i class="bi bi-chat-text me-1 text-muted"></i> Description</div>
            <div class="card-body"><p class="mb-0" style="white-space:pre-wrap;"><?= esc($agency['description']) ?></p></div>
        </div>
        <?php endif; ?>

        <!-- Note clients partagés -->
        <div class="alert alert-info mt-3 mb-0" style="font-size:.875rem;">
            <i class="bi bi-people-fill me-1"></i>
            <strong>Clients partagés :</strong> les clients sont visibles par toutes les agences. Chaque agence gère ses propres biens et équipe.
        </div>
    </div>
</div>
</div>

<!-- TAB : Équipe -->
<div class="tab-pane fade" id="tab-equipe">
<?php if (empty($members)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-people fs-1 opacity-25 d-block mb-2"></i>
    Aucun membre rattaché à cette agence.
    <?php if (in_array('users.create', $perms)): ?>
    <div class="mt-3">
        <a href="<?= base_url('admin/users/create') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-person-plus me-1"></i>Ajouter un membre
        </a>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span class="text-muted small"><?= count($members) ?> membre(s)</span>
    <?php if (in_array('users.create', $perms)): ?>
    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-sm btn-outline-primary">
        <i class="bi bi-person-plus me-1"></i>Ajouter un membre
    </a>
    <?php endif; ?>
</div>
<div class="row g-3">
    <?php foreach ($members as $m): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="card shadow-sm">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                     style="width:42px;height:42px;font-size:.95rem;background:<?= esc($m['role_color']) ?>;">
                    <?= strtoupper(mb_substr($m['first_name'], 0, 1)) ?>
                </div>
                <div class="flex-grow-1 min-width-0">
                    <div class="fw-semibold text-truncate"><?= esc($m['first_name'] . ' ' . $m['last_name']) ?></div>
                    <div class="small text-muted text-truncate"><?= esc($m['email']) ?></div>
                    <span class="badge mt-1" style="background-color:<?= esc($m['role_color']) ?>;font-size:.65rem;">
                        <?= esc($m['role_label']) ?>
                    </span>
                </div>
                <?php if (in_array('users.view', $perms)): ?>
                <a href="<?= base_url('admin/users/' . $m['id']) ?>" class="btn btn-sm btn-light flex-shrink-0">
                    <i class="bi bi-eye"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</div>

<!-- TAB : Biens -->
<div class="tab-pane fade" id="tab-biens">
<?php if (empty($properties)): ?>
<div class="text-center text-muted py-5">
    <i class="bi bi-building fs-1 opacity-25 d-block mb-2"></i>
    Aucun bien rattaché à cette agence.
    <?php if (in_array('properties.create', $perms)): ?>
    <div class="mt-3">
        <a href="<?= base_url('admin/properties/create') ?>" class="btn btn-sm btn-outline-primary">
            <i class="bi bi-plus-circle me-1"></i>Ajouter un bien
        </a>
    </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <span class="text-muted small">Derniers biens rattachés à cette agence</span>
    <a href="<?= base_url('admin/properties') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-box-arrow-up-right me-1"></i>Tous les biens
    </a>
</div>
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Réf.</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Agent</th>
                <th>Statut</th>
                <th>Prix</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($properties as $p): ?>
            <?php
            $pt = $propTypeLabels[$p['type']] ?? ['label'=>$p['type'],'icon'=>'bi-house','color'=>'secondary'];
            $sc = $statusColors[$p['status']] ?? 'secondary';
            ?>
            <tr>
                <td class="text-muted small"><?= esc($p['reference']) ?></td>
                <td><?= esc($p['title']) ?></td>
                <td><span class="badge bg-<?= $pt['color'] ?>-subtle text-<?= $pt['color'] ?> border"><i class="bi <?= $pt['icon'] ?> me-1"></i><?= $pt['label'] ?></span></td>
                <td class="small"><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></td>
                <td><span class="badge text-bg-<?= $sc ?>"><?= esc($p['status']) ?></span></td>
                <td class="fw-semibold"><?= number_format($p['price'], 0, ',', ' ') ?> TND</td>
                <td>
                    <?php if (in_array('properties.view', $perms)): ?>
                    <a href="<?= base_url('admin/properties/' . $p['id']) ?>" class="btn btn-sm btn-light"><i class="bi bi-eye"></i></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if ((int) $agency['properties_count'] > count($properties)): ?>
<div class="text-center mt-2">
    <a href="<?= base_url('admin/properties') ?>" class="btn btn-sm btn-outline-primary">
        Voir tous les <?= (int) $agency['properties_count'] ?> biens →
    </a>
</div>
<?php endif; ?>
<?php endif; ?>
</div>

</div><!-- /.tab-content -->


