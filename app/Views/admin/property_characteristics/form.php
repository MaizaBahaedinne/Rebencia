<?php
// Valeurs courantes (édition) ou défauts (création)
$isEdit    = ! empty($row['id']);
$key       = $isEdit ? $row['key']        : old('key',       '');
$label     = $isEdit ? $row['label']      : old('label',     '');
$icon      = $isEdit ? $row['icon']       : old('icon',      'bi-check-circle');
$type      = $isEdit ? $row['type']       : old('type',      'boolean');
$unit      = $isEdit ? ($row['unit'] ?? ''): old('unit',      '');
$sortOrder = $isEdit ? $row['sort_order'] : old('sort_order', 100);
$isActive  = $isEdit ? $row['is_active']  : old('is_active', 1);

$appliesTo   = $isEdit && $row['applies_to']   ? json_decode($row['applies_to'],   true) : (old('applies_to')  ?? []);
$requiredFor = $isEdit && $row['required_for'] ? json_decode($row['required_for'], true) : (old('required_for') ?? []);
$options     = $isEdit && $row['options']      ? json_decode($row['options'],      true) : [];
$optionsText = old('options_text', $options ? implode("\n", $options) : '');

$propertyTypes = [
    'apartment' => 'Appartement',
    'house'     => 'Maison',
    'villa'     => 'Villa',
    'commercial'=> 'Commercial',
    'land'      => 'Terrain',
    'office'    => 'Bureau',
];
$formAction = $isEdit
    ? base_url('admin/property-characteristics/' . $row['id'] . '/update')
    : base_url('admin/property-characteristics/store');
?>

<div class="d-flex align-items-center mb-4 gap-2">
    <a href="<?= base_url('admin/property-characteristics') ?>" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i>
    </a>
    <h4 class="mb-0 fw-bold">
        <i class="bi bi-tag me-2 text-primary"></i><?= esc($page_title) ?>
    </h4>
</div>

<?php if (! empty($errors)) : ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach ($errors as $err) : ?>
        <li><?= esc($err) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="post" action="<?= $formAction ?>" id="charForm">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- ─── Colonne principale ─────────────────────────────────── -->
        <div class="col-lg-8">

            <!-- Identification -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-fingerprint me-2"></i>Identification
                </div>
                <div class="card-body row g-3">

                    <!-- Clé technique -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Clé technique <span class="text-danger">*</span>
                            <i class="bi bi-info-circle text-muted ms-1"
                               data-bs-toggle="tooltip"
                               title="Identifiant unique snake_case, jamais modifié après création. Ex: has_pool"></i>
                        </label>
                        <input type="text" name="key" class="form-control font-monospace"
                               value="<?= esc($key) ?>"
                               placeholder="has_elevator"
                               pattern="[a-z0-9_]+"
                               <?= $isEdit ? 'readonly' : '' ?>
                               required>
                        <?php if ($isEdit) : ?>
                        <div class="form-text text-muted">La clé ne peut pas être modifiée après création.</div>
                        <?php endif; ?>
                    </div>

                    <!-- Label affiché -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            Label affiché <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="label" class="form-control"
                               value="<?= esc($label) ?>"
                               placeholder="Ascenseur"
                               required>
                    </div>

                    <!-- Type de saisie -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Type de saisie <span class="text-danger">*</span></label>
                        <select name="type" id="typeSelect" class="form-select" required>
                            <option value="boolean" <?= $type === 'boolean' ? 'selected' : '' ?>>Oui / Non (case à cocher)</option>
                            <option value="number"  <?= $type === 'number'  ? 'selected' : '' ?>>Nombre</option>
                            <option value="text"    <?= $type === 'text'    ? 'selected' : '' ?>>Texte libre</option>
                            <option value="select"  <?= $type === 'select'  ? 'selected' : '' ?>>Liste de choix</option>
                        </select>
                    </div>

                    <!-- Unité (conditionnel number) -->
                    <div class="col-md-6" id="unitField">
                        <label class="form-label fw-semibold">Unité <small class="text-muted">(optionnel)</small></label>
                        <input type="text" name="unit" class="form-control"
                               value="<?= esc($unit) ?>"
                               placeholder="m², km², années…">
                    </div>

                    <!-- Options (conditionnel select) -->
                    <div class="col-12" id="optionsField" style="display:none">
                        <label class="form-label fw-semibold">
                            Options <span class="text-danger">*</span>
                            <small class="text-muted">— une valeur par ligne</small>
                        </label>
                        <textarea name="options_text" class="form-control font-monospace"
                                  rows="5"
                                  placeholder="Individuel gaz&#10;Collectif&#10;Électrique&#10;Autre"><?= esc($optionsText) ?></textarea>
                    </div>

                </div>
            </div>

            <!-- Icône Bootstrap Icons -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-palette me-2"></i>Icône Bootstrap Icons
                </div>
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text" id="iconPreview">
                            <i class="bi <?= esc($icon) ?> fs-5"></i>
                        </span>
                        <input type="text" name="icon" id="iconInput" class="form-control font-monospace"
                               value="<?= esc($icon) ?>"
                               placeholder="bi-check-circle"
                               required>
                        <a href="https://icons.getbootstrap.com/" target="_blank" class="btn btn-outline-secondary" title="Parcourir les icônes">
                            <i class="bi bi-box-arrow-up-right"></i>
                        </a>
                    </div>
                    <div class="form-text">
                        Saisissez une classe Bootstrap Icons (ex: <code>bi-water</code>, <code>bi-elevator</code>).
                        <a href="https://icons.getbootstrap.com/" target="_blank">Parcourir la liste complète</a>.
                    </div>

                    <!-- Suggestions rapides -->
                    <div class="mt-2 d-flex flex-wrap gap-1" id="iconSuggestions">
                        <?php
                        $suggestions = [
                            'bi-check-circle','bi-x-circle','bi-water','bi-tree','bi-car-front',
                            'bi-p-square','bi-thermometer-snow','bi-thermometer-sun','bi-wifi',
                            'bi-sun','bi-columns-gap','bi-box','bi-shield-check','bi-person-badge',
                            'bi-rulers','bi-calendar3','bi-compass','bi-stars','bi-fire',
                            'bi-arrow-up-square','bi-binoculars','bi-house','bi-building',
                            'bi-p-circle','bi-sun-fill',
                        ];
                        foreach ($suggestions as $s):
                        ?>
                        <button type="button" class="btn btn-sm btn-outline-secondary icon-suggestion"
                                data-icon="<?= esc($s) ?>" title="<?= esc($s) ?>">
                            <i class="bi <?= esc($s) ?>"></i>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- ─── Colonne latérale ──────────────────────────────────── -->
        <div class="col-lg-4">

            <!-- Statut et ordre -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-sliders me-2"></i>Paramètres
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ordre d'affichage</label>
                        <input type="number" name="sort_order" class="form-control"
                               value="<?= (int) $sortOrder ?>" min="0" max="9999" step="10" required>
                        <div class="form-text">Les valeurs les plus faibles apparaissent en premier.</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="isActive"
                               name="is_active" value="1"
                               <?= $isActive ? 'checked' : '' ?>>
                        <label class="form-check-label fw-semibold" for="isActive">Caractéristique active</label>
                    </div>
                </div>
            </div>

            <!-- Types de bien → applies_to -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-funnel me-2"></i>Types de biens concernés
                </div>
                <div class="card-body">
                    <div class="form-text mb-2">
                        Si aucun type n'est coché, la caractéristique s'applique à <strong>tous les biens</strong>.
                    </div>
                    <?php foreach ($propertyTypes as $val => $lbl) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="applies_to[]" value="<?= esc($val) ?>"
                               id="at_<?= esc($val) ?>"
                               <?= in_array($val, (array) $appliesTo, true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="at_<?= esc($val) ?>">
                            <?= esc($lbl) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Obligatoire pour → required_for -->
            <div class="card shadow-sm mb-4">
                <div class="card-header fw-semibold">
                    <i class="bi bi-exclamation-circle me-2"></i>Obligatoire pour
                </div>
                <div class="card-body">
                    <div class="form-text mb-2">
                        Cochez les types pour lesquels cette caractéristique est <strong>requise</strong>.
                    </div>
                    <?php foreach ($propertyTypes as $val => $lbl) : ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                               name="required_for[]" value="<?= esc($val) ?>"
                               id="rf_<?= esc($val) ?>"
                               <?= in_array($val, (array) $requiredFor, true) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="rf_<?= esc($val) ?>">
                            <?= esc($lbl) ?>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-2 justify-content-end pb-4">
        <a href="<?= base_url('admin/property-characteristics') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i>Annuler
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-floppy me-1"></i>
            <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la caractéristique' ?>
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Activer les tooltips Bootstrap
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el);
    });

    const typeSelect   = document.getElementById('typeSelect');
    const unitField    = document.getElementById('unitField');
    const optionsField = document.getElementById('optionsField');

    function updateConditionalFields() {
        const t = typeSelect.value;
        unitField.style.display    = (t === 'number') ? '' : 'none';
        optionsField.style.display = (t === 'select') ? '' : 'none';
    }
    typeSelect.addEventListener('change', updateConditionalFields);
    updateConditionalFields(); // état initial

    // Aperçu icône en temps réel
    const iconInput   = document.getElementById('iconInput');
    const iconPreview = document.getElementById('iconPreview').querySelector('i');

    iconInput.addEventListener('input', function () {
        iconPreview.className = 'bi ' + this.value.trim() + ' fs-5';
    });

    // Suggéssions icônes rapides
    document.querySelectorAll('.icon-suggestion').forEach(btn => {
        btn.addEventListener('click', function () {
            const ic = this.dataset.icon;
            iconInput.value = ic;
            iconPreview.className = 'bi ' + ic + ' fs-5';
        });
    });

    // Slug auto depuis le label (création uniquement)
    <?php if (! $isEdit) : ?>
    const labelInput = document.querySelector('input[name="label"]');
    const keyInput   = document.querySelector('input[name="key"]');
    labelInput.addEventListener('input', function () {
        if (keyInput.dataset.touched) return;
        keyInput.value = this.value
            .toLowerCase()
            .replace(/[àáâãä]/g, 'a').replace(/[éèêë]/g, 'e')
            .replace(/[îï]/g, 'i').replace(/[ôö]/g, 'o').replace(/[ùûü]/g, 'u')
            .replace(/[^a-z0-9]+/g, '_')
            .replace(/^_+|_+$/g, '');
    });
    keyInput.addEventListener('input', function () {
        this.dataset.touched = '1';
    });
    <?php endif; ?>
});
</script>
