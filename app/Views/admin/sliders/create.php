<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle"></i> <?= esc($page_title) ?>
        </h1>
        <a href="<?= base_url('admin/sliders') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>

    <?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h6><i class="fas fa-exclamation-triangle"></i> Erreurs de validation :</h6>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/sliders/store') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8">
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Informations du Slider</h6>
                    </div>
                    <div class="card-body">
                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">Titre <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= old('title') ?>" required>
                        </div>

                        <!-- Sous-titre -->
                        <div class="mb-3">
                            <label for="subtitle" class="form-label">Sous-titre</label>
                            <input type="text" class="form-control" id="subtitle" name="subtitle" 
                                   value="<?= old('subtitle') ?>">
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3"><?= old('description') ?></textarea>
                        </div>

                        <!-- Image -->
                        <div class="mb-3">
                            <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" 
                                   accept="image/*" required onchange="previewImage(this)">
                            <small class="text-muted">Format: JPG, PNG, WebP. Taille max: 2 Mo. Dimensions recommandées: 1920x800px</small>
                            <div id="imagePreview" class="mt-2"></div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3">Bouton Principal</h6>
                                <div class="mb-3">
                                    <label for="button1_text" class="form-label">Texte du bouton</label>
                                    <input type="text" class="form-control" id="button1_text" 
                                           name="button1_text" value="<?= old('button1_text') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="button1_link" class="form-label">Lien du bouton</label>
                                    <input type="url" class="form-control" id="button1_link" 
                                           name="button1_link" value="<?= old('button1_link') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-secondary mb-3">Bouton Secondaire</h6>
                                <div class="mb-3">
                                    <label for="button2_text" class="form-label">Texte du bouton</label>
                                    <input type="text" class="form-control" id="button2_text" 
                                           name="button2_text" value="<?= old('button2_text') ?>">
                                </div>
                                <div class="mb-3">
                                    <label for="button2_link" class="form-label">Lien du bouton</label>
                                    <input type="url" class="form-control" id="button2_link" 
                                           name="button2_link" value="<?= old('button2_link') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paramètres -->
            <div class="col-lg-4">
                <!-- Configuration -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Configuration</h6>
                    </div>
                    <div class="card-body">
                        <!-- Type d'animation -->
                        <div class="mb-3">
                            <label for="animation_type" class="form-label">Type d'animation</label>
                            <select class="form-select" id="animation_type" name="animation_type">
                                <option value="fade" <?= old('animation_type') === 'fade' ? 'selected' : '' ?>>Fondu</option>
                                <option value="slide" <?= old('animation_type') === 'slide' ? 'selected' : '' ?>>Glissement</option>
                                <option value="zoom" <?= old('animation_type') === 'zoom' ? 'selected' : '' ?>>Zoom</option>
                            </select>
                        </div>

                        <!-- Position du texte -->
                        <div class="mb-3">
                            <label for="text_position" class="form-label">Position du texte</label>
                            <select class="form-select" id="text_position" name="text_position">
                                <option value="left" <?= old('text_position') === 'left' ? 'selected' : '' ?>>Gauche</option>
                                <option value="center" <?= old('text_position') === 'center' ? 'selected' : '' ?>>Centre</option>
                                <option value="right" <?= old('text_position') === 'right' ? 'selected' : '' ?>>Droite</option>
                            </select>
                        </div>

                        <!-- Opacité de l'overlay -->
                        <div class="mb-3">
                            <label for="overlay_opacity" class="form-label">
                                Opacité de l'overlay: <span id="opacityValue">50</span>%
                            </label>
                            <input type="range" class="form-range" id="overlay_opacity" 
                                   name="overlay_opacity" min="0" max="100" 
                                   value="<?= old('overlay_opacity', 50) ?>"
                                   oninput="document.getElementById('opacityValue').textContent = this.value">
                        </div>

                        <!-- Ordre d'affichage -->
                        <div class="mb-3">
                            <label for="display_order" class="form-label">Ordre d'affichage</label>
                            <input type="number" class="form-control" id="display_order" 
                                   name="display_order" value="<?= old('display_order', 0) ?>" min="0">
                        </div>

                        <!-- Actif -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" value="1" <?= old('is_active', '1') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Actif</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body text-end">
                        <a href="<?= base_url('admin/sliders') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded" style="max-height: 300px;">`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?= $this->endSection() ?>
