<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="rb-page-header">
    <div class="container">
        <h1 class="rb-page-title"><?= lang('Vitrine.about_title') ?></h1>
        <p class="rb-page-subtitle"><?= lang('Vitrine.about_subtitle') ?></p>
    </div>
</div>

<!-- Notre Histoire -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="rb-section-title text-start"><?= lang('Vitrine.about_story_title') ?></h2>
                <p class="text-secondary lh-lg">
                    Fondée il y a plus de 10 ans, <strong>Rebencia</strong> est née de la passion de deux associés
                    pour l'immobilier marocain. Partis d'une modeste agence à Casablanca, nous sommes aujourd'hui
                    présents dans les principales villes du royaume, forts d'une équipe de professionnels dévoués.
                </p>
                <p class="text-secondary lh-lg">
                    Notre philosophie : placer <strong>l'humain au cœur de chaque transaction</strong>.
                    Chaque client est unique, chaque projet mérite une attention particulière.
                </p>
                <div class="row g-3 mt-2">
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded-3">
                            <div class="display-6 fw-bold rb-text-accent">10+</div>
                            <small class="text-muted"><?= lang('Vitrine.stats_years') ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center p-3 bg-light rounded-3">
                            <div class="display-6 fw-bold rb-text-accent">500+</div>
                            <small class="text-muted"><?= lang('Vitrine.stats_sold') ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="rb-about-illustration">
                    <i class="bi bi-buildings" style="font-size:8rem;color:var(--rb-primary);opacity:.15;"></i>
                    <div class="rb-about-badge rb-badge-primary">
                        <i class="bi bi-patch-check-fill me-1"></i>Agence certifiée
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Nos Valeurs -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="rb-section-title"><?= lang('Vitrine.about_values_title') ?></h2>
        </div>
        <div class="row g-4">
            <?php
            $values = [
                ['icon' => 'bi-hand-thumbs-up', 'title' => 'Intégrité',       'text' => 'Nous agissons toujours dans l\'intérêt de nos clients, avec honnêteté et transparence.'],
                ['icon' => 'bi-heart',           'title' => 'Engagement',       'text' => 'Chaque projet est traité avec le même niveau de soin et de professionnalisme.'],
                ['icon' => 'bi-lightning',       'title' => 'Réactivité',       'text' => 'Nous sommes disponibles et réactifs à chaque étape de votre projet.'],
                ['icon' => 'bi-award',           'title' => 'Excellence',        'text' => 'Nous visons l\'excellence à travers une formation continue et des standards élevés.'],
                ['icon' => 'bi-people',          'title' => 'Esprit d\'équipe', 'text' => 'Nos agents collaborent pour vous offrir les meilleures opportunités.'],
                ['icon' => 'bi-leaf',            'title' => 'Durabilité',       'text' => 'Nous promouvons des pratiques immobilières responsables et durables.'],
            ];
            foreach ($values as $v): ?>
            <div class="col-md-6 col-lg-4">
                <div class="rb-why-card">
                    <div class="rb-why-icon"><i class="bi <?= $v['icon'] ?>"></i></div>
                    <h5><?= $v['title'] ?></h5>
                    <p><?= $v['text'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Notre Équipe -->
<?php if (!empty($team)): ?>
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="rb-section-title"><?= lang('Vitrine.about_team_title') ?></h2>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($team as $member): ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="rb-team-card text-center">
                    <?php if (!empty($member['avatar'])): ?>
                    <img src="<?= base_url('uploads/' . esc($member['avatar'])) ?>"
                         class="rb-team-avatar" alt="<?= esc($member['first_name']) ?>">
                    <?php else: ?>
                    <div class="rb-team-avatar-placeholder mx-auto">
                        <?= strtoupper(substr($member['first_name'] ?? 'A', 0, 1) . substr($member['last_name'] ?? '', 0, 1)) ?>
                    </div>
                    <?php endif; ?>
                    <h6 class="mt-3 mb-0 fw-semibold"><?= esc($member['first_name'] . ' ' . $member['last_name']) ?></h6>
                    <small class="text-muted"><?= esc($member['role_label'] ?? '') ?></small>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="rb-cta-section">
    <div class="container text-center">
        <h2 class="rb-cta-title"><?= lang('Vitrine.cta_title') ?></h2>
        <p class="rb-cta-subtitle"><?= lang('Vitrine.cta_subtitle') ?></p>
        <a href="<?= base_url($currentLang . '/contact') ?>" class="btn rb-btn-accent btn-lg">
            <i class="bi bi-chat-dots me-1"></i><?= lang('Vitrine.cta_btn') ?>
        </a>
    </div>
</section>

<?= $this->endSection() ?>
