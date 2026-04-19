<?php
$isEdit = ! empty($zone['id']);

/**
 * Mapping : pour chaque type, le type attendu du parent.
 * 'pays' → pas de parent.
 */
$parentTypeRequired = [
    'region'      => 'pays',
    'ville'       => 'region',
    'code_postal' => 'ville',
];

$typeLabels = [
    'pays'        => 'Pays',
    'region'      => 'Région / État',
    'ville'       => 'Ville',
    'code_postal' => 'Code postal',
];

$codeLabels = [
    'pays'        => 'Code ISO pays (ex : MA, FR)',
    'region'      => 'Code région (optionnel)',
    'ville'       => 'Code ville (optionnel)',
    'code_postal' => 'Code postal (ex : 10000)',
];
?>

<!-- EN-TÊTE -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('admin/zones') ?>" class="btn btn-sm btn-light">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-bold">
            <?= $isEdit ? 'Modifier la zone' : 'Nouvelle zone' ?>
        </h4>
        <?php if ($isEdit) : ?>
        <p class="text-muted mb-0"><?= esc($zone['name']) ?></p>
        <?php endif; ?>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST"
                      action="<?= $isEdit
                          ? base_url('admin/zones/' . $zone['id'] . '/update')
                          : base_url('admin/zones/store') ?>">
                    <?= csrf_field() ?>

                    <div class="row g-3">

                        <!-- Type -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Type <span class="text-danger">*</span>
                            </label>
                            <select id="zoneType" name="type" class="form-select" required>
                                <option value="">-- Sélectionner --</option>
                                <?php foreach ($typeLabels as $typeKey => $typeLabel) : ?>
                                <option value="<?= $typeKey ?>"
                                    <?= old('type', $zone['type'] ?? '') === $typeKey ? 'selected' : '' ?>>
                                    <?= $typeLabel ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Nom -->
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nom <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="zoneName" class="form-control"
                                   value="<?= esc(old('name', $zone['name'] ?? '')) ?>"
                                   placeholder="Ex : Maroc, Casablanca-Settat…"
                                   maxlength="150" required>
                        </div>

                        <!-- Code (affiché pour tous, libellé dynamique) -->
                        <div class="col-md-6" id="codeWrapper">
                            <label class="form-label fw-semibold" id="codeLabel">Code</label>
                            <input type="text" name="code" class="form-control"
                                   id="zoneCode"
                                   value="<?= esc(old('code', $zone['code'] ?? '')) ?>"
                                   placeholder="—"
                                   maxlength="20">
                            <div class="form-text" id="codeHint"></div>
                        </div>

                        <!-- Parent (masqué si type = pays) -->
                        <div class="col-md-6" id="parentWrapper">
                            <label class="form-label fw-semibold" id="parentLabel">Parent</label>
                            <select name="parent_id" id="parentSelect" class="form-select">
                                <option value="">-- Aucun / Sélectionner --</option>
                                <?php foreach ($parents as $p) : ?>
                                <?php if ($isEdit && $p['id'] === $zone['id']) continue; ?>
                                <option value="<?= $p['id'] ?>"
                                        data-type="<?= esc($p['type']) ?>"
                                    <?= old('parent_id', $zone['parent_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                                    [<?= esc($typeLabels[$p['type']] ?? $p['type']) ?>]
                                    <?= esc($p['name']) ?>
                                    <?= $p['code'] ? '(' . esc($p['code']) . ')' : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Statut actif -->
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="isActive" name="is_active" value="1"
                                    <?= old('is_active', $zone['is_active'] ?? 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="isActive">Zone active</label>
                            </div>
                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>
                            <?= $isEdit ? 'Enregistrer les modifications' : 'Créer la zone' ?>
                        </button>
                        <a href="<?= base_url('admin/zones') ?>" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const typeSelect   = document.getElementById('zoneType');
    const parentWrapper = document.getElementById('parentWrapper');
    const parentSelect  = document.getElementById('parentSelect');
    const parentLabel   = document.getElementById('parentLabel');
    const codeLabel     = document.getElementById('codeLabel');
    const codeHint      = document.getElementById('codeHint');

    const parentTypeMap = {
        region:      'pays',
        ville:       'region',
        code_postal: 'ville',
    };

    const codeLabels = {
        pays:        'Code ISO pays (ex : MA, FR)',
        region:      'Code région (optionnel)',
        ville:       'Code ville (optionnel)',
        code_postal: 'Code postal (ex : 10000)',
    };

    const parentTypeLabels = {
        pays:        'Pays',
        region:      'Région',
        ville:       'Ville',
        code_postal: 'Code postal',
    };

    function updateForm() {
        const type = typeSelect.value;

        // Mise à jour du label et hint du code
        codeLabel.textContent = type === 'code_postal' ? 'Code postal *' : 'Code';
        codeHint.textContent  = codeLabels[type] ?? '';

        // Parent : masqué pour Pays, obligatoire sinon
        if (type === 'pays' || type === '') {
            parentWrapper.style.display = 'none';
            parentSelect.removeAttribute('required');
        } else {
            parentWrapper.style.display = '';
            parentSelect.setAttribute('required', 'required');
            const expectedParentType = parentTypeMap[type];
            parentLabel.textContent = 'Parent – ' + (parentTypeLabels[expectedParentType] ?? '') + ' *';

            // Filtre les options du select parent
            Array.from(parentSelect.options).forEach(opt => {
                if (opt.value === '') return; // garder l'option vide
                opt.hidden = opt.dataset.type !== expectedParentType;
            });

            // Réinitialise si l'option sélectionnée n'est plus visible
            const selectedOpt = parentSelect.options[parentSelect.selectedIndex];
            if (selectedOpt && selectedOpt.hidden) {
                parentSelect.value = '';
            }
        }
    }

    typeSelect.addEventListener('change', updateForm);
    // Appel initial pour appliquer l'état au chargement (mode édition)
    updateForm();
})();
</script>
