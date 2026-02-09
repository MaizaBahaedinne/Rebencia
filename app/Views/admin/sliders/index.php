<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-images"></i> <?= esc($page_title) ?>
        </h1>
        <a href="<?= base_url('admin/sliders/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Slider
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Sliders</h6>
        </div>
        <div class="card-body">
            <?php if (empty($sliders)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun slider pour le moment</p>
                    <a href="<?= base_url('admin/sliders/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer votre premier slider
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th width="80">Aperçu</th>
                                <th>Titre</th>
                                <th>Position</th>
                                <th width="100">Ordre</th>
                                <th width="100">Statut</th>
                                <th width="150">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sliders as $slider): ?>
                            <tr>
                                <td>
                                    <?php if ($slider['image']): ?>
                                        <img src="<?= base_url('uploads/sliders/' . $slider['image']) ?>" 
                                             alt="<?= esc($slider['title']) ?>" 
                                             class="img-thumbnail" 
                                             style="width: 80px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 80px; height: 50px;">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($slider['title']) ?></strong>
                                    <?php if ($slider['subtitle']): ?>
                                        <br><small class="text-muted"><?= esc($slider['subtitle']) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?= ucfirst($slider['text_position']) ?></span>
                                </td>
                                <td class="text-center"><?= $slider['display_order'] ?></td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               <?= $slider['is_active'] ? 'checked' : '' ?>
                                               onchange="toggleStatus(<?= $slider['id'] ?>, this)">
                                    </div>
                                </td>
                                <td>
                                    <a href="<?= base_url('admin/sliders/edit/' . $slider['id']) ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="deleteSlider(<?= $slider['id'] ?>)" 
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleStatus(id, checkbox) {
    fetch('<?= base_url('admin/sliders/toggle-status') ?>/' + id, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            checkbox.checked = !checkbox.checked;
            alert('Erreur lors de la modification du statut');
        }
    });
}

function deleteSlider(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce slider ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('admin/sliders/delete') ?>/' + id;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<?= $this->endSection() ?>
