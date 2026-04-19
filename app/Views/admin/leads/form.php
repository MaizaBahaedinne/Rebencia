
<?php $isEdit = isset($lead); ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-0"><?= $isEdit ? 'Modifier le lead' : 'Nouveau lead' ?></h2>
        <small class="text-muted"><?= $isEdit ? esc($lead['first_name'] . ' ' . $lead['last_name']) : 'Créer un prospect' ?></small>
    </div>
    <a href="<?= site_url('admin/leads') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Retour
    </a>
</div>

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<form method="post" action="<?= $isEdit ? site_url('admin/leads/' . $lead['id'] . '/update') : site_url('admin/leads/store') ?>">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Contact -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-person me-2"></i>Informations du contact</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('first_name', $lead['first_name'] ?? '')) ?>" required>
                            <?php if (isset($errors['first_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['first_name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc(old('last_name', $lead['last_name'] ?? '')) ?>" required>
                            <?php if (isset($errors['last_name'])): ?>
                                <div class="invalid-feedback"><?= $errors['last_name'] ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= esc(old('email', $lead['email'] ?? '')) ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?= esc(old('phone', $lead['phone'] ?? '')) ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projet -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong><i class="bi bi-house me-2"></i>Projet immobilier</strong></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Type de transaction</label>
                            <select name="transaction_type" class="form-select">
                                <option value="">— Sélectionner —</option>
                                <option value="buy"  <?= old('transaction_type', $lead['transaction_type'] ?? '') === 'buy'  ? 'selected' : '' ?>>Achat</option>
                                <option value="rent" <?= old('transaction_type', $lead['transaction_type'] ?? '') === 'rent' ? 'selected' : '' ?>>Location</option>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Type de bien</label>
                            <select name="property_type" class="form-select">
                                <option value="">— Sélectionner —</option>
                                <?php foreach (['apartment'=>'Appartement','house'=>'Maison','villa'=>'Villa','land'=>'Terrain','commercial'=>'Local commercial','office'=>'Bureau'] as $val=>$lbl): ?>
                                    <option value="<?= $val ?>" <?= old('property_type', $lead['property_type'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Budget min (TND)</label>
                            <input type="number" name="budget_min" class="form-control" min="0" step="1000"
                                   value="<?= old('budget_min', $lead['budget_min'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Budget max (TND)</label>
                            <input type="number" name="budget_max" class="form-control" min="0" step="1000"
                                   value="<?= old('budget_max', $lead['budget_max'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Surface souhaitée (m²)</label>
                            <input type="number" name="desired_surface" class="form-control" min="0"
                                   value="<?= old('desired_surface', $lead['desired_surface'] ?? '') ?>">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Localisation souhaitée</label>
                            <input type="text" name="desired_location" class="form-control"
                                   value="<?= esc(old('desired_location', $lead['desired_location'] ?? '')) ?>">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Propriété liée</label>
                            <select name="property_id" class="form-select">
                                <option value="">— Aucune —</option>
                                <?php foreach ($properties as $prop): ?>
                                    <option value="<?= $prop['id'] ?>" <?= old('property_id', $lead['property_id'] ?? '') == $prop['id'] ? 'selected' : '' ?>>
                                        [<?= esc($prop['reference']) ?>] <?= esc($prop['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent"><strong><i class="bi bi-chat-text me-2"></i>Notes</strong></div>
                <div class="card-body">
                    <textarea name="notes" class="form-control" rows="4"><?= esc(old('notes', $lead['notes'] ?? '')) ?></textarea>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statut pipeline -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong>Pipeline</strong></div>
                <div class="card-body">
                    <label class="form-label">Statut <span class="text-danger">*</span></label>
                    <select name="status" class="form-select mb-3" required>
                        <?php
                        $statusOptions = [
                            'new'         => 'Nouveau',
                            'contacted'   => 'Contacté',
                            'interested'  => 'Intéressé',
                            'visit_done'  => 'Visite faite',
                            'negotiating' => 'En négociation',
                            'won'         => 'Conclu',
                            'lost'        => 'Perdu',
                        ];
                        foreach ($statusOptions as $val => $lbl):
                        ?>
                            <option value="<?= $val ?>" <?= old('status', $lead['status'] ?? 'new') === $val ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <label class="form-label">Source</label>
                    <select name="source" class="form-select">
                        <option value="">— Source —</option>
                        <?php foreach (['website'=>'Site web','referral'=>'Recommandation','phone'=>'Téléphone','email'=>'Email','walk_in'=>'Passage en agence','social'=>'Réseaux sociaux','other'=>'Autre'] as $val=>$lbl): ?>
                            <option value="<?= $val ?>" <?= old('source', $lead['source'] ?? '') === $val ? 'selected' : '' ?>>
                                <?= $lbl ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Assignation -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong>Assignation</strong></div>
                <div class="card-body">
                    <label class="form-label">Assigné à</label>
                    <select name="assigned_to" class="form-select">
                        <option value="">— Non assigné —</option>
                        <?php foreach ($agents as $agent): ?>
                            <option value="<?= $agent['id'] ?>" <?= old('assigned_to', $lead['assigned_to'] ?? '') == $agent['id'] ? 'selected' : '' ?>>
                                <?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Priorité -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-transparent"><strong>Priorité</strong></div>
                <div class="card-body">
                    <div class="btn-group w-100" role="group">
                        <?php foreach (['low'=>'Faible','medium'=>'Normale','high'=>'Haute'] as $val=>$lbl): ?>
                        <input type="radio" class="btn-check" name="priority" id="priority_<?= $val ?>" value="<?= $val ?>"
                               <?= old('priority', $lead['priority'] ?? 'medium') === $val ? 'checked' : '' ?>>
                        <label class="btn btn-outline-secondary" for="priority_<?= $val ?>"><?= $lbl ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le lead' ?>
                </button>
            </div>
        </div>
    </div>
</form>

