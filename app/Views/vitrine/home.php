<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('content') ?>

<!-- =========================================================
     HERO
========================================================= -->
<section class="rb-hero">
    <div class="rb-hero-bg"></div>
    <div class="container rb-hero-content">
        <div class="row justify-content-center text-center">
            <div class="col-lg-9">
                <h1 class="rb-hero-title"><?= lang('Vitrine.hero_title') ?></h1>
                <p class="rb-hero-subtitle"><?= lang('Vitrine.hero_subtitle') ?></p>
            </div>
        </div>

        <!-- Barre de recherche -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-10">
                <div class="rb-search-bar">
                    <form action="<?= base_url($currentLang . '/properties') ?>" method="get" class="row g-2 align-items-end">
                        <!-- Mot-clé -->
                        <div class="col-lg-4 col-md-6">
                            <input type="text" name="q" class="form-control rb-search-input"
                                   placeholder="<?= lang('Vitrine.hero_search_placeholder') ?>">
                        </div>
                        <!-- Type -->
                        <div class="col-lg-2 col-md-6">
                            <select name="type" class="form-select rb-search-input">
                                <option value=""><?= lang('Vitrine.search_all_types') ?></option>
                                <option value="appartement"><?= lang('Vitrine.search_type_apartment') ?></option>
                                <option value="villa"><?= lang('Vitrine.search_type_villa') ?></option>
                                <option value="terrain"><?= lang('Vitrine.search_type_terrain') ?></option>
                                <option value="commercial"><?= lang('Vitrine.search_type_commercial') ?></option>
                                <option value="bureau"><?= lang('Vitrine.search_type_bureau') ?></option>
                            </select>
                        </div>
                        <!-- Transaction -->
                        <div class="col-lg-2 col-md-6">
                            <select name="transaction_type" class="form-select rb-search-input">
                                <option value=""><?= lang('Vitrine.search_all_transactions') ?></option>
                                <option value="vente"><?= lang('Vitrine.search_transaction_buy') ?></option>
                                <option value="location"><?= lang('Vitrine.search_transaction_rent') ?></option>
                            </select>
                        </div>
                        <!-- Submit -->
                        <div class="col-lg-2 col-md-6">
                            <button type="submit" class="btn rb-btn-accent w-100">
                                <i class="bi bi-search me-1"></i><?= lang('Vitrine.hero_btn_search') ?>
                            </button>
                        </div>
                        <!-- Estimer -->
                        <div class="col-lg-2 col-md-6">
                            <a href="<?= base_url($currentLang . '/estimate') ?>" class="btn btn-outline-light w-100">
                                <i class="bi bi-calculator me-1"></i><?= lang('Vitrine.hero_btn_estimate') ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     STATS
========================================================= -->
<section class="rb-stats-bar">
    <div class="container">
        <div class="row g-0 text-center">
            <div class="col-6 col-md-3 rb-stat-item">
                <span class="rb-stat-number" data-count="<?= $stats['properties'] ?>"><?= $stats['properties'] ?>+</span>
                <span class="rb-stat-label"><?= lang('Vitrine.stats_properties') ?></span>
            </div>
            <div class="col-6 col-md-3 rb-stat-item">
                <span class="rb-stat-number" data-count="<?= $stats['sold'] ?>"><?= $stats['sold'] ?>+</span>
                <span class="rb-stat-label"><?= lang('Vitrine.stats_sold') ?></span>
            </div>
            <div class="col-6 col-md-3 rb-stat-item">
                <span class="rb-stat-number" data-count="<?= $stats['agents'] ?>"><?= $stats['agents'] ?>+</span>
                <span class="rb-stat-label"><?= lang('Vitrine.stats_agents') ?></span>
            </div>
            <div class="col-6 col-md-3 rb-stat-item">
                <span class="rb-stat-number" data-count="<?= $stats['years'] ?>"><?= $stats['years'] ?>+</span>
                <span class="rb-stat-label"><?= lang('Vitrine.stats_years') ?></span>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     BIENS EN VEDETTE
========================================================= -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="rb-section-title"><?= lang('Vitrine.featured_title') ?></h2>
            <p class="rb-section-subtitle"><?= lang('Vitrine.featured_subtitle') ?></p>
        </div>

        <?php if (empty($featured)): ?>
        <p class="text-center text-muted"><?= lang('Vitrine.no_results') ?></p>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($featured as $p): ?>
            <?= view('vitrine/partials/property_card', ['p' => $p, 'currentLang' => $currentLang]) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="text-center mt-5">
            <a href="<?= base_url($currentLang . '/properties') ?>" class="btn rb-btn-primary btn-lg">
                <?= lang('Vitrine.btn_view_all') ?> <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>

<!-- =========================================================
     POURQUOI REBENCIA ?
========================================================= -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="rb-section-title"><?= lang('Vitrine.why_title') ?></h2>
            <p class="rb-section-subtitle"><?= lang('Vitrine.why_subtitle') ?></p>
        </div>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="rb-why-card">
                    <div class="rb-why-icon"><i class="bi bi-map"></i></div>
                    <h5><?= lang('Vitrine.why_1_title') ?></h5>
                    <p><?= lang('Vitrine.why_1_text') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="rb-why-card">
                    <div class="rb-why-icon"><i class="bi bi-shield-check"></i></div>
                    <h5><?= lang('Vitrine.why_2_title') ?></h5>
                    <p><?= lang('Vitrine.why_2_text') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="rb-why-card">
                    <div class="rb-why-icon"><i class="bi bi-eye"></i></div>
                    <h5><?= lang('Vitrine.why_3_title') ?></h5>
                    <p><?= lang('Vitrine.why_3_text') ?></p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="rb-why-card">
                    <div class="rb-why-icon"><i class="bi bi-building"></i></div>
                    <h5><?= lang('Vitrine.why_4_title') ?></h5>
                    <p><?= lang('Vitrine.why_4_text') ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- =========================================================
     TÉMOIGNAGES
========================================================= -->
<section class="py-5 rb-testimonials-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="rb-section-title text-white"><?= lang('Vitrine.testimonials_title') ?></h2>
        </div>
        <div class="row g-4 justify-content-center">
            <?php
            $testimonials = [
                ['name' => 'Karim B.', 'city' => 'Tunis', 'text' => 'Service impeccable, j\'ai trouvé mon appartement en moins de 2 semaines. Équipe très professionnelle !', 'rating' => 5],
                ['name' => 'Sara M.', 'city' => 'Sfax', 'text' => 'L\'agence Rebencia nous a accompagnés du début à la fin. Je recommande vivement !', 'rating' => 5],
                ['name' => 'Ahmed K.', 'city' => 'Sousse', 'text' => 'Excellente expérience, des agents à l\'ecoute et des biens de qualité. Merci Rebencia !', 'rating' => 5],
            ];
            foreach ($testimonials as $t): ?>
            <div class="col-md-4">
                <div class="rb-testimonial-card">
                    <div class="rb-testimonial-stars">
                        <?php for ($s = 0; $s < $t['rating']; $s++): ?>
                        <i class="bi bi-star-fill text-warning"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="rb-testimonial-text">"<?= esc($t['text']) ?>"</p>
                    <div class="rb-testimonial-author">
                        <div class="rb-testimonial-avatar">
                            <?= strtoupper(substr($t['name'], 0, 1)) ?>
                        </div>
                        <div>
                            <strong><?= esc($t['name']) ?></strong>
                            <small class="d-block text-muted"><?= esc($t['city']) ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- =========================================================
     CTA
========================================================= -->
<section class="rb-cta-section">
    <div class="container text-center">
        <h2 class="rb-cta-title"><?= lang('Vitrine.cta_title') ?></h2>
        <p class="rb-cta-subtitle"><?= lang('Vitrine.cta_subtitle') ?></p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="<?= base_url($currentLang . '/contact') ?>" class="btn rb-btn-accent btn-lg">
                <i class="bi bi-chat-dots me-1"></i><?= lang('Vitrine.cta_btn') ?>
            </a>
            <a href="<?= base_url($currentLang . '/estimate') ?>" class="btn btn-outline-light btn-lg">
                <i class="bi bi-calculator me-1"></i><?= lang('Vitrine.cta_btn2') ?>
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
