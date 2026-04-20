<div class="d-flex align-items-center justify-content-between mb-4">
    <h4 class="mb-0"><i class="bi bi-bell me-2"></i>Notifications</h4>
    <?php if (!empty($rows)) : ?>
    <form method="post" action="<?= base_url('admin/notifications/read-all') ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-check2-all me-1"></i>Tout marquer comme lu
        </button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($rows)) : ?>
<div class="card shadow-sm">
    <div class="card-body text-center py-5 text-muted">
        <i class="bi bi-bell-slash display-4 d-block mb-3 opacity-50"></i>
        <p class="mb-0">Aucune notification pour le moment.</p>
    </div>
</div>
<?php else : ?>

<div class="card shadow-sm">
    <div class="list-group list-group-flush">
        <?php foreach ($rows as $notif) :
            $t = $types[$notif['type']] ?? $types['info'];
        ?>
        <div class="list-group-item list-group-item-action d-flex gap-3 py-3 <?= $notif['is_read'] ? '' : 'fw-semibold' ?>"
             style="<?= $notif['is_read'] ? '' : 'background:rgba(26,60,94,.04);' ?>">

            <!-- Icône type -->
            <div class="flex-shrink-0 pt-1">
                <i class="bi <?= esc($t['icon']) ?> fs-5 <?= esc($t['color']) ?>"></i>
            </div>

            <!-- Contenu -->
            <div class="flex-grow-1 overflow-hidden">
                <div class="d-flex justify-content-between">
                    <span class="text-truncate"><?= esc($notif['title']) ?></span>
                    <small class="text-muted text-nowrap ms-2">
                        <?= date('d/m H:i', strtotime($notif['created_at'])) ?>
                    </small>
                </div>
                <p class="mb-1 text-muted fw-normal" style="font-size:.85rem;">
                    <?= esc($notif['message']) ?>
                </p>
                <?php if ($notif['url']) : ?>
                <a href="<?= base_url(ltrim($notif['url'], '/')) ?>" class="small">Voir &rarr;</a>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="flex-shrink-0 d-flex flex-column align-items-end gap-1">
                <?php if (!$notif['is_read']) : ?>
                <form method="post" action="<?= base_url('admin/notifications/' . $notif['id'] . '/read') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-primary py-0 px-1" title="Marquer comme lu">
                        <i class="bi bi-check2"></i>
                    </button>
                </form>
                <?php endif; ?>
                <form method="post" action="<?= base_url('admin/notifications/' . $notif['id'] . '/delete') ?>"
                      onsubmit="return confirm('Supprimer cette notification ?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-1" title="Supprimer">
                        <i class="bi bi-trash3"></i>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($pager) : ?>
<div class="mt-3 d-flex justify-content-center">
    <?= $pager->links() ?>
</div>
<?php endif; ?>

<?php endif; ?>
