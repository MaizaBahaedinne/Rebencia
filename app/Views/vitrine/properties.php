<?= $this->extend('layouts/vitrine') ?>

<?= $this->section('content') ?>

<!-- Page header -->
<div class="rb-page-header">
    <div class="container">
        <h1 class="rb-page-title"><?= lang('Vitrine.filter_title') ?></h1>
        <p class="rb-page-subtitle"><?= lang('Vitrine.filter_subtitle') ?></p>
    </div>
</div>

<section class="py-4">
    <div class="container">
        <div class="row g-4">

            <!-- Sidebar filtres -->
            <div class="col-lg-3">
                <div class="rb-filter-panel">
                    <h6 class="rb-filter-title">
                        <i class="bi bi-funnel me-1"></i><?= lang('Vitrine.filter_btn') ?>
                    </h6>
                    <form id="filterForm" action="<?= base_url($currentLang . '/properties') ?>" method="get">
                        <!-- Mot-clé -->
                        <div class="mb-3">
                            <input type="text" name="q" class="form-control form-control-sm"
                                   placeholder="<?= lang('Vitrine.filter_keyword') ?>"
                                   value="<?= esc($filters['keyword']) ?>">
                        </div>

                        <!-- Type de bien -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Type</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value=""><?= lang('Vitrine.search_all_types') ?></option>
                                <?php foreach (['appartement' => lang('Vitrine.search_type_apartment'), 'villa' => lang('Vitrine.search_type_villa'), 'terrain' => lang('Vitrine.search_type_terrain'), 'commercial' => lang('Vitrine.search_type_commercial'), 'bureau' => lang('Vitrine.search_type_bureau')] as $val => $lbl): ?>
                                <option value="<?= $val ?>" <?= $filters['type'] === $val ? 'selected' : '' ?>><?= $lbl ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Transaction -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Transaction</label>
                            <select name="transaction_type" class="form-select form-select-sm">
                                <option value=""><?= lang('Vitrine.search_all_transactions') ?></option>
                                <option value="vente" <?= $filters['transaction_type'] === 'vente' ? 'selected' : '' ?>><?= lang('Vitrine.search_transaction_buy') ?></option>
                                <option value="location" <?= $filters['transaction_type'] === 'location' ? 'selected' : '' ?>><?= lang('Vitrine.search_transaction_rent') ?></option>
                            </select>
                        </div>

                        <!-- Budget -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Budget (DH)</label>
                            <div class="row g-1">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control form-control-sm"
                                           placeholder="Min" value="<?= $filters['min_price'] ?: '' ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control form-control-sm"
                                           placeholder="Max" value="<?= $filters['max_price'] ?: '' ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Surface -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold"><?= lang('Vitrine.search_min_surface') ?></label>
                            <input type="number" name="min_surface" class="form-control form-control-sm"
                                   placeholder="0 m²" value="<?= $filters['min_surface'] ?: '' ?>">
                        </div>

                        <!-- Tri -->
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Trier par</label>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="recent"     <?= $filters['sort'] === 'recent'     ? 'selected' : '' ?>><?= lang('Vitrine.filter_sort_recent') ?></option>
                                <option value="price_asc"  <?= $filters['sort'] === 'price_asc'  ? 'selected' : '' ?>><?= lang('Vitrine.filter_sort_price_asc') ?></option>
                                <option value="price_desc" <?= $filters['sort'] === 'price_desc' ? 'selected' : '' ?>><?= lang('Vitrine.filter_sort_price_desc') ?></option>
                                <option value="surface"    <?= $filters['sort'] === 'surface'    ? 'selected' : '' ?>><?= lang('Vitrine.filter_sort_surface') ?></option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn rb-btn-primary btn-sm">
                                <i class="bi bi-search me-1"></i><?= lang('Vitrine.filter_btn') ?>
                            </button>
                            <a href="<?= base_url($currentLang . '/properties') ?>" class="btn btn-outline-secondary btn-sm">
                                <i class="bi bi-x-circle me-1"></i><?= lang('Vitrine.filter_reset') ?>
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Résultats -->
            <div class="col-lg-9">
                <!-- Barre résultats -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">
                        <?= str_replace('{count}', $total, lang('Vitrine.results_count')) ?>
                    </span>
                </div>

                <?php if (empty($properties)): ?>
                <div class="rb-no-results">
                    <i class="bi bi-house-slash"></i>
                    <h5><?= lang('Vitrine.no_results') ?></h5>
                    <a href="<?= base_url($currentLang . '/properties') ?>" class="btn rb-btn-primary btn-sm mt-2">
                        <?= lang('Vitrine.filter_reset') ?>
                    </a>
                </div>
                <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($properties as $p): ?>
                    <?= view('vitrine/partials/property_card', ['p' => $p, 'currentLang' => $currentLang]) ?>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination simple -->
                <?php if ($total > $perPage): ?>
                <nav class="mt-4 d-flex justify-content-center">
                    <ul class="pagination rb-pagination">
                        <?php
                        $totalPages = (int)ceil($total / $perPage);
                        for ($page = 1; $page <= $totalPages; $page++):
                            $params        = array_merge($filters, ['page' => $page]);
                            $queryString   = http_build_query(array_filter($params, fn($v) => $v !== '' && $v !== 0));
                        ?>
                        <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= base_url($currentLang . '/properties?' . $queryString) ?>"><?= $page ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?= $this->endSection() ?>
