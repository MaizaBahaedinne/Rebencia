<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i>Détails de l'Agence
        </h1>
        <div>
            <a href="<?= base_url('admin/agencies/edit/' . $agency['id']) ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="<?= base_url('admin/agencies') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Générales</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Nom de l'Agence</label>
                            <p class="h5"><?= esc($agency['name']) ?></p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Code</label>
                            <p class="h5"><span class="badge bg-secondary"><?= esc($agency['code']) ?></span></p>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Type</label>
                            <p>
                                <?php if ($agency['type'] === 'siege'): ?>
                                    <span class="badge bg-primary fs-6">Siège</span>
                                <?php else: ?>
                                    <span class="badge bg-info fs-6">Agence</span>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label class="text-muted small">Adresse</label>
                            <p><i class="fas fa-map-marker-alt me-2 text-danger"></i><?= esc($agency['address']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Ville</label>
                            <p><?= esc($agency['city']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Gouvernorat</label>
                            <p><?= esc($agency['governorate']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Code Postal</label>
                            <p><?= esc($agency['postal_code'] ?? 'N/A') ?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Téléphone</label>
                            <p>
                                <?php if (!empty($agency['phone'])): ?>
                                    <i class="fas fa-phone me-2 text-success"></i>
                                    <a href="tel:<?= esc($agency['phone']) ?>"><?= esc($agency['phone']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Non renseigné</span>
                                <?php endif ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Email</label>
                            <p>
                                <?php if (!empty($agency['email'])): ?>
                                    <i class="fas fa-envelope me-2 text-info"></i>
                                    <a href="mailto:<?= esc($agency['email']) ?>"><?= esc($agency['email']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Non renseigné</span>
                                <?php endif ?>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <label class="text-muted small">Site Web</label>
                            <p>
                                <?php if (!empty($agency['website'])): ?>
                                    <i class="fas fa-globe me-2 text-primary"></i>
                                    <a href="<?= esc($agency['website']) ?>" target="_blank"><?= esc($agency['website']) ?></a>
                                <?php else: ?>
                                    <span class="text-muted">Non renseigné</span>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>

                    <?php if (!empty($agency['latitude']) && !empty($agency['longitude'])): ?>
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <label class="text-muted small">Localisation GPS</label>
                                <p>
                                    <i class="fas fa-map-marked-alt me-2 text-danger"></i>
                                    Lat: <?= esc($agency['latitude']) ?> / Long: <?= esc($agency['longitude']) ?>
                                    <a href="https://www.google.com/maps?q=<?= $agency['latitude'] ?>,<?= $agency['longitude'] ?>" 
                                       target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-external-link-alt me-1"></i>Voir sur Google Maps
                                    </a>
                                </p>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Agences filles -->
            <?php if ($agency['type'] === 'siege' && !empty($subAgencies)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Agences Rattachées</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Nom</th>
                                        <th>Ville</th>
                                        <th>Contact</th>
                                        <th>Statut</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subAgencies as $sub): ?>
                                        <tr>
                                            <td><strong><?= esc($sub['code']) ?></strong></td>
                                            <td><?= esc($sub['name']) ?></td>
                                            <td><?= esc($sub['city']) ?></td>
                                            <td>
                                                <?php if (!empty($sub['phone'])): ?>
                                                    <small><i class="fas fa-phone me-1"></i><?= esc($sub['phone']) ?></small>
                                                <?php endif ?>
                                            </td>
                                            <td>
                                                <?php if ($sub['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactif</span>
                                                <?php endif ?>
                                            </td>
                                            <td class="text-center">
                                                <a href="<?= base_url('admin/agencies/view/' . $sub['id']) ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <!-- Utilisateurs de l'agence -->
            <?php if (!empty($users)): ?>
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Équipe (<?= count($users) ?> membre(s))</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Téléphone</th>
                                        <th>Rôle</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>">
                                                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                                                </a>
                                            </td>
                                            <td><?= esc($user['email']) ?></td>
                                            <td><?= esc($user['phone'] ?? 'N/A') ?></td>
                                            <td><span class="badge bg-secondary"><?= esc($user['role_id']) ?></span></td>
                                            <td>
                                                <?php if ($user['status'] === 'active'): ?>
                                                    <span class="badge bg-success">Actif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Inactif</span>
                                                <?php endif ?>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Logo -->
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <?php if (!empty($agency['logo'])): ?>
                        <img src="<?= base_url('uploads/agencies/' . $agency['logo']) ?>" 
                             alt="Logo" class="img-fluid rounded mb-3" style="max-height: 200px;">
                    <?php else: ?>
                        <img src="<?= base_url('assets/images/no-image.png') ?>" 
                             alt="Pas de logo" class="img-fluid rounded mb-3" style="max-height: 200px; opacity: 0.5;">
                        <p class="text-muted">Aucun logo</p>
                    <?php endif ?>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistiques</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-users text-primary me-2"></i>Utilisateurs</span>
                            <strong class="text-primary"><?= count($users) ?></strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-building text-success me-2"></i>Propriétés</span>
                            <strong class="text-success"><?= $properties ?></strong>
                        </div>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-handshake text-warning me-2"></i>Transactions</span>
                            <strong class="text-warning"><?= $transactions ?></strong>
                        </div>
                    </div>
                    <?php if ($agency['type'] === 'siege' && !empty($subAgencies)): ?>
                        <hr>
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-sitemap text-info me-2"></i>Agences Rattachées</span>
                                <strong class="text-info"><?= count($subAgencies) ?></strong>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>

            <!-- Statut -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informations Système</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted small">Statut</label>
                        <p>
                            <?php if ($agency['status'] === 'active'): ?>
                                <span class="badge bg-success fs-6">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6">Inactif</span>
                            <?php endif ?>
                        </p>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="text-muted small">Date de Création</label>
                        <p><i class="fas fa-calendar me-2"></i><?= date('d/m/Y H:i', strtotime($agency['created_at'])) ?></p>
                    </div>
                    <div class="mb-0">
                        <label class="text-muted small">Dernière Modification</label>
                        <p><i class="fas fa-clock me-2"></i><?= date('d/m/Y H:i', strtotime($agency['updated_at'])) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
