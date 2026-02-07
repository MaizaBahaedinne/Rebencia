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
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 0.5rem;
        transition: all 0.2s;
        position: relative;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 220px;
    }
    .agency-item:hover {
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
        transform: translateY(-2px);
    }
    .agency-item.siege {
        border-left: 4px solid #3b82f6;
        background: linear-gradient(135deg, #fff 0%, #f8faff 100%);
    }
    .agency-item.agence {
        border-left: 4px solid #10b981;
    }
    .agency-item.hidden {
        display: none;
    }
    .agency-logo {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .agency-logo-placeholder {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        background: linear-gradient(135deg, #e5e7eb 0%, #cbd5e1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: #94a3b8;
        border: 2px solid #e5e7eb;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .agency-title {
        flex: 1;
        min-width: 0;
    }
    .agency-title h5 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 600;
        color: #1f2937;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .agency-code {
        font-size: 0.7rem;
        color: #6b7280;
        font-weight: 500;
        margin-top: 2px;
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
    
    /* Modal Styles */
    .agency-modal .modal-dialog {
        max-width: 700px;
    }
    .agency-modal .modal-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-bottom: none;
    }
    .agency-modal .modal-header .btn-close {
        filter: invert(1);
    }
    .modal-agency-logo {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .modal-agency-logo-placeholder {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        color: #3b82f6;
        border: 3px solid white;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .modal-stat-card {
        background: #f9fafb;
        padding: 1rem;
        border-radius: 8px;
        border-left: 4px solid #e5e7eb;
        text-align: center;
    }
    .modal-stat-card.users { border-left-color: #3b82f6; }
    .modal-stat-card.properties { border-left-color: #10b981; }
    .modal-stat-card.transactions { border-left-color: #f59e0b; }
    .modal-stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        line-height: 1;
    }
    .modal-stat-card .stat-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin: 0.5rem 0 0 0;
        font-weight: 500;
    }
    .info-row {
        display: flex;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-label {
        font-weight: 600;
        color: #6b7280;
        width: 140px;
        flex-shrink: 0;
    }
    .info-value {
        color: #1f2937;
        flex: 1;
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
                <div class="agency-item <?= esc($agency['type']) ?>" 
                     data-agency-id="<?= $agency['id'] ?>"
                     data-agency-name="<?= esc($agency['name']) ?>"
                     data-agency-code="<?= esc($agency['code']) ?>"
                     data-agency-type="<?= esc($agency['type']) ?>"
                     data-agency-status="<?= esc($agency['status']) ?>"
                     data-agency-city="<?= esc($agency['city']) ?>"
                     data-agency-governorate="<?= esc($agency['governorate']) ?>"
                     data-agency-address="<?= esc($agency['address'] ?? '') ?>"
                     data-agency-phone="<?= esc($agency['phone'] ?? '') ?>"
                     data-agency-email="<?= esc($agency['email'] ?? '') ?>"
                     data-agency-users="<?= $agency['users_count'] ?? 0 ?>"
                     data-agency-properties="<?= $agency['properties_count'] ?? 0 ?>"
                     data-agency-transactions="<?= $agency['transactions_count'] ?? 0 ?>"
                     data-agency-logo="<?= !empty($agency['logo']) ? base_url('uploads/agencies/' . $agency['logo']) : '' ?>"
                     onclick="showAgencyModal(this)">
                    <?php if ($hasChildren): ?>
                        <div class="toggle-children" onclick="event.stopPropagation(); toggleChildren(this)">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    <?php endif; ?>
                    
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
                        <div class="agency-code"><?= esc($agency['code']) ?></div>
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

    <div id="noResults" class="no-results" style="display: none;">
        <i class="fas fa-search fa-3x mb-3"></i>
        <h5>Aucune agence trouvée</h5>
        <p>Essayez de modifier vos critères de recherche</p>
    </div>
</div>

<!-- Modal pour afficher les détails de l'agence -->
<div class="modal fade agency-modal" id="agencyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div id="modalLogo"></div>
                    <div>
                        <h5 class="modal-title mb-1" id="modalName"></h5>
                        <small id="modalCode"></small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Statistiques -->
                <div class="row mb-4">
                    <div class="col-4">
                        <div class="modal-stat-card users">
                            <p class="stat-value text-primary" id="modalUsers">0</p>
                            <p class="stat-label">Utilisateurs</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="modal-stat-card properties">
                            <p class="stat-value text-success" id="modalProperties">0</p>
                            <p class="stat-label">Biens</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="modal-stat-card transactions">
                            <p class="stat-value text-warning" id="modalTransactions">0</p>
                            <p class="stat-label">Transactions</p>
                        </div>
                    </div>
                </div>

                <!-- Informations -->
                <h6 class="mb-3 fw-bold">Informations</h6>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-tag me-2"></i>Type</div>
                    <div class="info-value" id="modalType"></div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-circle me-2"></i>Statut</div>
                    <div class="info-value" id="modalStatus"></div>
                </div>
                <div class="info-row">
                    <div class="info-label"><i class="fas fa-map-marker-alt me-2"></i>Localisation</div>
                    <div class="info-value" id="modalLocation"></div>
                </div>
                <div class="info-row" id="modalAddressRow" style="display: none;">
                    <div class="info-label"><i class="fas fa-home me-2"></i>Adresse</div>
                    <div class="info-value" id="modalAddress"></div>
                </div>
                <div class="info-row" id="modalPhoneRow" style="display: none;">
                    <div class="info-label"><i class="fas fa-phone me-2"></i>Téléphone</div>
                    <div class="info-value" id="modalPhone"></div>
                </div>
                <div class="info-row" id="modalEmailRow" style="display: none;">
                    <div class="info-label"><i class="fas fa-envelope me-2"></i>Email</div>
                    <div class="info-value" id="modalEmail"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Fermer
                </button>
                <a href="#" id="modalViewLink" class="btn btn-info">
                    <i class="fas fa-eye me-2"></i>Plus d'infos
                </a>
                <a href="#" id="modalEditLink" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function showAgencyModal(element) {
    const id = element.dataset.agencyId;
    const name = element.dataset.agencyName;
    const code = element.dataset.agencyCode;
    const type = element.dataset.agencyType;
    const status = element.dataset.agencyStatus;
    const city = element.dataset.agencyCity;
    const governorate = element.dataset.agencyGovernorate;
    const address = element.dataset.agencyAddress;
    const phone = element.dataset.agencyPhone;
    const email = element.dataset.agencyEmail;
    const users = element.dataset.agencyUsers;
    const properties = element.dataset.agencyProperties;
    const transactions = element.dataset.agencyTransactions;
    const logo = element.dataset.agencyLogo;

    // Logo
    if (logo) {
        document.getElementById('modalLogo').innerHTML = 
            `<img src="${logo}" alt="Logo" class="modal-agency-logo">`;
    } else {
        document.getElementById('modalLogo').innerHTML = 
            `<div class="modal-agency-logo-placeholder"><i class="fas fa-building"></i></div>`;
    }

    // Informations de base
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalCode').textContent = 'Code: ' + code;
    
    // Statistiques
    document.getElementById('modalUsers').textContent = users;
    document.getElementById('modalProperties').textContent = properties;
    document.getElementById('modalTransactions').textContent = transactions;

    // Type
    document.getElementById('modalType').innerHTML = 
        type === 'siege' 
        ? '<span class="badge bg-primary">Siège</span>' 
        : '<span class="badge bg-success">Agence</span>';

    // Statut
    document.getElementById('modalStatus').innerHTML = 
        status === 'active' 
        ? '<span class="badge bg-success">Actif</span>' 
        : '<span class="badge bg-danger">Inactif</span>';

    // Localisation
    document.getElementById('modalLocation').textContent = `${city}, ${governorate}`;

    // Adresse
    if (address) {
        document.getElementById('modalAddress').textContent = address;
        document.getElementById('modalAddressRow').style.display = 'flex';
    } else {
        document.getElementById('modalAddressRow').style.display = 'none';
    }

    // Téléphone
    if (phone) {
        document.getElementById('modalPhone').textContent = phone;
        document.getElementById('modalPhoneRow').style.display = 'flex';
    } else {
        document.getElementById('modalPhoneRow').style.display = 'none';
    }

    // Email
    if (email) {
        document.getElementById('modalEmail').textContent = email;
        document.getElementById('modalEmailRow').style.display = 'flex';
    } else {
        document.getElementById('modalEmailRow').style.display = 'none';
    }

    // Liens d'action
    document.getElementById('modalViewLink').href = '<?= base_url('admin/agencies/view/') ?>' + id;
    document.getElementById('modalEditLink').href = '<?= base_url('admin/agencies/edit/') ?>' + id;

    // Ouvrir le modal
    const modal = new bootstrap.Modal(document.getElementById('agencyModal'));
    modal.show();
}

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

function applyFilters() {
    const searchName = document.getElementById('searchName').value.toLowerCase();
    const filterType = document.getElementById('filterType').value.toLowerCase();
    const filterCity = document.getElementById('filterCity').value.toLowerCase();
    const filterStatus = document.getElementById('filterStatus').value.toLowerCase();
    
    let visibleCount = 0;
    
    // Parcourir toutes les agences
    document.querySelectorAll('.agency-item').forEach(item => {
        const name = item.dataset.agencyName.toLowerCase();
        const code = item.dataset.agencyCode.toLowerCase();
        const type = item.dataset.agencyType;
        const city = item.dataset.agencyCity.toLowerCase();
        const status = item.dataset.agencyStatus;
        
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

function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette agence ?')) {
        window.location.href = '<?= base_url('admin/agencies/delete/') ?>' + id;
    }
}
</script>
<?= $this->endSection() ?>
