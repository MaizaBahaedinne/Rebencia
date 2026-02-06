<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<style>
    .filters-section {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .filters-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.75rem;
    }
    .agency-tree {
        list-style: none;
        padding-left: 0;
    }
    .agency-tree > li {
        margin-bottom: 0.5rem;
    }
    .agency-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
        position: relative;
    }
    .agency-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }
    .agency-item.siege {
        border-left: 3px solid #3b82f6;
        background: linear-gradient(135deg, #fff 0%, #f8faff 100%);
    }
    .agency-item.agence {
        border-left: 3px solid #10b981;
    }
    .agency-item.hidden {
        display: none;
    }
    .agency-header {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }
    .agency-logo {
        width: 45px;
        height: 45px;
        border-radius: 6px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }
    .agency-logo-placeholder {
        width: 45px;
        height: 45px;
        border-radius: 6px;
        background: linear-gradient(135deg, #e5e7eb 0%, #cbd5e1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #94a3b8;
    }
    .agency-title {
        flex: 1;
        min-width: 0;
    }
    .agency-title h5 {
        margin: 0;
        font-size: 1rem;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .agency-code {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 500;
    }
    .agency-stats {
        display: flex;
        gap: 0.5rem;
        margin: 0.5rem 0;
        flex-wrap: wrap;
    }
    .stat-card {
        background: #f9fafb;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        border-left: 2px solid #d1d5db;
        flex: 1;
        min-width: 80px;
    }
    .stat-card.users { border-left-color: #3b82f6; }
    .stat-card.properties { border-left-color: #10b981; }
    .stat-card.transactions { border-left-color: #f59e0b; }
    .stat-card .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0;
        line-height: 1.2;
    }
    .stat-card .stat-label {
        font-size: 0.7rem;
        color: #6b7280;
        margin: 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .agency-contact {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 0.5rem 0;
        font-size: 0.875rem;
    }
    .contact-item {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        color: #4b5563;
    }
    .contact-item i {
        color: #9ca3af;
        font-size: 0.75rem;
    }
    .agency-actions {
        display: flex;
        gap: 0.4rem;
        justify-content: flex-end;
        margin-top: 0.5rem;
    }
    .agency-actions .btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
    }
    .children-agencies {
        list-style: none;
        padding-left: 2.5rem;
        margin-top: 0.5rem;
        position: relative;
    }
    .children-agencies::before {
        content: '';
        position: absolute;
        left: 1.25rem;
        top: 0;
        bottom: 0;
        width: 1px;
        background: linear-gradient(180deg, #cbd5e1 0%, transparent 100%);
    }
    .children-agencies > li {
        position: relative;
        margin-bottom: 0.5rem;
    }
    .children-agencies > li::before {
        content: '';
        position: absolute;
        left: -1.25rem;
        top: 1.5rem;
        width: 1.25rem;
        height: 1px;
        background: #cbd5e1;
    }
    .toggle-children {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
        font-size: 0.75rem;
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
    .no-results {
        text-align: center;
        padding: 3rem;
        color: #6b7280;
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

    <!-- Filtres de recherche -->
    <div class="filters-section">
        <div class="filters-row">
            <div>
                <label class="form-label small mb-1">Recherche</label>
                <input type="text" id="searchName" class="form-control form-control-sm" 
                       placeholder="Nom ou code agence..." onkeyup="applyFilters()">
            </div>
            <div>
                <label class="form-label small mb-1">Type</label>
                <select id="filterType" class="form-select form-select-sm" onchange="applyFilters()">
                    <option value="">Tous les types</option>
                    <option value="siege">Siège</option>
                    <option value="agence">Agence</option>
                </select>
            </div>
            <div>
                <label class="form-label small mb-1">Ville</label>
                <input type="text" id="filterCity" class="form-control form-control-sm" 
                       placeholder="Filtrer par ville..." onkeyup="applyFilters()">
            </div>
            <div>
                <label class="form-label small mb-1">Statut</label>
                <select id="filterStatus" class="form-select form-select-sm" onchange="applyFilters()">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actif</option>
                    <option value="inactive">Inactif</option>
                </select>
            </div>
            <div class="d-flex align-items-end">
                <button class="btn btn-outline-secondary btn-sm w-100" onclick="resetFilters()">
                    <i class="fas fa-redo me-1"></i>Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <div id="agencyTreeContainer">
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
                                <span class="badge bg-primary">Siège</span>
                            <?php else: ?>
                                <span class="badge bg-success">Agence</span>
                            <?php endif; ?>
                            
                            <?php if ($agency['status'] === 'active'): ?>
                                <span class="badge bg-success ms-1">Actif</span>
                            <?php else: ?>
                                <span class="badge bg-danger ms-1">Inactif</span>
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
    </div>
    
    <div id="noResults" class="no-results" style="display: none;">
        <i class="fas fa-search fa-3x mb-3"></i>
        <h5>Aucune agence trouvée</h5>
        <p>Essayez de modifier vos critères de recherche</p>
    </div>
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

function applyFilters() {
    const searchName = document.getElementById('searchName').value.toLowerCase();
    const filterType = document.getElementById('filterType').value.toLowerCase();
    const filterCity = document.getElementById('filterCity').value.toLowerCase();
    const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
    
    let visibleCount = 0;
    
    // Parcourir toutes les agences
    document.querySelectorAll('.agency-item').forEach(item => {
        const name = item.querySelector('.agency-title h5').textContent.toLowerCase();
        const code = item.querySelector('.agency-code').textContent.toLowerCase();
        const type = item.classList.contains('siege') ? 'siege' : 'agence';
        const city = item.querySelector('.contact-item .fa-map-marker-alt')?.parentElement.textContent.toLowerCase() || '';
        
        // Trouver le statut
        const badges = item.querySelectorAll('.badge');
        let status = '';
        badges.forEach(badge => {
            if (badge.textContent.toLowerCase().includes('actif')) status = 'active';
            if (badge.textContent.toLowerCase().includes('inactif')) status = 'inactive';
        });
        
        // Appliquer les filtres
        const matchName = !searchName || name.includes(searchName) || code.includes(searchName);
        const matchType = !filterType || type === filterType;
        const matchCity = !filterCity || city.includes(filterCity);
        const matchStatus = !filterStatus || status === filterStatus;
        
        if (matchName && matchType && matchCity && matchStatus) {
            item.closest('li').style.display = '';
            visibleCount++;
        } else {
            item.closest('li').style.display = 'none';
        }
    });
    
    // Afficher le message si aucun résultat
    const noResults = document.getElementById('noResults');
    const treeContainer = document.getElementById('agencyTreeContainer');
    
    if (visibleCount === 0) {
        treeContainer.style.display = 'none';
        noResults.style.display = 'block';
    } else {
        treeContainer.style.display = 'block';
        noResults.style.display = 'none';
    }
}

function resetFilters() {
    document.getElementById('searchName').value = '';
    document.getElementById('filterType').value = '';
    document.getElementById('filterCity').value = '';
    document.getElementById('filterStatus').value = '';
    applyFilters();
}
}

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette agence ?')) {
        window.location.href = '<?= base_url('admin/agencies/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
