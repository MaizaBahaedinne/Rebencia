<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/hierarchy') ?>">Hiérarchie</a></li>
        <li class="breadcrumb-item active">Assigner un manager</li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-user-tie text-primary"></i> Assigner un manager
    </h1>
    <a href="<?= base_url('admin/hierarchy') ?>" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Formulaire d'assignation</h5>
            </div>
            <div class="card-body">
                <form id="assignManagerForm" method="POST">
                    <?= csrf_field() ?>
                    
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Utilisateur <span class="text-danger">*</span></label>
                        <select class="form-select" id="user_id" name="user_id" required>
                            <option value="">-- Sélectionner un utilisateur --</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?= $user['id'] ?>" <?= isset($_GET['user']) && $_GET['user'] == $user['id'] ? 'selected' : '' ?>>
                                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> (<?= esc($user['email']) ?>)
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="manager_id" class="form-label">Manager <span class="text-danger">*</span></label>
                        <select class="form-select" id="manager_id" name="manager_id" required>
                            <option value="">-- Sélectionner un manager --</option>
                            <?php foreach ($managers as $manager): ?>
                                <option value="<?= $manager['id'] ?>">
                                    <?= esc($manager['first_name'] . ' ' . $manager['last_name']) ?> - Role ID: <?= esc($manager['role_id']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <div class="form-text">Le manager ne peut pas être un subordonné de l'utilisateur</div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/hierarchy') ?>" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Assigner
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-info-circle text-primary"></i> Information</h6>
                <p class="small text-muted mb-2">
                    L'assignation d'un manager est <strong>obligatoire</strong> pour tous les utilisateurs (sauf les administrateurs).
                </p>
                <p class="small text-muted mb-2">
                    Le manager aura accès à tous les biens et ressources gérés par cet utilisateur.
                </p>
                <p class="small text-muted mb-0">
                    La hiérarchie est récursive : un directeur de siège peut gérer tous les biens des agences sous sa responsabilité.
                </p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('assignManagerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const userId = formData.get('user_id');
    const managerId = formData.get('manager_id');
    
    if (userId === managerId) {
        alert('Un utilisateur ne peut pas être son propre manager');
        return;
    }
    
    fetch('<?= base_url('admin/hierarchy/assign-manager') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '<?= base_url('admin/hierarchy') ?>';
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
});
</script>
<?= $this->endSection() ?>