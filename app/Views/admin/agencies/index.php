<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<style>
    .agency-tree {
        list-style: none;
        padding-left: 0;
    }
    .agency-tree > li {
        margin-bottom: 1rem;
    }
    .agency-item {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 0.5rem;
        transition: all 0.3s;
        position: relative;
    }
    .agency-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
    }
    .agency-item.siege {
        border-left: 5px solid #3b82f6;
        background: linear-gradient(135deg, #fff 0%, #f0f7ff 100%);
    }
    .agency-item.agence {
        border-left: 5px solid #10b981;
    }
    .agency-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .agency-logo {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
    }
    .agency-logo-placeholder {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        background: linear-gradient(135deg, #e5e7eb 0%, #cbd5e1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #94a3b8;
    }
    .agency-title {
        flex: 1;
    }
    .agency-title h5 {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 600;
    }
    .agency-code {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    .agency-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin: 1rem 0;
    }
    .stat-card {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 8px;
        border-left: 3px solid #d1d5db;
    }
    .stat-card.users { border-left-color: #3b82f6; }
    .stat-card.properties { border-left-color: #10b981; }
    .stat-card.transactions { border-left-color: #f59e0b; }
    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }
    .stat-card .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
    .agency-contact {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin: 1rem 0;
    }
    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #4b5563;
    }
    .contact-item i {
        color: #9ca3af;
    }
    .agency-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
    }
    .children-agencies {
        list-style: none;
        padding-left: 3rem;
        margin-top: 1rem;
        position: relative;
    }
    .children-agencies::before {
        content: '';
        position: absolute;
        left: 1.5rem;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(180deg, #cbd5e1 0%, transparent 100%);
    }
    .children-agencies > li {
        position: relative;
        margin-bottom: 1rem;
    }
    .children-agencies > li::before {
        content: '';
        position: absolute;
        left: -1.5rem;
        top: 2.5rem;
        width: 1.5rem;
        height: 2px;
        background: #cbd5e1;
    }
    .toggle-children {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
        z-index: 10;
    }
    .toggle-children:hover {
        border-color: #3b82f6;
        background: #f0f7ff;
    }
    .toggle-children i {
        transition: transform 0.3s;
    }
    .toggle-children.active i {
        transform: rotate(180deg);
    }
    .children-agencies.collapsed {
        display: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-sitemap me-2"></i>Hiérarchie des Agences
        </h1>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="expandAll()">
                <i class="fas fa-expand-alt me-2"></i>Tout Développer
            </button>
            <button class="btn btn-outline-secondary me-2" onclick="collapseAll()">
                <i class="fas fa-compress-alt me-2"></i>Tout Réduire
            </button>
            <a href="<?= base_url('admin/agencies/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Agence
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

    <?php
    // Function to render agency tree recursively
    function renderAgencyTree($agencies, $level = 0) {
        if (empty($agencies)) return;
        
        echo '<ul class="agency-tree' . ($level > 0 ? ' children-agencies' : '') . '">';
        
        foreach ($agencies as $agency) {
            $hasChildren = !empty($agency['children']);
            ?>
            <li>
                <div class="agency-item <?= esc($agency['type']) ?>" data-agency-id="<?= $agency['id'] ?>">
                    <?php if ($hasChildren): ?>
                        <div class="toggle-children" onclick="toggleChildren(this)">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="agency-header">
                        <?php if (!empty($agency['logo'])): ?>
                            <img src="<?= base_url('uploads/agencies/' . $agency['logo']) ?>" 
                                 alt="Logo" class="agency-logo">
                        <?php else: ?>
                            <div class="agency-logo-placeholder">
                                <i class="fas fa-building"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div class="agency-title">
                            <h5><?= esc($agency['name']) ?></h5>
                            <div class="agency-code">Code: <?= esc($agency['code']) ?></div>
                        </div>
                        
                        <div>
                            <?php if ($agency['type'] === 'siege'): ?>
                                <span class="badge bg-primary fs-6">Siège</span>
                            <?php else: ?>
                                <span class="badge bg-success fs-6">Agence</span>
                            <?php endif; ?>
                            
                            <?php if ($agency['status'] === 'active'): ?>
                                <span class="badge bg-success fs-6 ms-2">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger fs-6 ms-2">Inactif</span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="agency-stats">
                        <div class="stat-card users">
                            <p class="stat-value text-primary"><?= $agency['users_count'] ?? 0 ?></p>
                            <p class="stat-label">Utilisateurs</p>
                        </div>
                        <div class="stat-card properties">
                            <p class="stat-value text-success"><?= $agency['properties_count'] ?? 0 ?></p>
                            <p class="stat-label">Biens</p>
                        </div>
                        <div class="stat-card transactions">
                            <p class="stat-value text-warning"><?= $agency['transactions_count'] ?? 0 ?></p>
                            <p class="stat-label">Transactions</p>
                        </div>
                    </div>
                    
                    <div class="agency-contact">
                        <div class="contact-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?= esc($agency['city']) ?>, <?= esc($agency['governorate']) ?></span>
                        </div>
                        <?php if (!empty($agency['phone'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <span><?= esc($agency['phone']) ?></span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($agency['email'])): ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <span><?= esc($agency['email']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="agency-actions">
                        <a href="<?= base_url('admin/agencies/view/' . $agency['id']) ?>" 
                           class="btn btn-info btn-sm">
                            <i class="fas fa-eye me-1"></i>Détails
                        </a>
                        <a href="<?= base_url('admin/agencies/edit/' . $agency['id']) ?>" 
                           class="btn btn-warning btn-sm">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                        <?php if ($agency['users_count'] == 0): ?>
                            <button onclick="confirmDelete(<?= $agency['id'] ?>)" 
                                    class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Supprimer
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($hasChildren): ?>
                    <?php renderAgencyTree($agency['children'], $level + 1); ?>
                <?php endif; ?>
            </li>
            <?php
        }
        
        echo '</ul>';
    }
    
    // Render the tree
    renderAgencyTree($agencies);
    ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleChildren(btn) {
    const agencyItem = btn.closest('.agency-item');
    const childrenList = agencyItem.nextElementSibling;
    
    if (childrenList && childrenList.classList.contains('children-agencies')) {
        childrenList.classList.toggle('collapsed');
        btn.classList.toggle('active');
    }
}

function expandAll() {
    document.querySelectorAll('.children-agencies').forEach(ul => {
        ul.classList.remove('collapsed');
    });
    document.querySelectorAll('.toggle-children').forEach(btn => {
        btn.classList.add('active');
    });
}

function collapseAll() {
    document.querySelectorAll('.children-agencies').forEach(ul => {
        ul.classList.add('collapsed');
    });
    document.querySelectorAll('.toggle-children').forEach(btn => {
        btn.classList.remove('active');
    });
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette agence ?')) {
        window.location.href = '<?= base_url('admin/agencies/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
