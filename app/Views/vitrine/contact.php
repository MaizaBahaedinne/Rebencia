<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="rb-page-header">
    <div class="container">
        <h1 class="rb-page-title"><?= lang('Vitrine.contact_title') ?></h1>
        <p class="rb-page-subtitle"><?= lang('Vitrine.contact_subtitle') ?></p>
    </div>
</div>

<section class="py-5">
    <div class="container">

        <!-- Flash success -->
        <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i><?= esc($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Flash errors -->
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

        <div class="row g-5">

            <!-- Formulaire -->
            <div class="col-lg-7">
                <form action="<?= base_url($currentLang . '/contact') ?>" method="post" class="rb-contact-form">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Vitrine.contact_name') ?> *</label>
                        <input type="text" name="name" class="form-control" required
                               value="<?= esc(old('name')) ?>"
                               placeholder="<?= lang('Vitrine.contact_name') ?>">
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Vitrine.contact_email') ?> *</label>
                            <input type="email" name="email" class="form-control" required
                                   value="<?= esc(old('email')) ?>"
                                   placeholder="vous@exemple.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><?= lang('Vitrine.contact_phone') ?></label>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?= esc(old('phone')) ?>"
                                   placeholder="+212 6 00 00 00 00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Vitrine.contact_subject') ?></label>
                        <select name="subject" class="form-select">
                            <option value="Demande d'information">Demande d'information</option>
                            <option value="Demande de visite">Demande de visite</option>
                            <option value="Mise en vente">Mise en vente d'un bien</option>
                            <option value="Mise en location">Mise en location d'un bien</option>
                            <option value="Estimation">Estimation gratuite</option>
                            <option value="Autre">Autre</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold"><?= lang('Vitrine.contact_message') ?> *</label>
                        <textarea name="message" class="form-control" rows="6" required
                                  placeholder="Décrivez votre demande…"><?= esc(old('message')) ?></textarea>
                    </div>
                    <button type="submit" class="btn rb-btn-primary btn-lg w-100">
                        <i class="bi bi-send me-2"></i><?= lang('Vitrine.contact_send') ?>
                    </button>
                </form>
            </div>

            <!-- Infos contact -->
            <div class="col-lg-5">
                <div class="rb-contact-info">
                    <h5 class="fw-bold mb-4"><?= lang('Vitrine.contact_title') ?></h5>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rb-contact-icon"><i class="bi bi-geo-alt-fill"></i></div>
                        <div>
                            <strong><?= lang('Vitrine.contact_address') ?></strong>
                            <p class="mb-0 text-muted">123 Boulevard Mohammed V<br>20000 Casablanca, Maroc</p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rb-contact-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <strong><?= lang('Vitrine.contact_phone_label') ?></strong>
                            <p class="mb-0"><a href="tel:+212522000000" class="text-decoration-none">+212 5 22 00 00 00</a></p>
                            <p class="mb-0"><a href="tel:+212600000000" class="text-decoration-none text-muted">+212 6 00 00 00 00</a></p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rb-contact-icon"><i class="bi bi-envelope-fill"></i></div>
                        <div>
                            <strong><?= lang('Vitrine.contact_email_label') ?></strong>
                            <p class="mb-0"><a href="mailto:contact@rebencia.com" class="text-decoration-none">contact@rebencia.com</a></p>
                        </div>
                    </div>

                    <div class="d-flex gap-3 mb-4">
                        <div class="rb-contact-icon"><i class="bi bi-clock-fill"></i></div>
                        <div>
                            <strong><?= lang('Vitrine.contact_hours') ?></strong>
                            <p class="mb-0 text-muted"><?= lang('Vitrine.contact_hours_val') ?></p>
                        </div>
                    </div>

                    <!-- WhatsApp rapide -->
                    <a href="https://wa.me/212600000000?text=Bonjour+Rebencia%2C+je+voudrais+plus+d%27informations."
                       target="_blank" class="btn btn-outline-success w-100 mt-2">
                        <i class="bi bi-whatsapp me-2"></i>Chat WhatsApp
                    </a>
                </div>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
