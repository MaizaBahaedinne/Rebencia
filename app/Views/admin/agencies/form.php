
<?php $isEdit = ! empty($agency['id']); ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= $isEdit ? base_url('admin/agencies/' . $agency['id']) : base_url('admin/agencies') ?>"
       class="btn btn-sm btn-light"><i class="bi bi-arrow-left"></i></a>
    <h4 class="fw-bold mb-0">
        <i class="bi bi-buildings me-2 text-primary"></i>
        <?= $isEdit ? 'Modifier : ' . esc($agency['name']) : 'Nouvelle agence' ?>
    </h4>
</div>

<?php if (session()->has('errors') || $isEdit && isset($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ((session('errors') ?? []) as $e): ?>
        <li><?= esc($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="POST"
      action="<?= $isEdit ? base_url('admin/agencies/' . $agency['id'] . '/update') : base_url('admin/agencies/store') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- Colonne principale -->
        <div class="col-lg-8">

            <!-- Informations générales -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white"><i class="bi bi-info-circle me-1 text-primary"></i> Informations générales</div>
                <div class="card-body row g-3">

                    <div class="col-12">
                        <label class="form-label fw-semibold">Nom de l'agence <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required maxlength="150"
                               value="<?= esc(old('name', $agency['name'] ?? '')) ?>"
                               placeholder="Ex : Agence Tunis Centre">
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control" maxlength="191"
                               value="<?= esc(old('email', $agency['email'] ?? '')) ?>"
                               placeholder="contact@agence.com">
                    </div>

                    <div class="col-sm-6">
                        <label class="form-label fw-semibold">Téléphone</label>
                        <input type="text" name="phone" class="form-control" maxlength="30"
                               value="<?= esc(old('phone', $agency['phone'] ?? '')) ?>"
                               placeholder="+216 71 000 000">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Description courte de l'agence…"><?= esc(old('description', $agency['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Adresse & Zone -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white"><i class="bi bi-geo-alt me-1 text-info"></i> Localisation & Zone de responsabilité</div>
                <div class="card-body row g-3">

                    <div class="col-sm-8">
                        <label class="form-label fw-semibold">Adresse</label>
                        <input type="text" name="address" class="form-control" maxlength="255"
                               value="<?= esc(old('address', $agency['address'] ?? '')) ?>"
                               placeholder="Rue, immeuble…">
                    </div>

                    <div class="col-sm-4">
                        <label class="form-label fw-semibold">Ville</label>
                        <input type="text" name="city" class="form-control" maxlength="100"
                               value="<?= esc(old('city', $agency['city'] ?? '')) ?>"
                               placeholder="Tunis">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-semibold">Zone de responsabilité</label>
                        <select name="zone_id" class="form-select">
                            <option value="">— Aucune zone assignée —</option>
                            <?php foreach ($zones as $z): ?>
                            <option value="<?= $z['id'] ?>"
                                <?= (int) old('zone_id', $agency['zone_id'] ?? 0) === (int) $z['id'] ? 'selected' : '' ?>>
                                <?= esc($z['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">La zone géographique dont cette agence est responsable (pays ou gouvernorat).</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne secondaire -->
        <div class="col-lg-4">

            <!-- Logo -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white"><i class="bi bi-image me-1 text-secondary"></i> Logo</div>
                <div class="card-body text-center">
                    <?php if (! empty($agency['logo'])): ?>
                    <img src="<?= base_url($agency['logo']) ?>" alt="" class="rounded-2 mb-3"
                         style="max-height:100px;max-width:100%;object-fit:contain;">
                    <?php endif; ?>
                    <input type="file" name="logo" class="form-control form-control-sm" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                    <div class="form-text">JPG, PNG, WebP ou SVG. Max 2 Mo.</div>
                </div>
            </div>

            <!-- Statut -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white"><i class="bi bi-toggle-on me-1 text-success"></i> Statut</div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="switchActive"
                               <?= old('is_active', ($agency['is_active'] ?? 1)) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="switchActive">Agence active</label>
                    </div>
                    <div class="form-text">Une agence inactive n'est plus visible dans les sélecteurs.</div>
                </div>
            </div>

            <!-- Actions -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i><?= $isEdit ? 'Enregistrer les modifications' : 'Créer l\'agence' ?>
                </button>
                <a href="<?= $isEdit ? base_url('admin/agencies/' . $agency['id']) : base_url('admin/agencies') ?>"
                   class="btn btn-outline-secondary">Annuler</a>
            </div>
        </div>

    </div><!-- /.row -->
</form>


