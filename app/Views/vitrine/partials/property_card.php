<div class="col-md-6 col-lg-4">
    <div class="rb-property-card">
        <!-- Image -->
        <a href="<?= base_url($currentLang . '/properties/' . $p['id']) ?>" class="rb-card-img-link">
            <?php if (!empty($p['main_image'])): ?>
            <img src="<?= base_url('uploads/' . esc($p['main_image'])) ?>"
                 alt="<?= esc($p['title']) ?>" class="rb-card-img" loading="lazy">
            <?php else: ?>
            <div class="rb-card-img-placeholder">
                <i class="bi bi-image"></i>
            </div>
            <?php endif; ?>
            <!-- Badges -->
            <div class="rb-card-badges">
                <?php if (!empty($p['featured'])): ?>
                <span class="rb-badge rb-badge-accent"><?= lang('Vitrine.label_featured') ?></span>
                <?php endif; ?>
                <span class="rb-badge <?= $p['transaction_type'] === 'vente' ? 'rb-badge-primary' : 'rb-badge-success' ?>">
                    <?= $p['transaction_type'] === 'vente' ? lang('Vitrine.label_buy') : lang('Vitrine.label_rent') ?>
                </span>
            </div>
        </a>

        <!-- Body -->
        <div class="rb-card-body">
            <p class="rb-card-ref"><?= lang('Vitrine.label_ref') ?> <?= esc($p['reference'] ?? '#' . $p['id']) ?></p>
            <h5 class="rb-card-title">
                <a href="<?= base_url($currentLang . '/properties/' . $p['id']) ?>"><?= esc($p['title']) ?></a>
            </h5>
            <p class="rb-card-location">
                <i class="bi bi-geo-alt-fill text-danger me-1"></i><?= esc($p['city'] ?? '') ?>
                <?= !empty($p['zone']) ? ' — ' . esc($p['zone']) : '' ?>
            </p>

            <!-- Caractéristiques -->
            <div class="rb-card-features">
                <?php if (!empty($p['surface'])): ?>
                <span><i class="bi bi-arrows-angle-expand"></i> <?= number_format($p['surface'], 0, ',', ' ') ?> <?= lang('Vitrine.label_surface') ?></span>
                <?php endif; ?>
                <?php if (!empty($p['rooms'])): ?>
                <span><i class="bi bi-door-open"></i> <?= (int)$p['rooms'] ?> <?= lang('Vitrine.label_rooms') ?></span>
                <?php endif; ?>
                <?php if (!empty($p['bedrooms'])): ?>
                <span><i class="bi bi-moon-stars"></i> <?= (int)$p['bedrooms'] ?></span>
                <?php endif; ?>
                <?php if (!empty($p['bathrooms'])): ?>
                <span><i class="bi bi-droplet-half"></i> <?= (int)$p['bathrooms'] ?></span>
                <?php endif; ?>
            </div>

            <!-- Prix + CTA -->
            <div class="rb-card-footer">
                <div class="rb-card-price">
                    <?= number_format((float)$p['price'], 0, ',', ' ') ?>
                    <small><?= lang('Vitrine.label_price') ?><?= $p['transaction_type'] === 'location' ? '/mois' : '' ?></small>
                </div>
                <a href="<?= base_url($currentLang . '/properties/' . $p['id']) ?>" class="btn rb-btn-primary btn-sm">
                    <?= lang('Vitrine.btn_details') ?>
                </a>
            </div>
        </div>
    </div>
</div>
