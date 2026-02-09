<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-th-large"></i> <?= esc($page_title) ?>
        </h1>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-10">
            <form action="<?= base_url('admin/settings/updateFooter') ?>" method="post">
                <?= csrf_field() ?>
                
                <!-- General Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-info-circle"></i> Informations Générales
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="site_name">Nom du site</label>
                            <input type="text" class="form-control" id="site_name" name="site_name" 
                                   value="<?= esc($settings['site_name'] ?? 'REBENCIA') ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label for="site_description">Description du site</label>
                            <textarea class="form-control" id="site_description" name="site_description" rows="2"><?= esc($settings['site_description'] ?? '') ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="footer_about">Texte À propos (Footer)</label>
                            <textarea class="form-control" id="footer_about" name="footer_about" rows="3"><?= esc($settings['footer_about'] ?? '') ?></textarea>
                            <small class="text-muted">Ce texte sera affiché dans la section "À propos" du footer</small>
                        </div>
                    </div>
                </div>

                <!-- Contact Info Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-address-card"></i> Informations de Contact
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contact_phone_1">Téléphone 1</label>
                                    <input type="text" class="form-control" id="contact_phone_1" name="contact_phone_1" 
                                           value="<?= esc($settings['contact_phone_1'] ?? '') ?>" placeholder="+216 XX XXX XXX">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="contact_phone_2">Téléphone 2</label>
                                    <input type="text" class="form-control" id="contact_phone_2" name="contact_phone_2" 
                                           value="<?= esc($settings['contact_phone_2'] ?? '') ?>" placeholder="+216 XX XXX XXX">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="contact_email">Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                   value="<?= esc($settings['contact_email'] ?? '') ?>" placeholder="contact@rebencia.tn">
                        </div>

                        <div class="form-group mb-3">
                            <label for="contact_address">Adresse complète</label>
                            <textarea class="form-control" id="contact_address" name="contact_address" rows="2"><?= esc($settings['contact_address'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Social Media Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-share-alt"></i> Réseaux Sociaux
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label for="social_facebook">
                                <i class="fab fa-facebook text-primary"></i> Facebook
                            </label>
                            <input type="url" class="form-control" id="social_facebook" name="social_facebook" 
                                   value="<?= esc($settings['social_facebook'] ?? '') ?>" placeholder="https://facebook.com/votre-page">
                        </div>

                        <div class="form-group mb-3">
                            <label for="social_instagram">
                                <i class="fab fa-instagram text-danger"></i> Instagram
                            </label>
                            <input type="url" class="form-control" id="social_instagram" name="social_instagram" 
                                   value="<?= esc($settings['social_instagram'] ?? '') ?>" placeholder="https://instagram.com/votre-compte">
                        </div>

                        <div class="form-group mb-3">
                            <label for="social_linkedin">
                                <i class="fab fa-linkedin text-info"></i> LinkedIn
                            </label>
                            <input type="url" class="form-control" id="social_linkedin" name="social_linkedin" 
                                   value="<?= esc($settings['social_linkedin'] ?? '') ?>" placeholder="https://linkedin.com/company/votre-entreprise">
                        </div>

                        <div class="form-group mb-3">
                            <label for="social_youtube">
                                <i class="fab fa-youtube text-danger"></i> YouTube
                            </label>
                            <input type="url" class="form-control" id="social_youtube" name="social_youtube" 
                                   value="<?= esc($settings['social_youtube'] ?? '') ?>" placeholder="https://youtube.com/@votre-chaine">
                        </div>

                        <div class="form-group mb-3">
                            <label for="social_whatsapp">
                                <i class="fab fa-whatsapp text-success"></i> WhatsApp
                            </label>
                            <input type="text" class="form-control" id="social_whatsapp" name="social_whatsapp" 
                                   value="<?= esc($settings['social_whatsapp'] ?? '') ?>" placeholder="+21612345678">
                            <small class="text-muted">Numéro au format international sans espaces</small>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="card shadow mb-4">
                    <div class="card-body text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
