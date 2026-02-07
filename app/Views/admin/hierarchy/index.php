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
                        <i class="fas fa-expand"></i> Tout développer
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                        <i class="fas fa-compress"></i> Tout réduire
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
        
        <!-- Zoomable Container -->
        <div class="org-chart-wrapper">
            <div class="org-chart-container" id="orgChart">
                <?= renderOrgChartGrouped() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Wrapper et zoom */
    .org-chart-wrapper {
        overflow: auto;
        background: #f8f9fa;
        height: calc(100vh - 350px);
        min-height: 600px;
        position: relative;
    }
    
    .org-chart-container {
        padding: 40px;
        min-width: 100%;
        transform-origin: top center;
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
    
    .org-tree {
        display: flex;
        justify-content: center;
        padding: 20px 0;
    }
    
    .org-node {
        display: inline-block;
        text-align: center;
        position: relative;
    }
    
    .org-card {
        background: white;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 14px;
        min-width: 160px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.06);
        transition: all 0.3s;
        position: relative;
        margin: 0 8px 25px;
        cursor: pointer;
    }
    
    .org-card:hover {
        box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        transform: translateY(-2px);
        border-color: #0d6efd;
    }
    
    .org-card.ceo {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }
    
    .org-card.manager {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-color: #f093fb;
    }
    
    .org-card.team-lead {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        border-color: #4facfe;
    }
    
    .org-card.member {
        background: white;
        border-color: #dee2e6;
    }
    
    .org-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
        font-size: 18px;
        font-weight: 600;
    }
    
    .org-card.member .org-avatar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .org-name {
        font-weight: 600;
        font-size: 14px;
        margin-bottom: 3px;
        line-height: 1.2;
    }
    
    .org-role {
        font-size: 11px;
        opacity: 0.9;
        margin-bottom: 2px;
    }
    
    .org-agency {
        font-size: 10px;
        opacity: 0.85;
        font-style: italic;
    }
    
    .org-card.member .org-role,
    .org-card.member .org-agency {
        color: #6c757d;
    }
    
    .org-stats {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,0.3);
    }
    
    .org-card.member .org-stats {
        border-top-color: #e0e0e0;
    }
    
    .org-stat {
        text-align: center;
    }
    
    .org-stat-value {
        font-weight: 600;
        font-size: 15px;
    }
    
    .org-stat-label {
        font-size: 9px;
        opacity: 0.8;
    }
    
    .org-children {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 15px;
        position: relative;
    }
    
    /* Lignes de connexion */
    .org-node::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 50%;
        width: 2px;
        height: 30px;
        background: #dee2e6;
    }
    
    .org-node.root::before {
        display: none;
    }
    
    .org-children::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 0;
        right: 0;
        height: 2px;
        background: #dee2e6;
    }
    
    .warning-badge {
        position: absolute;
        top: -8px;
        right: -8px;
        background: #ffc107;
        color: #000;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    
    /* Structure hiérarchique organisationnelle */
    .organizational-tree {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }
    
    .headquarters-section {
        text-align: center;
        margin-bottom: 40px;
    }
    
    .hierarchy-connector {
        width: 3px;
        height: 60px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
        margin: 0 auto 40px;
        position: relative;
    }
    
    .hierarchy-connector::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-top: 10px solid #764ba2;
    }
    
    .agencies-section {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .agency-branch {
        margin-bottom: 30px;
        position: relative;
    }
    
    /* Indentation par niveau */
    .agency-branch.level-0 {
        margin-left: 0;
    }
    
    .agency-branch.level-1 {
        margin-left: 60px;
        position: relative;
    }
    
    .agency-branch.level-2 {
        margin-left: 120px;
        position: relative;
    }
    
    .agency-branch.level-3 {
        margin-left: 180px;
        position: relative;
    }
    
    /* Connecteur parent-fils */
    .parent-connector {
        position: absolute;
        left: -60px;
        top: 50%;
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        transform: translateY(-50%);
    }
    
    .parent-connector::before {
        content: '';
        position: absolute;
        left: 0;
        top: -50px;
        width: 2px;
        height: 100px;
        background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    }
    
    .sub-agencies {
        margin-top: 20px;
    }
    
    /* Cartes d'entité (siège et agences) */
    .entity-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        transition: all 0.3s;
        cursor: pointer;
        position: relative;
        border: 3px solid #e0e0e0;
    }
    
    .entity-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(0,0,0,0.18);
    }
    
    .headquarters-card {
        border-color: #ffd700;
        background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
    }
    
    .headquarters-card .entity-icon {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
    }
    
    .agency-card {
        border-color: #667eea;
    }
    
    .entity-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin: 0 auto 16px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .entity-name {
        font-size: 20px;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 8px;
        letter-spacing: 1px;
    }
    
    .entity-count {
        font-size: 14px;
        color: #7f8c8d;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .entity-type {
        margin-top: 8px;
    }
    
    .entity-type .badge {
        font-size: 11px;
        padding: 4px 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .toggle-icon {
        position: absolute;
        top: 16px;
        right: 16px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(102, 126, 234, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }
    
    .toggle-icon i {
        transition: transform 0.3s;
        color: #667eea;
    }
    
    .toggle-icon.collapsed i {
        transform: rotate(-180deg);
    }
    
    /* Organigramme de l'entité */
    .entity-orgchart {
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px dashed #dee2e6;
        transition: all 0.3s;
    }
    
    .entity-orgchart.collapsed {
        display: none;
    }
    
    /* Message pour entités vides */
    .empty-entity-message {
        text-align: center;
        padding: 40px 20px;
        background: #f8f9fa;
        border-radius: 12px;
        margin: 20px 0;
    }
    
    .empty-entity-message i {
        opacity: 0.5;
    }
    
    .empty-entity-message p {
        font-size: 14px;
        margin: 12px 0;
    }
    
    .empty-entity-message .btn {
        margin-top: 12px;
    }
    
    /* Responsive Design */
    @media (max-width: 1200px) {
        .agency-branch.level-1 {
            margin-left: 40px;
        }
        
        .agency-branch.level-2 {
            margin-left: 80px;
        }
        
        .agency-branch.level-3 {
            margin-left: 120px;
        }
        
        .parent-connector {
            left: -40px;
            width: 40px;
        }
    }
    
    @media (max-width: 768px) {
        .org-chart-wrapper {
            height: calc(100vh - 400px);
        }
        
        .organizational-tree {
            padding: 10px;
        }
        
        .agencies-section {
            width: 100%;
        }
        
        .agency-branch {
            margin-bottom: 20px;
        }
        
        .agency-branch.level-1,
        .agency-branch.level-2,
        .agency-branch.level-3 {
            margin-left: 20px;
        }
        
        .parent-connector {
            left: -20px;
            width: 20px;
        }
        
        .parent-connector::before {
            height: 60px;
            top: -30px;
        }
        
        .entity-card {
            padding: 16px;
        }
        
        .entity-icon {
            width: 60px;
            height: 60px;
            font-size: 24px;
        }
        
        .entity-name {
            font-size: 16px;
        }
        
        .entity-count {
            font-size: 12px;
        }
    }
    
    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .agency-branch {
        animation: fadeIn 0.5s ease-out;
    }
    
    .agency-branch:nth-child(1) { animation-delay: 0.1s; }
    .agency-branch:nth-child(2) { animation-delay: 0.2s; }
    .agency-branch:nth-child(3) { animation-delay: 0.3s; }
    .agency-branch:nth-child(4) { animation-delay: 0.4s; }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Toggle agency
function toggleAgency(agencyId) {
    const content = document.getElementById('agency-' + agencyId);
    const toggle = document.getElementById('toggle-agency-' + agencyId);
    
    if (content.classList.contains('show')) {
        content.classList.remove('show');
        toggle.classList.add('collapsed');
    } else {
        content.classList.add('show');
        toggle.classList.remove('collapsed');
    }
}

// Expand all
function expandAll() {
    document.querySelectorAll('.agency-content').forEach(el => {
        el.classList.add('show');
    });
    document.querySelectorAll('.agency-toggle').forEach(el => {
        el.classList.remove('collapsed');
    });
}

// Collapse all
function collapseAll() {
    document.querySelectorAll('.agency-content').forEach(el => {
        el.classList.remove('show');
    });
    document.querySelectorAll('.agency-toggle').forEach(el => {
        el.classList.add('collapsed');
    });
}

document.querySelectorAll('.org-card').forEach(card => {
    card.addEventListener('click', function(e) {
        e.stopPropagation();
        const userId = this.dataset.userId;
        if (userId) {
            window.location.href = '<?= base_url('admin/hierarchy/view-user/') ?>' + userId;
        }
    });
});
</script>
<?= $this->endSection() ?>

<?php
/**
 * Helper function to render organization chart grouped by agency
 */
function renderOrgChartGrouped() {
    $userModel = new \App\Models\UserModel();
    $agencyModel = new \App\Models\AgencyModel();
    $roleModel = new \App\Models\RoleModel();
    
    // Récupérer toutes les agences actives
    $agencies = $agencyModel->where('status', 'active')->findAll();
    
    // Récupérer les utilisateurs sans agence (siège)
    $headquartersUsers = $userModel->where('agency_id IS NULL')->orWhere('agency_id', 0)->findAll();
    
    $html = '<div class="organizational-tree">';
    
    // 1. SIÈGE (en haut de la hiérarchie) - Toujours affiché
    $html .= '<div class="headquarters-section">';
    $html .= '<div class="entity-card headquarters-card">';
    $html .= '<div class="entity-icon"><i class="fas fa-landmark"></i></div>';
    $html .= '<div class="entity-name">SIÈGE SOCIAL</div>';
    $html .= '<div class="entity-count">' . count($headquartersUsers) . ' personne(s)</div>';
    $html .= '</div>';
    
    // Organigramme du siège
    $html .= '<div class="entity-orgchart">';
    if (!empty($headquartersUsers)) {
        $html .= '<div class="org-tree">';
        $html .= buildAgencyTree($headquartersUsers, $userModel, $roleModel, $agencyModel);
        $html .= '</div>';
    } else {
        $html .= '<div class="empty-entity-message">';
        $html .= '<i class="fas fa-users-slash fa-2x mb-2 text-muted"></i>';
        $html .= '<p class="text-muted mb-0">Aucune personne affectée au siège</p>';
        $html .= '</div>';
    }
    $html .= '</div>';
    $html .= '</div>';
    
    // Ligne de connexion vers les agences
    if (!empty($agencies)) {
        $html .= '<div class="hierarchy-connector"></div>';
    }
    
    // 2. AGENCES (hiérarchie parent-fils)
    if (!empty($agencies)) {
        $html .= '<div class="agencies-section">';
        $html .= renderAgencyHierarchy(null, $agencies, $userModel, $roleModel, $agencyModel, 0);
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Render agency hierarchy recursively
 */
function renderAgencyHierarchy($parentId, $allAgencies, $userModel, $roleModel, $agencyModel, $level) {
    $html = '';
    
    // Filtrer les agences qui ont ce parent
    $agencies = array_filter($allAgencies, function($agency) use ($parentId) {
        return $agency['parent_id'] == $parentId;
    });
    
    foreach ($agencies as $agency) {
        $agencyUsers = $userModel->where('agency_id', $agency['id'])->findAll();
        
        $indentClass = 'level-' . $level;
        $html .= '<div class="agency-branch ' . $indentClass . '" id="agency-branch-' . $agency['id'] . '">';
        
        // Connecteur parent-fils
        if ($level > 0) {
            $html .= '<div class="parent-connector"></div>';
        }
        
        // Carte de l'agence
        $html .= '<div class="entity-card agency-card" onclick="toggleAgency(' . $agency['id'] . ')">';
        $html .= '<div class="entity-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">';
        $html .= '<i class="fas fa-building"></i>';
        $html .= '</div>';
        $html .= '<div class="entity-name">' . esc(strtoupper($agency['name'])) . '</div>';
        $html .= '<div class="entity-count">' . count($agencyUsers) . ' personne(s)</div>';
        if ($agency['type']) {
            $html .= '<div class="entity-type"><span class="badge bg-info">' . esc($agency['type']) . '</span></div>';
        }
        $html .= '<div class="toggle-icon" id="agency-toggle-' . $agency['id'] . '">';
        $html .= '<i class="fas fa-chevron-down"></i>';
        $html .= '</div>';
        $html .= '</div>';
        
        // Organigramme de l'agence (collapsible)
        $html .= '<div class="entity-orgchart" id="agency-content-' . $agency['id'] . '" style="display: block;">';
        if (!empty($agencyUsers)) {
            $html .= '<div class="org-tree">';
            $html .= buildAgencyTree($agencyUsers, $userModel, $roleModel, $agencyModel);
            $html .= '</div>';
        } else {
            $html .= '<div class="empty-entity-message">';
            $html .= '<i class="fas fa-users-slash fa-2x mb-2 text-muted"></i>';
            $html .= '<p class="text-muted mb-0">Aucune personne affectée à cette agence</p>';
            $html .= '<a href="' . base_url('admin/users/bulk-manage') . '" class="btn btn-sm btn-outline-primary mt-2">';
            $html .= '<i class="fas fa-user-plus"></i> Affecter des personnes';
            $html .= '</a>';
            $html .= '</div>';
        }
        $html .= '</div>'; // entity-orgchart
        
        // Agences enfants (récursif)
        $html .= '<div class="sub-agencies">';
        $html .= renderAgencyHierarchy($agency['id'], $allAgencies, $userModel, $roleModel, $agencyModel, $level + 1);
        $html .= '</div>';
        
        $html .= '</div>'; // agency-branch
    }
    
    return $html;
}

/**
 * Build hierarchical tree for an agency
 */
function buildAgencyTree($users, $userModel, $roleModel, $agencyModel) {
    // Trouver les responsables (sans manager dans cette agence)
    $roots = [];
    $userIds = array_column($users, 'id');
    
    foreach ($users as $user) {
        // Si pas de manager, ou si le manager n'est pas dans cette agence
        if (!$user['manager_id'] || !in_array($user['manager_id'], $userIds)) {
            $roots[] = $user;
        }
    }
    
    if (empty($roots)) {
        // Si pas de racine trouvée, afficher tous les utilisateurs
        $html = '<div class="org-node root">';
        foreach ($users as $user) {
            $html .= renderAgencyUserNode($user, $userModel, $roleModel, $agencyModel, $users);
        }
        $html .= '</div>';
        return $html;
    }
    
    $html = '<div class="org-node root">';
    foreach ($roots as $root) {
        $html .= renderAgencyUserNode($root, $userModel, $roleModel, $agencyModel, $users);
    }
    $html .= '</div>';
    
    return $html;
}

/**
 * Render a user node with their subordinates in the agency
 */
function renderAgencyUserNode($user, $userModel, $roleModel, $agencyModel, $agencyUsers) {
    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
    
    $role = $roleModel->find($user['role_id']);
    $agency = $user['agency_id'] ? $agencyModel->find($user['agency_id']) : null;
    
    $roleLabel = $role ? ucfirst(str_replace('_', ' ', $role['name'])) : 'Non défini';
    $agencyLabel = $agency ? $agency['name'] : 'Non affecté';
    
    // Trouver les subordonnés dans cette agence
    $agencyUserIds = array_column($agencyUsers, 'id');
    $subordinates = [];
    foreach ($agencyUsers as $agencyUser) {
        if ($agencyUser['manager_id'] == $user['id']) {
            $subordinates[] = $agencyUser;
        }
    }
    $subordinateCount = count($subordinates);
    
    // Déterminer la classe de style
    if (!$user['manager_id']) {
        $levelClass = 'ceo';
    } else if ($subordinateCount > 0) {
        $levelClass = 'manager';
    } else {
        $levelClass = 'member';
    }
    
    $html = '<div class="org-card ' . $levelClass . '" data-user-id="' . $user['id'] . '">';
    $html .= '<div class="org-avatar">' . $initials . '</div>';
    $html .= '<div class="org-name">' . esc($user['first_name'] . ' ' . $user['last_name']) . '</div>';
    $html .= '<div class="org-role">' . esc($roleLabel) . '</div>';
    $html .= '<div class="org-agency"><i class="fas fa-building"></i> ' . esc($agencyLabel) . '</div>';
    
    if ($subordinateCount > 0) {
        $html .= '<div class="org-stats">';
        $html .= '<div class="org-stat">';
        $html .= '<div class="org-stat-value">' . $subordinateCount . '</div>';
        $html .= '<div class="org-stat-label">Équipe</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    // Afficher les subordonnés
    if ($subordinateCount > 0) {
        $html .= '<div class="org-children">';
        foreach ($subordinates as $subordinate) {
            $html .= '<div class="org-node">';
            $html .= renderAgencyUserNode($subordinate, $userModel, $roleModel, $agencyModel, $agencyUsers);
            $html .= '</div>';
        }
        $html .= '</div>';
    }
    
    return $html;
}

?>

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

function expandAll() {
    document.querySelectorAll('.entity-orgchart').forEach(content => {
        content.style.display = 'block';
    });
    document.querySelectorAll('.toggle-icon').forEach(toggle => {
        toggle.classList.remove('collapsed');
    });
}

function collapseAll() {
    document.querySelectorAll('.entity-orgchart').forEach(content => {
        if (!content.closest('.headquarters-section')) {
            content.style.display = 'none';
        }
    });
    document.querySelectorAll('.toggle-icon').forEach(toggle => {
        toggle.classList.add('collapsed');
    });
}

function toggleAgency(agencyId) {
    const content = document.getElementById('agency-content-' + agencyId);
    const toggle = document.getElementById('agency-toggle-' + agencyId);
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        toggle.classList.remove('collapsed');
    } else {
        content.style.display = 'none';
        toggle.classList.add('collapsed');
    }
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
