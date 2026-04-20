<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('head') ?>
<style>
    .rb-gallery-thumb { cursor: pointer; transition: opacity .2s; border: 2px solid transparent; border-radius: 6px; overflow: hidden; }
    .rb-gallery-thumb:hover, .rb-gallery-thumb.active { border-color: var(--rb-accent); opacity: .85; }
    .rb-main-photo { width: 100%; height: 420px; object-fit: cover; border-radius: 12px; }
    .rb-share-btn { font-size: .85rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Breadcrumb -->
<div class="bg-light py-2 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= base_url($currentLang . '/') ?>"><?= lang('Vitrine.nav_home') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url($currentLang . '/properties') ?>"><?= lang('Vitrine.nav_properties') ?></a></li>
                <li class="breadcrumb-item active"><?= esc($property['title']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<section class="py-4">
    <div class="container">
        <div class="row g-4">

            <!-- Colonne principale -->
            <div class="col-lg-8">

                <!-- Galerie photos -->
                <?php
                $mainImg = '';
                foreach ($images as $img) {
                    if ($img['is_main']) { $mainImg = $img['file_path']; break; }
                }
                if (!$mainImg && !empty($images)) $mainImg = $images[0]['file_path'];
                ?>
                <?php if ($mainImg): ?>
                <img src="<?= base_url('uploads/' . esc($mainImg)) ?>"
                     alt="<?= esc($property['title']) ?>"
                     class="rb-main-photo mb-3" id="mainPhoto">
                <?php if (count($images) > 1): ?>
                <div class="d-flex gap-2 flex-wrap mb-3" id="galleryThumbs">
                    <?php foreach ($images as $img): ?>
                    <div class="rb-gallery-thumb <?= $img['file_path'] === $mainImg ? 'active' : '' ?>"
                         style="width:80px;height:60px;"
                         onclick="switchPhoto(this, '<?= base_url('uploads/' . esc($img['file_path'])) ?>')">
                        <img src="<?= base_url('uploads/' . esc($img['file_path'])) ?>"
                             alt="" style="width:100%;height:100%;object-fit:cover;">
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <?php else: ?>
                <div class="rb-card-img-placeholder mb-3" style="height:300px;border-radius:12px;">
                    <i class="bi bi-image" style="font-size:3rem;"></i>
                </div>
                <?php endif; ?>

                <!-- Titre + badges -->
                <div class="d-flex align-items-start gap-2 flex-wrap mb-2">
                    <span class="rb-badge <?= $property['transaction_type'] === 'vente' ? 'rb-badge-primary' : 'rb-badge-success' ?> fs-6">
                        <?= $property['transaction_type'] === 'vente' ? lang('Vitrine.label_buy') : lang('Vitrine.label_rent') ?>
                    </span>
                    <?php if (!empty($property['featured'])): ?>
                    <span class="rb-badge rb-badge-accent fs-6"><?= lang('Vitrine.label_featured') ?></span>
                    <?php endif; ?>
                </div>
                <h1 class="h3 fw-bold mb-1"><?= esc($property['title']) ?></h1>
                <p class="text-muted mb-3">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                    <?= esc($property['city'] ?? '') ?><?= !empty($property['zone']) ? ', ' . esc($property['zone']) : '' ?>
                    <?= !empty($property['address']) ? ' — ' . esc($property['address']) : '' ?>
                </p>

                <!-- Prix -->
                <div class="rb-detail-price mb-4">
                    <span class="rb-price-main"><?= number_format((float)$property['price'], 0, ',', ' ') ?></span>
                    <span class="rb-price-currency"> DH<?= $property['transaction_type'] === 'location' ? '/mois' : '' ?></span>
                </div>

                <!-- Description -->
                <div class="rb-detail-section">
                    <h4 class="rb-detail-section-title"><?= lang('Vitrine.detail_description') ?></h4>
                    <p class="text-secondary lh-lg"><?= nl2br(esc($property['description'] ?? '')) ?></p>
                </div>

                <!-- Caractéristiques -->
                <div class="rb-detail-section">
                    <h4 class="rb-detail-section-title"><?= lang('Vitrine.detail_characteristics') ?></h4>
                    <div class="row g-3">
                        <?php
                        $chars = [
                            ['icon' => 'bi-arrows-angle-expand', 'label' => lang('Vitrine.detail_surface'),   'value' => !empty($property['surface']) ? number_format($property['surface'], 0, ',', ' ') . ' m²' : null],
                            ['icon' => 'bi-door-open',           'label' => lang('Vitrine.detail_rooms'),     'value' => !empty($property['rooms'])   ? $property['rooms'] : null],
                            ['icon' => 'bi-moon-stars',          'label' => lang('Vitrine.detail_bedrooms'),  'value' => !empty($property['bedrooms']) ? $property['bedrooms'] : null],
                            ['icon' => 'bi-droplet-half',        'label' => lang('Vitrine.detail_bathrooms'), 'value' => !empty($property['bathrooms']) ? $property['bathrooms'] : null],
                            ['icon' => 'bi-building',            'label' => lang('Vitrine.detail_floor'),     'value' => isset($property['floor']) && $property['floor'] !== null ? $property['floor'] : null],
                            ['icon' => 'bi-car-front',           'label' => lang('Vitrine.detail_parking'),   'value' => !empty($property['parking']) ? lang('Vitrine.yes') : null],
                            ['icon' => 'bi-house-heart',         'label' => lang('Vitrine.detail_furnished'), 'value' => !empty($property['furnished']) ? lang('Vitrine.yes') : null],
                            ['icon' => 'bi-tag',                 'label' => lang('Vitrine.detail_ref'),       'value' => $property['reference'] ?? '#' . $property['id']],
                            ['icon' => 'bi-geo',                 'label' => lang('Vitrine.detail_city'),      'value' => $property['city'] ?? null],
                            ['icon' => 'bi-house',               'label' => lang('Vitrine.detail_type'),      'value' => ucfirst($property['type'] ?? '')],
                        ];
                        foreach ($chars as $c):
                            if ($c['value'] === null) continue;
                        ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="rb-char-item">
                                <i class="bi <?= $c['icon'] ?> text-warning"></i>
                                <div>
                                    <span class="rb-char-label"><?= esc($c['label']) ?></span>
                                    <span class="rb-char-value"><?= esc((string)$c['value']) ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Partage -->
                <div class="d-flex gap-2 mt-4 flex-wrap">
                    <span class="text-muted small align-self-center"><?= lang('Vitrine.detail_share') ?> :</span>
                    <a href="https://wa.me/?text=<?= urlencode($property['title'] . ' — ' . current_url()) ?>"
                       target="_blank" class="btn btn-outline-success btn-sm rb-share-btn">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>"
                       target="_blank" class="btn btn-outline-primary btn-sm rb-share-btn">
                        <i class="bi bi-facebook"></i> Facebook
                    </a>
                    <button onclick="navigator.clipboard.writeText('<?= current_url() ?>');this.textContent='✓ Copié !'"
                            class="btn btn-outline-secondary btn-sm rb-share-btn">
                        <i class="bi bi-link-45deg"></i> Copier le lien
                    </button>
                </div>
            </div>

            <!-- Sidebar droite -->
            <div class="col-lg-4">

                <!-- Carte agent -->
                <?php if (!empty($agent)): ?>
                <div class="rb-agent-card mb-4">
                    <div class="text-center mb-3">
                        <?php if (!empty($agent['avatar'])): ?>
                        <img src="<?= base_url('uploads/' . esc($agent['avatar'])) ?>"
                             class="rb-agent-avatar" alt="<?= esc($agent['first_name']) ?>">
                        <?php else: ?>
                        <div class="rb-agent-avatar-placeholder">
                            <?= strtoupper(substr($agent['first_name'] ?? 'A', 0, 1) . substr($agent['last_name'] ?? '', 0, 1)) ?>
                        </div>
                        <?php endif; ?>
                        <h6 class="mt-2 mb-0"><?= esc($agent['first_name'] . ' ' . $agent['last_name']) ?></h6>
                        <small class="text-muted"><?= lang('Vitrine.detail_agent') ?></small>
                    </div>
                    <?php if (!empty($agent['phone'])): ?>
                    <a href="tel:<?= esc($agent['phone']) ?>" class="btn rb-btn-primary w-100 mb-2">
                        <i class="bi bi-telephone me-1"></i><?= esc($agent['phone']) ?>
                    </a>
                    <?php endif; ?>
                    <a href="https://wa.me/<?= preg_replace('/\D/', '', $agent['phone'] ?? '') ?>?text=<?= urlencode("Bonjour, je suis intéressé par le bien réf. " . ($property['reference'] ?? '#' . $property['id'])) ?>"
                       target="_blank" class="btn btn-outline-success w-100 btn-sm">
                        <i class="bi bi-whatsapp me-1"></i>WhatsApp
                    </a>
                </div>
                <?php endif; ?>

                <!-- Formulaire de contact -->
                <div class="rb-contact-widget">
                    <h6 class="mb-3"><i class="bi bi-chat-dots me-1"></i><?= lang('Vitrine.detail_contact_form') ?></h6>
                    <form action="<?= base_url($currentLang . '/contact') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="subject"
                               value="Demande sur : <?= esc($property['title']) ?> (Réf. <?= esc($property['reference'] ?? $property['id']) ?>)">
                        <div class="mb-2">
                            <input type="text" name="name" class="form-control form-control-sm"
                                   placeholder="<?= lang('Vitrine.contact_name') ?>" required>
                        </div>
                        <div class="mb-2">
                            <input type="email" name="email" class="form-control form-control-sm"
                                   placeholder="<?= lang('Vitrine.contact_email') ?>" required>
                        </div>
                        <div class="mb-2">
                            <input type="tel" name="phone" class="form-control form-control-sm"
                                   placeholder="<?= lang('Vitrine.contact_phone') ?>">
                        </div>
                        <div class="mb-2">
                            <textarea name="message" class="form-control form-control-sm" rows="3"
                                      placeholder="<?= lang('Vitrine.contact_message') ?>"><?= "Je suis intéressé(e) par ce bien. Merci de me recontacter." ?></textarea>
                        </div>
                        <button type="submit" class="btn rb-btn-accent w-100 btn-sm">
                            <i class="bi bi-send me-1"></i><?= lang('Vitrine.contact_send') ?>
                        </button>
                    </form>
                </div>

                <!-- Info rapide -->
                <div class="mt-3 small text-muted text-center">
                    <i class="bi bi-eye me-1"></i><?= number_format((int)$property['views_count'], 0, ',', ' ') ?> vue(s)
                    <?php if (!empty($property['published_at'])): ?>
                    &nbsp;·&nbsp; <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y', strtotime($property['published_at'])) ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>

        <!-- Biens similaires -->
        <?php if (!empty($similar)): ?>
        <hr class="my-5">
        <h4 class="mb-4"><?= lang('Vitrine.detail_similar') ?></h4>
        <div class="row g-4">
            <?php foreach ($similar as $p): ?>
            <?= $this->include('vitrine/partials/property_card', ['p' => $p]) ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="mt-4">
            <a href="<?= base_url($currentLang . '/properties') ?>" class="text-decoration-none text-muted">
                <?= lang('Vitrine.btn_back_catalogue') ?>
            </a>
        </div>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function switchPhoto(thumb, url) {
    document.getElementById('mainPhoto').src = url;
    document.querySelectorAll('.rb-gallery-thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}
</script>
<?= $this->endSection() ?>
