<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-bars me-2"></i>Gestion des Menus
        </h1>
        <div>
            <a href="<?= base_url('admin/menus/role-menus') ?>" class="btn btn-info me-2">
                <i class="fas fa-user-tag me-2"></i>Menus par Rôle
            </a>
            <a href="<?= base_url('admin/menus/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Menu
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($menus)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-bars fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun menu disponible</p>
                    <a href="<?= base_url('admin/menus/create') ?>" class="btn btn-primary">
                        Créer le premier menu
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Icône</th>
                                <th>URL</th>
                                <th>Parent</th>
                                <th>Ordre</th>
                                <th>Statut</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php renderMenuTable($menus); ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?php
function renderMenuTable($menus, $level = 0) {
    foreach ($menus as $menu) {
        $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $level);
        echo '<tr>';
        echo '<td>' . $indent;
        if ($level > 0) echo '<i class="fas fa-level-up-alt fa-rotate-90 text-muted me-2"></i>';
        echo esc($menu['title']) . '</td>';
        echo '<td>';
        if ($menu['icon']) {
            echo '<i class="' . esc($menu['icon']) . ' me-2"></i>' . esc($menu['icon']);
        } else {
            echo '<span class="text-muted">-</span>';
        }
        echo '</td>';
        echo '<td><code>' . ($menu['url'] ? esc($menu['url']) : '-') . '</code></td>';
        echo '<td>' . ($menu['parent_id'] ? 'Sous-menu' : 'Menu principal') . '</td>';
        echo '<td><span class="badge bg-secondary">' . $menu['order'] . '</span></td>';
        echo '<td>';
        if ($menu['is_active']) {
            echo '<span class="badge bg-success">Actif</span>';
        } else {
            echo '<span class="badge bg-danger">Inactif</span>';
        }
        echo '</td>';
        echo '<td class="text-end">';
        echo '<a href="' . base_url('admin/menus/edit/' . $menu['id']) . '" class="btn btn-sm btn-outline-primary me-1">';
        echo '<i class="fas fa-edit"></i>';
        echo '</a>';
        echo '<a href="' . base_url('admin/menus/delete/' . $menu['id']) . '" class="btn btn-sm btn-outline-danger" onclick="return confirm(\'Supprimer ce menu ?\')">';
        echo '<i class="fas fa-trash"></i>';
        echo '</a>';
        echo '</td>';
        echo '</tr>';
        
        if (isset($menu['children']) && !empty($menu['children'])) {
            renderMenuTable($menu['children'], $level + 1);
        }
    }
}
?>
