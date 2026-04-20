<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('content') ?>

<!-- Header -->
<div class="rb-page-header">
    <div class="container">
        <h1 class="rb-page-title"><?= lang('Vitrine.blog_title') ?></h1>
        <p class="rb-page-subtitle"><?= lang('Vitrine.blog_subtitle') ?></p>
    </div>
</div>

<section class="py-5">
    <div class="container">
        <?php if (empty($posts)): ?>
        <!-- Coming soon -->
        <div class="text-center py-5">
            <i class="bi bi-journal-text" style="font-size:5rem;color:var(--rb-primary);opacity:.2;"></i>
            <h4 class="mt-3 text-muted"><?= lang('Vitrine.blog_coming_soon') ?></h4>
            <a href="<?= base_url($currentLang . '/') ?>" class="btn rb-btn-primary mt-3">
                <i class="bi bi-arrow-left me-1"></i><?= lang('Vitrine.nav_home') ?>
            </a>
        </div>
        <?php else: ?>
        <div class="row g-4">
            <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-lg-4">
                <article class="rb-blog-card">
                    <?php if (!empty($post['cover'])): ?>
                    <img src="<?= esc($post['cover']) ?>" class="rb-blog-img" alt="<?= esc($post['title']) ?>">
                    <?php endif; ?>
                    <div class="rb-blog-body">
                        <div class="d-flex gap-2 mb-2">
                            <?php if (!empty($post['category'])): ?>
                            <span class="rb-badge rb-badge-primary"><?= esc($post['category']) ?></span>
                            <?php endif; ?>
                            <span class="text-muted small"><?= !empty($post['created_at']) ? date('d/m/Y', strtotime($post['created_at'])) : '' ?></span>
                        </div>
                        <h5 class="rb-blog-title"><?= esc($post['title']) ?></h5>
                        <p class="text-secondary"><?= esc(substr(strip_tags($post['excerpt'] ?? $post['content'] ?? ''), 0, 120)) ?>…</p>
                        <a href="#" class="btn rb-btn-primary btn-sm"><?= lang('Vitrine.blog_read_more') ?></a>
                    </div>
                </article>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>
