<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Détails de la Demande #<?= $estimation['id'] ?></h2>
        <p class="text-muted">Demande reçue le <?= date('d/m/Y à H:i', strtotime($estimation['created_at'])) ?></p>
    </div>
    <a href="<?= base_url('admin/property-estimations') ?>" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <!-- Left Column -->
    <div class="col-md-8">
        <!-- Property Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-house-door"></i> Informations du Bien</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Type de bien</label>
                        <div>
                            <strong>
                                <?php
                                $types = [
                                    'apartment' => 'Appartement',
                                    'villa' => 'Villa',
                                    'studio' => 'Studio',
                                    'office' => 'Bureau',
                                    'shop' => 'Commerce',
                                    'warehouse' => 'Entrepôt',
                                    'land' => 'Terrain',
                                    'other' => 'Autre'
                                ];
                                echo $types[$estimation['property_type']] ?? $estimation['property_type'];
                                ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">Type de transaction</label>
                        <div>
                            <strong><?= $estimation['transaction_type'] === 'sale' ? 'Vente' : 'Location' ?></strong>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="text-muted small">Adresse</label>
                        <div><?= esc($estimation['address'] ?: 'Non spécifiée') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Ville</label>
                        <div><?= esc($estimation['city'] ?: '-') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Gouvernorat</label>
                        <div><?= esc($estimation['governorate'] ?: '-') ?></div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="text-muted small">Zone</label>
                        <div><?= esc($zone['name'] ?? '-') ?></div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Surface totale</label>
                        <div><strong><?= $estimation['area_total'] ? number_format($estimation['area_total'], 0) . ' m²' : '-' ?></strong></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Pièces</label>
                        <div><strong><?= $estimation['rooms'] ?: '-' ?></strong></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Chambres</label>
                        <div><strong><?= $estimation['bedrooms'] ?: '-' ?></strong></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Salles de bain</label>
                        <div><strong><?= $estimation['bathrooms'] ?: '-' ?></strong></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Étage</label>
                        <div><?= $estimation['floor'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="text-muted small">Année de construction</label>
                        <div><?= $estimation['construction_year'] ?: '-' ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted small">État</label>
                        <div>
                            <?php
                            if ($estimation['condition_state']) {
                                $conditions = [
                                    'new' => 'Neuf',
                                    'excellent' => 'Excellent',
                                    'good' => 'Bon',
                                    'to_renovate' => 'À rénover',
                                    'to_demolish' => 'À démolir'
                                ];
                                echo $conditions[$estimation['condition_state']] ?? $estimation['condition_state'];
                            } else {
                                echo '-';
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-4">
                        <label class="text-muted small">Ascenseur</label>
                        <div><?= $estimation['has_elevator'] ? '✅ Oui' : '❌ Non' ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Parking</label>
                        <div><?= $estimation['has_parking'] ? '✅ Oui' : '❌ Non' ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-muted small">Jardin</label>
                        <div><?= $estimation['has_garden'] ? '✅ Oui' : '❌ Non' ?></div>
                    </div>
                </div>

                <?php if ($estimation['description']): ?>
                <hr>
                <div>
                    <label class="text-muted small">Description</label>
                    <p><?= nl2br(esc($estimation['description'])) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-md-4">
        <!-- Client Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-person"></i> Informations Client</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small">Nom complet</label>
                    <div><strong><?= esc($estimation['first_name'] . ' ' . $estimation['last_name']) ?></strong></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Email</label>
                    <div><a href="mailto:<?= esc($estimation['email']) ?>"><?= esc($estimation['email']) ?></a></div>
                </div>
                <div class="mb-3">
                    <label class="text-muted small">Téléphone</label>
                    <div>
                        <?php if ($estimation['phone']): ?>
                            <a href="tel:<?= esc($estimation['phone']) ?>"><?= esc($estimation['phone']) ?></a>
                        <?php else: ?>
                            <span class="text-muted">Non fourni</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($client): ?>
                <div class="mt-3">
                    <a href="<?= base_url('admin/clients/view/' . $client['id']) ?>" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-person-circle"></i> Voir la fiche client
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Status & Assignment -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Gestion</h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= base_url('admin/property-estimations/updateStatus/' . $estimation['id']) ?>">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-select" required>
                            <option value="pending" <?= $estimation['status'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="in_progress" <?= $estimation['status'] === 'in_progress' ? 'selected' : '' ?>>En cours</option>
                            <option value="estimated" <?= $estimation['status'] === 'estimated' ? 'selected' : '' ?>>Estimé</option>
                            <option value="contacted" <?= $estimation['status'] === 'contacted' ? 'selected' : '' ?>>Contacté</option>
                            <option value="converted" <?= $estimation['status'] === 'converted' ? 'selected' : '' ?>>Converti</option>
                            <option value="cancelled" <?= $estimation['status'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Agent assigné</label>
                        <select name="agent_id" class="form-select">
                            <option value="">Non assigné</option>
                            <?php foreach ($agents as $ag): ?>
                                <option value="<?= $ag['id'] ?>" <?= $estimation['agent_id'] == $ag['id'] ? 'selected' : '' ?>>
                                    <?= esc($ag['first_name'] . ' ' . $ag['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Prix estimé (TND)</label>
                        <input type="number" name="estimated_price" class="form-control" 
                               value="<?= $estimation['estimated_price'] ?>" step="0.01">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notes internes</label>
                        <textarea name="notes" class="form-control" rows="4"><?= esc($estimation['notes']) ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Mettre à jour
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
