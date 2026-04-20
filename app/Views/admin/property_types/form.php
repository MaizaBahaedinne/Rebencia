<?php
$isEdit  = ! empty($row['id']);
$name    = $isEdit ? $row['name']        : old('name',    '');
$slug    = $isEdit ? $row['slug']        : old('slug',    '');
$icon    = $isEdit ? ($row['icon'] ?? '') : old('icon',   '');
$desc    = $isEdit ? ($row['description'] ?? '') : old('description', '');
$active  = $isEdit ? $row['is_active']   : old('is_active', 1);

$formAction = $isEdit
    ? base_url('admin/property-types/' . $row['id'] . '/update')
    : base_url('admin/property-types/store');
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?= base_url('admin/property-types') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">
        <i class="bi bi-house-gear me-2 text-primary"></i><?= esc($page_title) ?>
    </h4>
</div>

<?php if (! empty($errors)): ?>
<div class="alert alert-danger alert-dismissible fade show">
    <ul class="mb-0">
        <?php foreach ($errors as $err): ?>
        <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<form method="post" action="<?= $formAction ?>" id="typeForm">
    <?= csrf_field() ?>

    <div class="row g-4">

        <!-- ─── Colonne principale ─────────────────────────────────── -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-pencil-square me-2"></i>Informations
                </div>
                <div class="card-body row g-3">

                    <!-- Nom -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Nom <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" id="fieldName"
                               class="form-control <?= !empty($errors['name']) ? 'is-invalid' : '' ?>"
                               value="<?= esc($name) ?>"
                               placeholder="Ex : Appartement"
                               required>
                        <?php if (!empty($errors['name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Slug -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            Slug
                            <span class="text-muted small fw-normal ms-1">(auto-généré si vide)</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text text-muted small font-monospace">/</span>
                            <input type="text" name="slug" id="fieldSlug"
                                   class="form-control font-monospace <?= !empty($errors['slug']) ? 'is-invalid' : '' ?>"
                                   value="<?= esc($slug) ?>"
                                   placeholder="appartement"
                                   pattern="[a-z0-9\-]+"
                                   title="Minuscules, chiffres et tirets uniquement">
                            <?php if (!empty($errors['slug'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['slug']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="form-text text-muted">Utilisé comme identifiant technique. Minuscules et tirets uniquement.</div>
                    </div>

                    <!-- Description -->
                    <div class="col-12">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Description optionnelle du type de bien…"><?= esc($desc) ?></textarea>
                    </div>

                </div>
            </div>
        </div>

        <!-- ─── Colonne latérale ───────────────────────────────────── -->
        <div class="col-lg-4">

            <!-- Icône -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-emoji-smile me-2"></i>Icône
                </div>
                <div class="card-body">
                    <label class="form-label fw-semibold">
                        Classe Bootstrap Icons
                        <span class="text-muted small fw-normal ms-1">(optionnel)</span>
                    </label>
                    <div class="input-group mb-2">
                        <span class="input-group-text" id="iconPreview">
                            <i class="bi <?= $icon ?: 'bi-house-gear' ?> fs-5" id="iconEl"></i>
                        </span>
                        <input type="text" name="icon" id="fieldIcon"
                               class="form-control font-monospace"
                               value="<?= esc($icon) ?>"
                               placeholder="bi-building">
                    </div>
                    <div class="form-text text-muted">
                        Ex: <code>bi-building</code>, <code>bi-house</code>, <code>bi-shop</code>
                        — voir <a href="https://icons.getbootstrap.com" target="_blank" rel="noopener">icons.getbootstrap.com</a>
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold bg-white">
                    <i class="bi bi-toggle-on me-2"></i>Statut
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox"
                               role="switch" name="is_active" id="isActive"
                               value="1" <?= $active ? 'checked' : '' ?>>
                        <label class="form-check-label" for="isActive">
                            Actif
                        </label>
                    </div>
                    <div class="form-text text-muted">Un type inactif n'apparaît pas dans les formulaires de biens.</div>
                </div>
            </div>

            <!-- Boutons -->
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy me-1"></i>
                    <?= $isEdit ? 'Enregistrer les modifications' : 'Créer le type' ?>
                </button>
                <a href="<?= base_url('admin/property-types') ?>" class="btn btn-outline-secondary">
                    Annuler
                </a>
            </div>

        </div>
    </div>
</form>

<script>
(function () {
    'use strict';

    // Prévisualisation de l'icône en temps réel
    const fieldIcon = document.getElementById('fieldIcon');
    const iconEl    = document.getElementById('iconEl');

    if (fieldIcon && iconEl) {
        fieldIcon.addEventListener('input', function () {
            const val = this.value.trim();
            iconEl.className = 'bi ' + (val || 'bi-house-gear') + ' fs-5';
        });
    }

    // Auto-génération du slug depuis le nom (si slug vide et pas en édition)
    const fieldName = document.getElementById('fieldName');
    const fieldSlug = document.getElementById('fieldSlug');
    <?php if (! $isEdit): ?>
    let slugManual = <?= $slug ? 'true' : 'false' ?>;

    if (fieldSlug) {
        fieldSlug.addEventListener('input', function () {
            slugManual = this.value.trim() !== '';
        });
    }

    if (fieldName && fieldSlug) {
        fieldName.addEventListener('input', function () {
            if (slugManual) return;
            const accents = {
                'à':'a','â':'a','ä':'a','á':'a','ã':'a',
                'è':'e','é':'e','ê':'e','ë':'e',
                'ì':'i','í':'i','î':'i','ï':'i',
                'ò':'o','ó':'o','ô':'o','ö':'o','õ':'o',
                'ù':'u','ú':'u','û':'u','ü':'u',
                'ý':'y','ÿ':'y','ñ':'n','ç':'c',
            };
            let slug = this.value.toLowerCase();
            slug = slug.replace(/[àâäáã]/g, 'a')
                       .replace(/[èéêë]/g, 'e')
                       .replace(/[ìíîï]/g, 'i')
                       .replace(/[òóôöõ]/g, 'o')
                       .replace(/[ùúûü]/g, 'u')
                       .replace(/[ç]/g, 'c')
                       .replace(/[ñ]/g, 'n')
                       .replace(/[^a-z0-9]+/g, '-')
                       .replace(/^-|-$/g, '');
            fieldSlug.value = slug;
        });
    }
    <?php endif; ?>
})();
</script>
