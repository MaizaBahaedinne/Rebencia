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
        
        <!-- Pyramid Container -->
        <div class="hierarchy-wrapper">
            <div class="hierarchy-container" id="orgChart">
                <?= renderTreeHierarchy() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('styles') ?>
<style>
    /* Hierarchy Wrapper - Style Mac Finder */
    .hierarchy-wrapper {
        overflow: auto;
        background: #ffffff;
        height: calc(100vh - 350px);
        min-height: 700px;
        padding: 20px;
    }
    
    .hierarchy-container {
        max-width: 100%;
        transform-origin: top left;
        transition: transform 0.3s ease;
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
    
    /* Agency Group - Style Mac Finder */
    .agency-group {
        margin-bottom: 30px;
        background: #f5f5f7;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .agency-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .agency-header:hover {
        background: linear-gradient(135deg, #5568d3 0%, #653a8a 100%);
    }
    
    .agency-header.headquarters {
        background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    }
    
    .agency-header.headquarters:hover {
        background: linear-gradient(135deg, #e6c200 0%, #e67e00 100%);
    }
    
    .agency-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .agency-icon {
        font-size: 24px;
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .agency-details h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .agency-details p {
        margin: 0;
        font-size: 13px;
        opacity: 0.9;
    }
    
    .agency-toggle {
        font-size: 20px;
        transition: transform 0.3s;
    }
    
    .agency-toggle.collapsed {
        transform: rotate(-90deg);
    }
    
    /* Tree Structure - Style Mac Finder */
    .tree-content {
        padding: 20px;
        background: white;
    }
    
    .tree-content.collapsed {
        display: none;
    }
    
    .tree-node {
        margin-left: 0;
    }
    
    .tree-item {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        margin-bottom: 4px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    
    .tree-item:hover {
        background: #f5f5f7;
    }
    
    .tree-item.has-children {
        font-weight: 500;
    }
    
    .tree-toggle {
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 8px;
        color: #86868b;
        font-size: 12px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    
    .tree-toggle.collapsed {
        transform: rotate(-90deg);
    }
    
    .tree-toggle.empty {
        visibility: hidden;
    }
    
    .user-avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 600;
        margin-right: 12px;
        flex-shrink: 0;
    }
    
    .tree-item.ceo .user-avatar-small {
        background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(255, 215, 0, 0.4);
    }
    
    .tree-item.manager .user-avatar-small {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: 2px solid #fff;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
    }
    
    .user-info {
        flex: 1;
        min-width: 0;
    }
    
    .user-name {
        font-size: 14px;
        font-weight: 500;
        color: #1d1d1f;
        margin-bottom: 2px;
    }
    
    .user-role {
        font-size: 12px;
        color: #86868b;
    }
    
    .tree-children {
        margin-left: 40px;
        border-left: 2px solid #e5e5e7;
        padding-left: 12px;
        margin-top: 4px;
    }
    
    .tree-children.collapsed {
        display: none;
    }
    
    .subordinate-count {
        background: #667eea;
        color: white;
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 12px;
        margin-left: 8px;
        font-weight: 600;
    }
    
    /* Empty State */
    .empty-message {
        text-align: center;
        padding: 60px 20px;
        color: #86868b;
    }
    
    .empty-message i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.5;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .hierarchy-wrapper {
            height: calc(100vh - 400px);
            padding: 10px;
        }
        
        .agency-header {
            padding: 12px 16px;
        }
        
        .tree-content {
            padding: 12px;
        }
        
        .tree-children {
            margin-left: 24px;
            padding-left: 8px;
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
        document.getElementById('orgChart').style.transform = 'scale(' + currentZoom + ')';
        document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
    }
}

function zoomOut() {
    if (currentZoom > 0.5) {
        currentZoom -= 0.1;
        document.getElementById('orgChart').style.transform = 'scale(' + currentZoom + ')';
        document.getElementById('zoomLevel').textContent = Math.round(currentZoom * 100) + '%';
    }
}

function resetZoom() {
    currentZoom = 1;
    document.getElementById('orgChart').style.transform = 'scale(1)';
    document.getElementById('zoomLevel').textContent = '100%';
}

function toggleAgency(agencyId) {
    const content = document.getElementById('agency-content-' + agencyId);
    const toggle = document.getElementById('agency-toggle-' + agencyId);
    if (content && toggle) {
        content.classList.toggle('collapsed');
        toggle.classList.toggle('collapsed');
    }
}

function toggleNode(userId) {
    const children = document.getElementById('children-' + userId);
    const toggle = document.getElementById('toggle-' + userId);
    if (children && toggle) {
        children.classList.toggle('collapsed');
        toggle.classList.toggle('collapsed');
    }
}

function expandAll() {
    document.querySelectorAll('.tree-content, .tree-children').forEach(el => el.classList.remove('collapsed'));
    document.querySelectorAll('.agency-toggle, .tree-toggle').forEach(el => el.classList.remove('collapsed'));
}

function collapseAll() {
    document.querySelectorAll('.tree-content, .tree-children').forEach(el => el.classList.add('collapsed'));
    document.querySelectorAll('.agency-toggle, .tree-toggle').forEach(el => el.classList.add('collapsed'));
}

function goToUser(userId) {
    window.location.href = '<?= base_url("admin/hierarchy/view-user/") ?>' + userId;
}
</script>
<?= $this->endSection() ?>

<?php
function renderTreeHierarchy() {
    $userModel = new \App\Models\UserModel();
    $agencyModel = new \App\Models\AgencyModel();
    $roleModel = new \App\Models\RoleModel();
    
    // Récupérer l'utilisateur connecté
    $currentUserId = session()->get('user_id');
    $currentUser = $userModel->find($currentUserId);
    $currentRoleLevel = session()->get('role_level');
    $currentAgencyId = $currentUser['agency_id'] ?? null;
    
    // Admin voit tout, sinon filtre par agency
    if ($currentRoleLevel == 100 || !$currentAgencyId) {
        $allUsers = $userModel->select('users.*, roles.name as role_name, roles.level as role_level, agencies.name as agency_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('agencies', 'agencies.id = users.agency_id', 'left')
            ->findAll();
        $agencies = $agencyModel->where('status', 'active')->findAll();
    } else {
        $allUsers = $userModel->select('users.*, roles.name as role_name, roles.level as role_level, agencies.name as agency_name')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('agencies', 'agencies.id = users.agency_id', 'left')
            ->where('users.agency_id', $currentAgencyId)
            ->findAll();
        $agencies = $agencyModel->where('id', $currentAgencyId)->where('status', 'active')->findAll();
    }
    
    // Grouper les utilisateurs par agence
    $usersByAgency = [];
    $usersWithoutAgency = [];
    
    foreach ($allUsers as $user) {
        if ($user['agency_id']) {
            if (!isset($usersByAgency[$user['agency_id']])) {
                $usersByAgency[$user['agency_id']] = [];
            }
            $usersByAgency[$user['agency_id']][] = $user;
        } else {
            $usersWithoutAgency[] = $user;
        }
    }
    
    $html = '';
    
    // Afficher les agences avec leurs hiérarchies
    foreach ($agencies as $agency) {
        $agencyUsers = $usersByAgency[$agency['id']] ?? [];
        if (empty($agencyUsers)) continue;
        
        // Trouver les utilisateurs racines (sans manager) dans cette agence
        $rootUsers = array_filter($agencyUsers, function($u) { return !$u['manager_id']; });
        
        $html .= '<div class="agency-group">';
        $html .= '<div class="agency-header ' . ($agency['type'] === 'headquarters' ? 'headquarters' : '') . '" onclick="toggleAgency(' . $agency['id'] . ')">';
        $html .= '<div class="agency-info">';
        $html .= '<div class="agency-icon"><i class="fas fa-' . ($agency['type'] === 'headquarters' ? 'landmark' : 'building') . '"></i></div>';
        $html .= '<div class="agency-details">';
        $html .= '<h3>' . esc($agency['name']) . '</h3>';
        $html .= '<p>' . count($agencyUsers) . ' collaborateur(s)</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="agency-toggle" id="agency-toggle-' . $agency['id'] . '"><i class="fas fa-chevron-down"></i></div>';
        $html .= '</div>';
        
        $html .= '<div class="tree-content" id="agency-content-' . $agency['id'] . '">';
        foreach ($rootUsers as $rootUser) {
            $html .= renderTreeNode($rootUser, $agencyUsers, $roleModel);
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    
    // Utilisateurs sans agence
    if (!empty($usersWithoutAgency)) {
        $rootUsers = array_filter($usersWithoutAgency, function($u) { return !$u['manager_id']; });
        
        $html .= '<div class="agency-group">';
        $html .= '<div class="agency-header" onclick="toggleAgency(0)">';
        $html .= '<div class="agency-info">';
        $html .= '<div class="agency-icon"><i class="fas fa-users"></i></div>';
        $html .= '<div class="agency-details">';
        $html .= '<h3>Sans agence</h3>';
        $html .= '<p>' . count($usersWithoutAgency) . ' collaborateur(s)</p>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="agency-toggle" id="agency-toggle-0"><i class="fas fa-chevron-down"></i></div>';
        $html .= '</div>';
        
        $html .= '<div class="tree-content" id="agency-content-0">';
        foreach ($rootUsers as $rootUser) {
            $html .= renderTreeNode($rootUser, $usersWithoutAgency, $roleModel);
        }
        $html .= '</div>';
        $html .= '</div>';
    }
    
    if (empty($html)) {
        $html = '<div class="empty-message"><i class="fas fa-sitemap"></i><p>Aucune structure hiérarchique trouvée</p></div>';
    }
    
    return $html;
}

function renderTreeNode($user, $allUsers, $roleModel) {
    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
    $roleName = ucfirst(str_replace('_', ' ', $user['role_name'] ?? 'N/A'));
    
    // Trouver les subordonnés
    $subordinates = array_filter($allUsers, function($u) use ($user) { 
        return $u['manager_id'] == $user['id']; 
    });
    $hasChildren = !empty($subordinates);
    
    // Déterminer la classe CSS
    $cssClass = '';
    if (!$user['manager_id']) {
        $cssClass = 'ceo';
    } elseif ($hasChildren) {
        $cssClass = 'manager';
    }
    
    $html = '<div class="tree-node">';
    $html .= '<div class="tree-item ' . $cssClass . '" onclick="goToUser(' . $user['id'] . ')">';
    
    if ($hasChildren) {
        $html .= '<span class="tree-toggle" id="toggle-' . $user['id'] . '" onclick="event.stopPropagation(); toggleNode(' . $user['id'] . ')"><i class="fas fa-chevron-down"></i></span>';
    } else {
        $html .= '<span class="tree-toggle empty"></span>';
    }
    
    $html .= '<div class="user-avatar-small">' . $initials . '</div>';
    $html .= '<div class="user-info">';
    $html .= '<div class="user-name">' . esc($user['first_name'] . ' ' . $user['last_name']);
    if ($hasChildren) {
        $html .= '<span class="subordinate-count">' . count($subordinates) . '</span>';
    }
    $html .= '</div>';
    $html .= '<div class="user-role">' . esc($roleName) . '</div>';
    $html .= '</div>';
    $html .= '</div>';
    
    if ($hasChildren) {
        $html .= '<div class="tree-children" id="children-' . $user['id'] . '">';
        foreach ($subordinates as $sub) {
            $html .= renderTreeNode($sub, $allUsers, $roleModel);
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}
?>
