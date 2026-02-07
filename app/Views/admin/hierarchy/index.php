<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Organigramme</li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-sitemap text-primary"></i> Organigramme
    </h1>
    <a href="<?= base_url('admin/hierarchy/assign-manager') ?>" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Assigner un manager
    </a>
</div>

<?php if (!empty($usersWithoutManager)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-exclamation-triangle"></i> Attention : <?= count($usersWithoutManager) ?> utilisateur(s) sans manager</h5>
        <p class="mb-2">Ces utilisateurs doivent avoir un manager assigné :</p>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($usersWithoutManager as $user): ?>
                <a href="<?= base_url('admin/hierarchy/assign-manager?user=' . $user['id']) ?>" class="btn btn-sm btn-outline-warning">
                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?>
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            <?php endforeach ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<?php if (!empty($usersWithoutAgency)): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-exclamation-circle"></i> Urgent : <?= count($usersWithoutAgency) ?> agent(s) non affecté(s) à une agence</h5>
        <p class="mb-2">Ces collaborateurs doivent être affectés à une agence :</p>
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($usersWithoutAgency as $user): ?>
                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-building me-1"></i>
                    <?= esc($user['first_name'] . ' ' . $user['last_name']) ?> 
                    (<?= esc($user['email']) ?>)
                    <i class="fas fa-arrow-right ms-1"></i>
                </a>
            <?php endforeach ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<!-- Organization Chart -->
<div class="card">
    <div class="card-body p-0">
        <!-- Controls Bar -->
        <div class="controls-bar p-3 border-bottom bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="expandAll()">
                        <i class="fas fa-expand-alt"></i> Tout développer
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                        <i class="fas fa-compress-alt"></i> Tout réduire
                    </button>
                </div>
                <div class="zoom-controls">
                    <button class="btn btn-sm btn-outline-dark me-1" onclick="zoomOut()" title="Zoom arrière">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <span class="badge bg-dark mx-2" id="zoomLevel">100%</span>
                    <button class="btn btn-sm btn-outline-dark" onclick="zoomIn()" title="Zoom avant">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary ms-3" onclick="resetZoom()" title="Réinitialiser">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Tree Container -->
        <div class="tree-wrapper">
            <div class="tree-container" id="orgChart">
                <?= renderOrgTree() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Tree Wrapper */
    .tree-wrapper {
        overflow: auto;
        background: #ffffff;
        height: calc(100vh - 350px);
        min-height: 600px;
    }
    
    .tree-container {
        padding: 20px 40px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        transform-origin: top left;
        transition: transform 0.3s ease;
        min-width: 100%;
    }
    
    .controls-bar {
        position: sticky;
        top: 0;
        z-index: 10;
        background: white !important;
    }
    
    .zoom-controls .badge {
        min-width: 60px;
        font-size: 13px;
        padding: 6px 10px;
    }
    
    /* Tree Structure - Style macOS */
    .tree-item {
        position: relative;
        user-select: none;
    }
    
    .tree-row {
        display: flex;
        align-items: center;
        padding: 6px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    
    .tree-row:hover {
        background: #f5f5f7;
    }
    
    .tree-row.active {
        background: #e3e3e5;
    }
    
    /* Chevron/Toggle */
    .tree-toggle {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 4px;
        flex-shrink: 0;
        color: #86868b;
        transition: transform 0.2s;
    }
    
    .tree-toggle i {
        font-size: 12px;
    }
    
    .tree-toggle.expanded {
        transform: rotate(90deg);
    }
    
    .tree-toggle.empty {
        visibility: hidden;
    }
    
    /* Icon */
    .tree-icon {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        flex-shrink: 0;
        font-size: 16px;
    }
    
    .tree-icon.headquarters {
        color: #ff9500;
    }
    
    .tree-icon.agency {
        color: #007aff;
    }
    
    .tree-icon.user {
        color: #34c759;
    }
    
    /* Label */
    .tree-label {
        flex: 1;
        font-size: 14px;
        color: #1d1d1f;
        font-weight: 400;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .tree-label.entity-name {
        font-weight: 600;
    }
    
    /* Badge */
    .tree-badge {
        margin-left: 8px;
        padding: 2px 8px;
        background: #f5f5f7;
        border-radius: 10px;
        font-size: 11px;
        color: #86868b;
        font-weight: 500;
    }
    
    .tree-badge.highlight {
        background: #007aff;
        color: white;
    }
    
    /* Children Container */
    .tree-children {
        margin-left: 20px;
        display: none;
        border-left: 1px solid #e5e5e7;
        padding-left: 8px;
    }
    
    .tree-children.expanded {
        display: block;
    }
    
    /* User Card Mini */
    .user-mini {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .user-avatar-mini {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        font-weight: 600;
        flex-shrink: 0;
    }
    
    .user-info-mini {
        flex: 1;
        min-width: 0;
    }
    
    .user-name {
        font-weight: 500;
        font-size: 13px;
        color: #1d1d1f;
    }
    
    .user-role {
        font-size: 11px;
        color: #86868b;
    }
    
    /* Empty State */
    .tree-empty {
        padding: 12px 20px;
        color: #86868b;
        font-size: 13px;
        font-style: italic;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .tree-container {
            padding: 15px 20px;
        }
        
        .tree-children {
            margin-left: 16px;
            padding-left: 6px;
        }
        
        .tree-wrapper {
            height: calc(100vh - 400px);
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let currentZoom = 1;
const zoomStep = 0.1;
const minZoom = 0.5;
const maxZoom = 2;

function zoomIn() {
    if (currentZoom < maxZoom) {
        currentZoom += zoomStep;
        applyZoom();
    }
}

function zoomOut() {
    if (currentZoom > minZoom) {
        currentZoom -= zoomStep;
        applyZoom();
    }
}

function resetZoom() {
    currentZoom = 1;
    applyZoom();
}

function applyZoom() {
    const chart = document.getElementById('orgChart');
    chart.style.transform = `scale(${currentZoom})`;
    document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
}

function toggleTreeItem(id) {
    const children = document.getElementById('children-' + id);
    const toggle = document.getElementById('toggle-' + id);
    
    if (children && toggle) {
        const isExpanded = children.classList.contains('expanded');
        if (isExpanded) {
            children.classList.remove('expanded');
            toggle.classList.remove('expanded');
        } else {
            children.classList.add('expanded');
            toggle.classList.add('expanded');
        }
    }
}

function expandAll() {
    document.querySelectorAll('.tree-children').forEach(el => {
        el.classList.add('expanded');
    });
    document.querySelectorAll('.tree-toggle').forEach(el => {
        if (!el.classList.contains('empty')) {
            el.classList.add('expanded');
        }
    });
}

function collapseAll() {
    document.querySelectorAll('.tree-children').forEach(el => {
        el.classList.remove('expanded');
    });
    document.querySelectorAll('.tree-toggle').forEach(el => {
        el.classList.remove('expanded');
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey || e.metaKey) {
        if (e.key === '+' || e.key === '=') {
            e.preventDefault();
            zoomIn();
        } else if (e.key === '-') {
            e.preventDefault();
            zoomOut();
        } else if (e.key === '0') {
            e.preventDefault();
            resetZoom();
        }
    }
});

// Mouse wheel zoom
document.getElementById('orgChart').addEventListener('wheel', function(e) {
    if (e.ctrlKey || e.metaKey) {
        e.preventDefault();
        if (e.deltaY < 0) {
            zoomIn();
        } else {
            zoomOut();
        }
    }
}, { passive: false });
</script>
<?= $this->endSection() ?>

<?php
/**
 * Render organization tree - macOS Finder style
 */
function renderOrgTree() {
    $userModel = new \App\Models\UserModel();
    $agencyModel = new \App\Models\AgencyModel();
    $roleModel = new \App\Models\RoleModel();
    
    $agencies = $agencyModel->where('status', 'active')->findAll();
    $headquartersUsers = $userModel->where('agency_id IS NULL')->orWhere('agency_id', 0)->findAll();
    
    $html = '<div class="tree-root">';
    
    // Siège Social
    $html .= '<div class="tree-item">';
    $html .= '<div class="tree-row" onclick="toggleTreeItem(\'hq\')">';
    $html .= '<div class="tree-toggle ' . (empty($headquartersUsers) ? 'empty' : 'expanded') . '" id="toggle-hq">';
    $html .= '<i class="fas fa-chevron-right"></i>';
    $html .= '</div>';
    $html .= '<div class="tree-icon headquarters"><i class="fas fa-landmark"></i></div>';
    $html .= '<div class="tree-label entity-name">Siège Social</div>';
    $html .= '<span class="tree-badge">' . count($headquartersUsers) . '</span>';
    $html .= '</div>';
    
    // Users du siège
    if (!empty($headquartersUsers)) {
        $html .= '<div class="tree-children expanded" id="children-hq">';
        $html .= renderUsersTree($headquartersUsers, $roleModel);
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    // Agences
    $html .= renderAgenciesTree(null, $agencies, $userModel, $roleModel);
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Render agencies tree recursively
 */
function renderAgenciesTree($parentId, $allAgencies, $userModel, $roleModel) {
    $html = '';
    
    $agencies = array_filter($allAgencies, function($a) use ($parentId) {
        return $a['parent_id'] == $parentId;
    });
    
    foreach ($agencies as $agency) {
        $agencyUsers = $userModel->where('agency_id', $agency['id'])->findAll();
        $hasChildren = !empty($agencyUsers) || count(array_filter($allAgencies, function($a) use ($agency) {
            return $a['parent_id'] == $agency['id'];
        })) > 0;
        
        $html .= '<div class="tree-item">';
        $html .= '<div class="tree-row" onclick="toggleTreeItem(\'agency-' . $agency['id'] . '\')">';
        $html .= '<div class="tree-toggle ' . ($hasChildren ? 'expanded' : 'empty') . '" id="toggle-agency-' . $agency['id'] . '">';
        $html .= '<i class="fas fa-chevron-right"></i>';
        $html .= '</div>';
        $html .= '<div class="tree-icon agency"><i class="fas fa-building"></i></div>';
        $html .= '<div class="tree-label entity-name">' . esc($agency['name']) . '</div>';
        $html .= '<span class="tree-badge">' . count($agencyUsers) . '</span>';
        $html .= '</div>';
        
        if ($hasChildren) {
            $html .= '<div class="tree-children expanded" id="children-agency-' . $agency['id'] . '">';
            
            // Users de l'agence
            if (!empty($agencyUsers)) {
                $html .= renderUsersTree($agencyUsers, $roleModel);
            }
            
            // Sous-agences
            $html .= renderAgenciesTree($agency['id'], $allAgencies, $userModel, $roleModel);
            
            $html .= '</div>';
        }
        
        $html .= '</div>';
    }
    
    return $html;
}

/**
 * Render users tree with hierarchy
 */
function renderUsersTree($users, $roleModel) {
    $html = '';
    
    // Trouver les users sans manager (racine)
    $rootUsers = array_filter($users, function($u) use ($users) {
        if (!$u['manager_id']) return true;
        // Vérifier si le manager est dans la même liste
        foreach ($users as $user) {
            if ($user['id'] == $u['manager_id']) return false;
        }
        return true;
    });
    
    foreach ($rootUsers as $user) {
        $html .= renderUserNode($user, $users, $roleModel);
    }
    
    return $html;
}

/**
 * Render single user node with subordinates
 */
function renderUserNode($user, $allUsers, $roleModel) {
    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
    $role = $roleModel->find($user['role_id']);
    $roleName = $role ? $role['name'] : 'N/A';
    
    // Trouver les subordonnés
    $subordinates = array_filter($allUsers, function($u) use ($user) {
        return $u['manager_id'] == $user['id'];
    });
    
    $hasSubordinates = !empty($subordinates);
    
    $html = '<div class="tree-item">';
    
    if ($hasSubordinates) {
        $html .= '<div class="tree-row" onclick="toggleTreeItem(\'user-' . $user['id'] . '\')">';
        $html .= '<div class="tree-toggle expanded" id="toggle-user-' . $user['id'] . '">';
        $html .= '<i class="fas fa-chevron-right"></i>';
        $html .= '</div>';
    } else {
        $html .= '<div class="tree-row" onclick="window.location.href=\'' . base_url('admin/hierarchy/view-user/' . $user['id']) . '\'">';
        $html .= '<div class="tree-toggle empty"></div>';
    }
    
    $html .= '<div class="tree-icon user"><i class="fas fa-user-circle"></i></div>';
    $html .= '<div class="user-mini">';
    $html .= '<div class="user-avatar-mini">' . $initials . '</div>';
    $html .= '<div class="user-info-mini">';
    $html .= '<div class="user-name">' . esc($user['first_name'] . ' ' . $user['last_name']) . '</div>';
    $html .= '<div class="user-role">' . esc($roleName) . '</div>';
    $html .= '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    // Subordonnés
    if ($hasSubordinates) {
        $html .= '<div class="tree-children expanded" id="children-user-' . $user['id'] . '">';
        foreach ($subordinates as $subordinate) {
            $html .= renderUserNode($subordinate, $allUsers, $roleModel);
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

?>
