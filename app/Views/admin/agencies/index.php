<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-building me-2"></i>Gestion des Agences
        </h1>
        <div>
            <button class="btn btn-outline-secondary me-2" onclick="expandAll()">
                <i class="fas fa-chevron-down me-1"></i>Développer tout
            </button>
            <button class="btn btn-outline-secondary me-2" onclick="collapseAll()">
                <i class="fas fa-chevron-right me-1"></i>Réduire tout
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

    <!-- Liste en mode détails (comme Windows Explorer) -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 agency-tree-table">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 35%;">
                                <i class="fas fa-folder me-2"></i>Nom
                            </th>
                            <th style="width: 15%;">
                                <i class="fas fa-map-marker-alt me-2"></i>Ville
                            </th>
                            <th style="width: 10%;">
                                <i class="fas fa-phone me-2"></i>Téléphone
                            </th>
                            <th style="width: 10%;" class="text-center">
                                <i class="fas fa-users me-2"></i>Agents
                            </th>
                            <th style="width: 10%;" class="text-center">
                                <i class="fas fa-home me-2"></i>Biens
                            </th>
                            <th style="width: 10%;" class="text-center">
                                <i class="fas fa-check-circle me-2"></i>Statut
                            </th>
                            <th style="width: 10%;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        function renderAgencyRow($agency, $level = 0, $parentPath = '') {
                            $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                            $hasChildren = !empty($agency['children']);
                            $agencyPath = $parentPath . '_' . $agency['id'];
                            
                            // Status badge
                            $statusBadge = '';
                            switch($agency['status']) {
                                case 'active':
                                    $statusBadge = '<span class="badge bg-success">Active</span>';
                                    break;
                                case 'suspended':
                                    $statusBadge = '<span class="badge bg-warning">Suspendue</span>';
                                    break;
                                case 'archived':
                                    $statusBadge = '<span class="badge bg-secondary">Archivée</span>';
                                    break;
                            }
                            
                            // Icon based on type
                            $icon = $agency['type'] === 'siege' ? 'fa-building' : 'fa-store';
                            $iconColor = $agency['type'] === 'siege' ? 'text-primary' : 'text-info';
                            
                            echo '<tr class="agency-row" data-agency-id="' . $agency['id'] . '" data-level="' . $level . '" data-path="' . $agencyPath . '">';
                            
                            // Nom avec indentation et toggle
                            echo '<td class="agency-name-cell">';
                            echo $indent;
                            if ($hasChildren) {
                                echo '<span class="toggle-icon me-2" onclick="toggleAgency(\'' . $agencyPath . '\')" style="cursor: pointer;">';
                                echo '<i class="fas fa-chevron-right"></i>';
                                echo '</span>';
                            } else {
                                echo '<span class="ms-3"></span>';
                            }
                            echo '<i class="fas ' . $icon . ' ' . $iconColor . ' me-2"></i>';
                            echo '<strong>' . esc($agency['name']) . '</strong>';
                            if ($agency['code']) {
                                echo ' <small class="text-muted">(' . esc($agency['code']) . ')</small>';
                            }
                            echo '</td>';
                            
                            // Ville
                            echo '<td>' . esc($agency['city'] ?? '-') . '</td>';
                            
                            // Téléphone
                            echo '<td>';
                            if ($agency['phone']) {
                                echo '<a href="tel:' . esc($agency['phone']) . '" class="text-decoration-none">';
                                echo esc($agency['phone']);
                                echo '</a>';
                            } else {
                                echo '-';
                            }
                            echo '</td>';
                            
                            // Agents
                            echo '<td class="text-center">';
                            echo '<span class="badge bg-primary rounded-pill">' . ($agency['agents_count'] ?? 0) . '</span>';
                            echo '</td>';
                            
                            // Biens
                            echo '<td class="text-center">';
                            echo '<span class="badge bg-info rounded-pill">' . ($agency['properties_count'] ?? 0) . '</span>';
                            echo '</td>';
                            
                            // Statut
                            echo '<td class="text-center">' . $statusBadge . '</td>';
                            
                            // Actions
                            echo '<td class="text-center">';
                            echo '<div class="btn-group btn-group-sm" role="group">';
                            echo '<a href="' . base_url('admin/agencies/view/' . $agency['id']) . '" class="btn btn-outline-primary" title="Voir">';
                            echo '<i class="fas fa-eye"></i>';
                            echo '</a>';
                            echo '<a href="' . base_url('admin/agencies/edit/' . $agency['id']) . '" class="btn btn-outline-warning" title="Modifier">';
                            echo '<i class="fas fa-edit"></i>';
                            echo '</a>';
                            echo '<a href="' . base_url('admin/agencies/delete/' . $agency['id']) . '" class="btn btn-outline-danger" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer cette agence ?\')" title="Supprimer">';
                            echo '<i class="fas fa-trash"></i>';
                            echo '</a>';
                            echo '</div>';
                            echo '</td>';
                            
                            echo '</tr>';
                            
                            // Render children
                            if ($hasChildren) {
                                foreach ($agency['children'] as $child) {
                                    renderAgencyRow($child, $level + 1, $agencyPath);
                                }
                            }
                        }
                        
                        // Render all root agencies
                        foreach ($agencies as $agency) {
                            renderAgencyRow($agency);
                        }
                        ?>
                        <?php if (empty($agencies)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-building fa-3x mb-3 opacity-50"></i>
                                <p>Aucune agence trouvée</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .agency-tree-table {
        font-size: 0.95rem;
    }
    
    .agency-tree-table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        padding: 12px 8px;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .agency-tree-table tbody tr {
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s ease;
    }
    
    .agency-tree-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .agency-tree-table tbody td {
        padding: 12px 8px;
        vertical-align: middle;
    }
    
    .agency-name-cell {
        font-weight: 500;
    }
    
    .toggle-icon {
        display: inline-block;
        width: 20px;
        text-align: center;
        transition: transform 0.2s ease;
    }
    
    .toggle-icon.expanded i {
        transform: rotate(90deg);
        display: inline-block;
    }
    
    .agency-row.collapsed {
        display: none;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Expand/Collapse functionality
    function toggleAgency(path) {
        const toggleIcon = document.querySelector(`[data-path="${path}"] .toggle-icon`);
        const childRows = document.querySelectorAll(`[data-path^="${path}_"]`);
        
        if (toggleIcon.classList.contains('expanded')) {
            // Collapse
            toggleIcon.classList.remove('expanded');
            childRows.forEach(row => {
                row.classList.add('collapsed');
                // Also collapse nested children
                const nestedToggle = row.querySelector('.toggle-icon');
                if (nestedToggle) {
                    nestedToggle.classList.remove('expanded');
                }
            });
        } else {
            // Expand only direct children
            toggleIcon.classList.add('expanded');
            const directChildLevel = parseInt(document.querySelector(`[data-path="${path}"]`).dataset.level) + 1;
            childRows.forEach(row => {
                if (parseInt(row.dataset.level) === directChildLevel) {
                    row.classList.remove('collapsed');
                }
            });
        }
    }
    
    function expandAll() {
        document.querySelectorAll('.toggle-icon').forEach(icon => {
            icon.classList.add('expanded');
        });
        document.querySelectorAll('.agency-row').forEach(row => {
            row.classList.remove('collapsed');
        });
    }
    
    function collapseAll() {
        document.querySelectorAll('.toggle-icon').forEach(icon => {
            icon.classList.remove('expanded');
        });
        document.querySelectorAll('.agency-row').forEach((row, index) => {
            if (parseInt(row.dataset.level) > 0) {
                row.classList.add('collapsed');
            }
        });
    }
</script>
<?= $this->endSection() ?>
        flex-direction: column;
        align-items: center;
        position: relative;
        width: 100%;
    }
    
    .agencies-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 2rem;
        position: relative;
        padding: 1rem 0;
    }
    
    /* Connection Lines */
    .agencies-row::before {
        content: '';
        position: absolute;
        top: 0;
        left: 10%;
        right: 10%;
        height: 2px;
        background: linear-gradient(to right, transparent, #6264a7 20%, #6264a7 80%, transparent);
        z-index: 0;
    }
    
    .org-level::before {
        content: '';
        position: absolute;
        top: -1.5rem;
        left: 50%;
        width: 2px;
        height: 1.5rem;
        background: #6264a7;
        transform: translateX(-50%);
    }
    
    .org-level:first-child::before {
        display: none;
    }
    
    /* Agency Box */
    .agency-box {
        background: white;
        border: 3px solid #e1e8ed;
        border-radius: 12px;
        padding: 1.5rem;
        width: 280px;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        z-index: 1;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .agency-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        border-color: #6264a7;
    }
    
    .agency-box.siege {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }
    
    .agency-box.agence {
        border-color: #10b981;
    }
    
    /* Agency Logo */
    .agency-logo-wrapper {
        margin-bottom: 1rem;
    }
    
    .agency-logo {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #e1e8ed;
        margin: 0 auto;
    }
    
    .agency-logo-placeholder {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background: #6264a7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        border: 3px solid #6264a7;
        margin: 0 auto;
    }
    
    .agency-box.siege .agency-logo-placeholder {
        background: white;
        color: #667eea;
        border-color: white;
    }
    
    .agency-box.agence .agency-logo-placeholder {
        background: #10b981;
        border-color: #10b981;
    }
    
    /* Agency Info */
    .agency-name {
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0.5rem 0;
        color: #1e293b;
        font-family: 'Segoe UI', Tahoma, sans-serif;
    }
    
    .agency-box.siege .agency-name {
        color: white;
    }
    
    .agency-code {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 1rem;
    }
    
    .agency-box.siege .agency-code {
        color: rgba(255, 255, 255, 0.9);
    }
    
    .agency-location {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
    }
    
    .agency-box.siege .agency-location {
        color: rgba(255, 255, 255, 0.85);
    }
    
    /* Agency Stats */
    .agency-stats {
        display: flex;
        justify-content: space-around;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 2px solid #e1e8ed;
        margin-top: 1rem;
    }
    
    .agency-box.siege .agency-stats {
        border-top-color: rgba(255, 255, 255, 0.3);
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: #6264a7;
        display: block;
    }
    
    .agency-box.siege .stat-value {
        color: white;
    }
    
    .agency-box.agence .stat-value {
        color: #10b981;
    }
    
    .stat-label {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.2rem;
    }
    
    .agency-box.siege .stat-label {
        color: rgba(255, 255, 255, 0.8);
    }
    
    /* Toggle Button */
    .toggle-btn {
        position: absolute;
        bottom: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: white;
        border: 2px solid #6264a7;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #6264a7;
        font-size: 10px;
        z-index: 10;
        transition: all 0.3s ease;
    }
    
    .toggle-btn:hover {
        background: #6264a7;
        color: white;
        transform: translateX(-50%) scale(1.2);
    }
    
    .toggle-btn.collapsed i {
        transform: rotate(180deg);
    }
    
    /* Collapsible Section */
    .collapsible-section {
        max-height: 10000px;
        opacity: 1;
        transition: max-height 0.5s ease, opacity 0.3s ease;
        overflow: hidden;
    }
    
    .collapsible-section.collapsed {
        max-height: 0;
        opacity: 0;
    }
    
    /* Empty State */
    .empty-message {
        text-align: center;
        padding: 3rem;
        color: #64748b;
        font-size: 1rem;
    }
    
    .empty-message i {
        color: #94a3b8;
        margin-bottom: 1rem;
    }
    
    /* Status Badge */
    .status-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-badge.active {
        background: #10b981;
        color: white;
    }
    
    .status-badge.inactive {
        background: #ef4444;
        color: white;
    }
    
    .agency-box.siege .status-badge {
        background: rgba(255, 255, 255, 0.3);
        color: white;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .agency-box {
            width: 240px;
            padding: 1.2rem;
        }
        
        .agencies-row {
            gap: 1.5rem;
        }
        
        .pyramid-wrapper {
            padding: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .agency-box {
            width: 200px;
            padding: 1rem;
        }
        
        .agency-logo, .agency-logo-placeholder {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .agency-name {
            font-size: 0.95rem;
        }
        
        .agencies-row {
            gap: 1rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let currentZoom = 1;

function zoomIn() {
    if (currentZoom < 2) {
        currentZoom += 0.1;
        document.getElementById('orgChart').style.transform = `scale(${currentZoom})`;
        document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
    }
}

function zoomOut() {
    if (currentZoom > 0.5) {
        currentZoom -= 0.1;
        document.getElementById('orgChart').style.transform = `scale(${currentZoom})`;
        document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
    }
}

function resetZoom() {
    currentZoom = 1;
    document.getElementById('orgChart').style.transform = 'scale(1)';
    document.getElementById('zoomLevel').textContent = '100%';
}

function toggleSection(id) {
    const section = document.getElementById('section-' + id);
    const btn = document.getElementById('btn-' + id);
    if (section && btn) {
        section.classList.toggle('collapsed');
        btn.classList.toggle('collapsed');
    }
}

function expandAll() {
    document.querySelectorAll('.collapsible-section').forEach(el => el.classList.remove('collapsed'));
    document.querySelectorAll('.toggle-btn').forEach(el => el.classList.remove('collapsed'));
}

function collapseAll() {
    document.querySelectorAll('.collapsible-section').forEach(el => el.classList.add('collapsed'));
    document.querySelectorAll('.toggle-btn').forEach(el => el.classList.add('collapsed'));
}

function goToAgency(agencyId) {
    window.location.href = '<?= base_url("admin/agencies/view/") ?>' + agencyId;
}
</script>
<?= $this->endSection() ?>

<?php
function renderAgenciesPyramid() {
    $agencyModel = new \App\Models\AgencyModel();
    $allAgencies = $agencyModel->getAgenciesWithStats();
    
    // Récupérer les sièges (agences sans parent_id)
    $sieges = array_filter($allAgencies, function($a) { return !$a['parent_id'] && $a['type'] === 'siege'; });
    
    $html = '';
    if (!empty($sieges)) {
        $html .= '<div class="org-level"><div class="agencies-row">';
        foreach ($sieges as $siege) {
            $html .= renderAgencyCard($siege, 'siege');
            $children = array_filter($allAgencies, function($a) use ($siege) { return $a['parent_id'] == $siege['id']; });
            if (!empty($children)) {
                $html .= '<div class="toggle-btn" id="btn-siege-' . $siege['id'] . '" onclick="toggleSection(\'siege-' . $siege['id'] . '\')"><i class="fas fa-minus"></i></div>';
            }
        }
        $html .= '</div></div>';
        foreach ($sieges as $siege) {
            $html .= renderChildAgencies($siege['id'], $allAgencies, 'siege-' . $siege['id']);
        }
    } else {
        $html = '<div class="empty-message"><i class="fas fa-sitemap fa-3x mb-3"></i><p>Aucune structure d\'agence trouvée</p></div>';
    }
    return $html;
}

function renderChildAgencies($parentId, $allAgencies, $sectionId) {
    $children = array_filter($allAgencies, function($a) use ($parentId) { return $a['parent_id'] == $parentId; });
    if (empty($children)) return '';
    
    $html = '<div class="collapsible-section" id="section-' . $sectionId . '"><div class="org-level"><div class="agencies-row">';
    foreach ($children as $child) {
        $html .= renderAgencyCard($child, 'agence');
        $subChildren = array_filter($allAgencies, function($a) use ($child) { return $a['parent_id'] == $child['id']; });
        if (!empty($subChildren)) {
            $html .= '<div class="toggle-btn" id="btn-agency-' . $child['id'] . '" onclick="toggleSection(\'agency-' . $child['id'] . '\')"><i class="fas fa-minus"></i></div>';
        }
    }
    $html .= '</div></div>';
    foreach ($children as $child) {
        $html .= renderChildAgencies($child['id'], $allAgencies, 'agency-' . $child['id']);
    }
    $html .= '</div>';
    return $html;
}

function renderAgencyCard($agency, $class = '') {
    $initials = strtoupper(substr($agency['name'], 0, 2));
    $statusClass = $agency['status'] === 'active' ? 'active' : 'inactive';
    $statusText = $agency['status'] === 'active' ? 'Actif' : 'Inactif';
    
    $html = '<div class="agency-box ' . $class . '" onclick="goToAgency(' . $agency['id'] . ')">';
    $html .= '<span class="status-badge ' . $statusClass . '">' . $statusText . '</span>';
    
    $html .= '<div class="agency-logo-wrapper">';
    if (!empty($agency['logo']) && file_exists(FCPATH . 'uploads/agencies/' . $agency['logo'])) {
        $html .= '<img src="' . base_url('uploads/agencies/' . $agency['logo']) . '" alt="Logo" class="agency-logo">';
    } else {
        $html .= '<div class="agency-logo-placeholder">' . $initials . '</div>';
    }
    $html .= '</div>';
    
    $html .= '<div class="agency-name">' . esc($agency['name']) . '</div>';
    $html .= '<div class="agency-code">' . esc($agency['code']) . '</div>';
    $html .= '<div class="agency-location"><i class="fas fa-map-marker-alt"></i> ' . esc($agency['city']) . ', ' . esc($agency['governorate']) . '</div>';
    
    $html .= '<div class="agency-stats">';
    $html .= '<div class="stat-item">';
    $html .= '<span class="stat-value">' . ($agency['users_count'] ?? 0) . '</span>';
    $html .= '<span class="stat-label">Utilisateurs</span>';
    $html .= '</div>';
    $html .= '<div class="stat-item">';
    $html .= '<span class="stat-value">' . ($agency['properties_count'] ?? 0) . '</span>';
    $html .= '<span class="stat-label">Biens</span>';
    $html .= '</div>';
    $html .= '<div class="stat-item">';
    $html .= '<span class="stat-value">' . ($agency['transactions_count'] ?? 0) . '</span>';
    $html .= '<span class="stat-label">Transactions</span>';
    $html .= '</div>';
    $html .= '</div>';
    
    $html .= '</div>';
    return $html;
}
?>
