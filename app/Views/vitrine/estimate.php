<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="rb-page-header">
    <div class="container">
        <h1 class="rb-page-title"><?= lang('Vitrine.estimate_title') ?></h1>
        <p class="rb-page-subtitle"><?= lang('Vitrine.estimate_subtitle') ?></p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Success -->
                <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show mb-4">
                    <i class="bi bi-check-circle-fill me-2"></i><?= esc($success) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <!-- Errors -->
                <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mb-4">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <ul class="mb-0 ps-3">
                        <?php foreach ($errors as $err): ?>
                        <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Avantages -->
                <div class="row g-3 mb-5 text-center">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <i class="bi bi-clock-history text-warning fs-2"></i>
                            <p class="mb-0 mt-2 fw-semibold small">Réponse sous 24h</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <i class="bi bi-shield-check text-success fs-2"></i>
                            <p class="mb-0 mt-2 fw-semibold small">Gratuit & sans engagement</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-3">
                            <i class="bi bi-person-check text-primary fs-2"></i>
                            <p class="mb-0 mt-2 fw-semibold small">Expert local dédié</p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire estimation -->
                <div class="rb-estimate-form">
                    <form action="<?= base_url($currentLang . '/estimate') ?>" method="post">
                        <?= csrf_field() ?>

                        <!-- Étape 1 : Bien -->
                        <h6 class="rb-form-step-title">
                            <span class="rb-step-number">1</span> <?= lang('Vitrine.estimate_step1') ?>
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Type de bien *</label>
                                <select name="property_type" class="form-select" required>
                                    <option value=""><?= lang('Vitrine.search_all_types') ?></option>
                                    <option value="appartement" <?= old('property_type') === 'appartement' ? 'selected' : '' ?>><?= lang('Vitrine.search_type_apartment') ?></option>
                                    <option value="villa"       <?= old('property_type') === 'villa'       ? 'selected' : '' ?>><?= lang('Vitrine.search_type_villa') ?></option>
                                    <option value="terrain"     <?= old('property_type') === 'terrain'     ? 'selected' : '' ?>><?= lang('Vitrine.search_type_terrain') ?></option>
                                    <option value="commercial"  <?= old('property_type') === 'commercial'  ? 'selected' : '' ?>><?= lang('Vitrine.search_type_commercial') ?></option>
                                    <option value="bureau"      <?= old('property_type') === 'bureau'      ? 'selected' : '' ?>><?= lang('Vitrine.search_type_bureau') ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Surface (m²) *</label>
                                <input type="number" name="surface" class="form-control" required min="1"
                                       value="<?= esc(old('surface')) ?>" placeholder="ex : 120">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Transaction souhaitée</label>
                                <select name="transaction_type" class="form-select">
                                    <option value="vente"   ><?= lang('Vitrine.search_transaction_buy') ?></option>
                                    <option value="location"><?= lang('Vitrine.search_transaction_rent') ?></option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre de pièces</label>
                                <input type="number" name="rooms" class="form-control" min="0"
                                       value="<?= esc(old('rooms')) ?>" placeholder="ex : 4">
                            </div>
                        </div>

                        <!-- Étape 2 : Localisation -->
                        <h6 class="rb-form-step-title">
                            <span class="rb-step-number">2</span> <?= lang('Vitrine.estimate_step2') ?>
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ville *</label>
                                <input type="text" name="city" class="form-control" required
                                       value="<?= esc(old('city')) ?>" placeholder="ex : Casablanca">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Quartier / Zone</label>
                                <input type="text" name="neighborhood" class="form-control"
                                       value="<?= esc(old('neighborhood')) ?>" placeholder="ex : Maarif">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Informations complémentaires</label>
                                <textarea name="extra_info" class="form-control" rows="2"
                                          placeholder="État du bien, étage, vue, rénovations récentes…"><?= esc(old('extra_info')) ?></textarea>
                            </div>
                        </div>

                        <!-- Étape 3 : Vos coordonnées -->
                        <h6 class="rb-form-step-title">
                            <span class="rb-step-number">3</span> <?= lang('Vitrine.estimate_step3') ?>
                        </h6>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><?= lang('Vitrine.contact_name') ?> *</label>
                                <input type="text" name="name" class="form-control" required
                                       value="<?= esc(old('name')) ?>" placeholder="Prénom Nom">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><?= lang('Vitrine.contact_phone') ?> *</label>
                                <input type="tel" name="phone" class="form-control" required
                                       value="<?= esc(old('phone')) ?>" placeholder="+212 6 00 00 00 00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold"><?= lang('Vitrine.contact_email') ?> *</label>
                                <input type="email" name="email" class="form-control" required
                                       value="<?= esc(old('email')) ?>" placeholder="vous@exemple.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Meilleur moment pour vous contacter</label>
                                <select name="preferred_time" class="form-select">
                                    <option value="matin">Matin (9h – 12h)</option>
                                    <option value="apresmidi">Après-midi (12h – 18h)</option>
                                    <option value="soir">Soir (après 18h)</option>
                                </select>
                            </div>
                        </div>

                        <button type="submit" class="btn rb-btn-accent btn-lg w-100">
                            <i class="bi bi-calculator me-2"></i><?= lang('Vitrine.estimate_send') ?>
                        </button>
                        <p class="text-center text-muted small mt-2">
                            <i class="bi bi-lock me-1"></i>Vos données sont confidentielles et ne seront jamais partagées.
                        </p>
                    </form>
                </div>

            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
