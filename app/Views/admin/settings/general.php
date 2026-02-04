<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sliders-h"></i> <?= esc($page_title) ?>
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-building"></i> Informations de l'Entreprise
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/settings/update') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_category" value="general">

                        <div class="form-group">
                            <label for="company_name">Nom de l'Entreprise <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="company_name" name="company_name" 
                                   value="<?= esc($settings['company_name'] ?? 'Rebencia') ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="company_slogan">Slogan</label>
                            <input type="text" class="form-control" id="company_slogan" name="company_slogan" 
                                   value="<?= esc($settings['company_slogan'] ?? 'Votre partenaire immobilier') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_email">Email</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email" 
                                           value="<?= esc($settings['company_email'] ?? 'contact@rebencia.com') ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_phone">Téléphone</label>
                                    <input type="tel" class="form-control" id="company_phone" name="company_phone" 
                                           value="<?= esc($settings['company_phone'] ?? '+216 XX XXX XXX') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="company_address">Adresse Complète</label>
                            <textarea class="form-control" id="company_address" name="company_address" rows="2"><?= esc($settings['company_address'] ?? 'Tunis, Tunisie') ?></textarea>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_currency">Devise</label>
                                    <select class="form-control" id="default_currency" name="default_currency">
                                        <option value="TND" <?= ($settings['default_currency'] ?? 'TND') === 'TND' ? 'selected' : '' ?>>TND - Dinar Tunisien</option>
                                        <option value="EUR" <?= ($settings['default_currency'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR - Euro</option>
                                        <option value="USD" <?= ($settings['default_currency'] ?? '') === 'USD' ? 'selected' : '' ?>>USD - Dollar US</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="default_language">Langue</label>
                                    <select class="form-control" id="default_language" name="default_language">
                                        <option value="fr" <?= ($settings['default_language'] ?? 'fr') === 'fr' ? 'selected' : '' ?>>Français</option>
                                        <option value="ar" <?= ($settings['default_language'] ?? '') === 'ar' ? 'selected' : '' ?>>العربية</option>
                                        <option value="en" <?= ($settings['default_language'] ?? '') === 'en' ? 'selected' : '' ?>>English</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="timezone">Fuseau Horaire</label>
                                    <select class="form-control" id="timezone" name="timezone">
                                        <option value="Africa/Tunis" <?= ($settings['timezone'] ?? 'Africa/Tunis') === 'Africa/Tunis' ? 'selected' : '' ?>>Africa/Tunis (UTC+1)</option>
                                        <option value="Europe/Paris" <?= ($settings['timezone'] ?? '') === 'Europe/Paris' ? 'selected' : '' ?>>Europe/Paris (UTC+1)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_format">Format de Date</label>
                                    <select class="form-control" id="date_format" name="date_format">
                                        <option value="d/m/Y" <?= ($settings['date_format'] ?? 'd/m/Y') === 'd/m/Y' ? 'selected' : '' ?>>JJ/MM/AAAA</option>
                                        <option value="Y-m-d" <?= ($settings['date_format'] ?? '') === 'Y-m-d' ? 'selected' : '' ?>>AAAA-MM-JJ</option>
                                        <option value="m/d/Y" <?= ($settings['date_format'] ?? '') === 'm/d/Y' ? 'selected' : '' ?>>MM/JJ/AAAA</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="time_format">Format d'Heure</label>
                                    <select class="form-control" id="time_format" name="time_format">
                                        <option value="H:i" <?= ($settings['time_format'] ?? 'H:i') === 'H:i' ? 'selected' : '' ?>>24 heures (13:00)</option>
                                        <option value="h:i A" <?= ($settings['time_format'] ?? '') === 'h:i A' ? 'selected' : '' ?>>12 heures (01:00 PM)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="items_per_page">Éléments par Page</label>
                            <input type="number" class="form-control" id="items_per_page" name="items_per_page" 
                                   value="<?= esc($settings['items_per_page'] ?? '25') ?>" min="10" max="100">
                            <small class="form-text text-muted">Nombre d'éléments affichés dans les tableaux</small>
                        </div>

                        <hr>

                        <div class="form-group">
                            <label>Logo de l'Entreprise</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="company_logo" name="company_logo" accept="image/*">
                                <label class="custom-file-label" for="company_logo">Choisir un fichier...</label>
                            </div>
                            <small class="form-text text-muted">Format recommandé: PNG transparent, 200x60px</small>
                        </div>

                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Enregistrer les Modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Aide
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Nom de l'Entreprise:</strong></p>
                    <p class="text-muted small mb-3">Ce nom apparaîtra sur tous les documents et emails.</p>

                    <p class="mb-2"><strong>Devise:</strong></p>
                    <p class="text-muted small mb-3">La devise utilisée pour tous les prix et transactions.</p>

                    <p class="mb-2"><strong>Langue:</strong></p>
                    <p class="text-muted small mb-3">Langue par défaut de l'interface.</p>

                    <p class="mb-2"><strong>Logo:</strong></p>
                    <p class="text-muted small">Utilisé dans le menu, sur les rapports PDF et emails.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update file input label
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = e.target.files[0]?.name || 'Choisir un fichier...';
    var label = e.target.nextElementSibling;
    label.textContent = fileName;
});
</script>
<?= $this->endSection() ?>
