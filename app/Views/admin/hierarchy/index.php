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
    <div class="card-body p-4">
        <div class="mb-3">
            <button class="btn btn-sm btn-outline-primary me-2" onclick="expandAll()">
                <i class="fas fa-expand"></i> Tout développer
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="collapseAll()">
                <i class="fas fa-compress"></i> Tout réduire
            </button>
        </div>
        <div class="org-chart-container">
            <?= renderOrgChartGrouped() ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .org-chart-container {
        overflow-x: auto;
        padding: 20px;
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
    
    /* Groupes d'agences */
    .agency-group {
        margin-bottom: 30px;
        border: 2px solid #dee2e6;
        border-radius: 12px;
        overflow: hidden;
        background: white;
    }
    
    .agency-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s;
    }
    
    .agency-header:hover {
        background: linear-gradient(135deg, #5568d3 0%, #653a8b 100%);
    }
    
    .agency-header h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }
    
    .agency-header .badge {
        font-size: 12px;
        padding: 6px 12px;
    }
    
    .agency-toggle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s;
    }
    
    .agency-toggle.collapsed {
        transform: rotate(-90deg);
    }
    
    .agency-content {
        padding: 20px;
        display: none;
        background: #f8f9fa;
    }
    
    .agency-content.show {
        display: block;
    }
    
    .agency-tree {
        display: flex;
        justify-content: center;
        padding: 20px 0;
        min-height: 200px;
    }
    
    .no-agency-group {
        background: #fff3cd;
        border: 2px solid #ffc107;
    }
    
    .no-agency-group .agency-header {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
    }
    
    .no-agency-group .agency-content {
        background: #fffef8;
    }

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
    
    // Récupérer toutes les agences
    $agencies = $agencyModel->findAll();
    
    // Récupérer les utilisateurs sans agence
    $usersWithoutAgency = $userModel->where('agency_id IS NULL')->findAll();
    
    $html = '';
    
    // Afficher chaque agence
    foreach ($agencies as $agency) {
        $agencyUsers = $userModel->where('agency_id', $agency['id'])->findAll();
        
        if (empty($agencyUsers)) {
            continue;
        }
        
        $html .= '<div class="agency-group">';
        $html .= '<div class="agency-header" onclick="toggleAgency(' . $agency['id'] . ')">';
        $html .= '<div class="d-flex align-items-center gap-3">';
        $html .= '<h4><i class="fas fa-building"></i> ' . esc($agency['name']) . '</h4>';
        $html .= '<span class="badge bg-light text-dark">' . count($agencyUsers) . ' personne(s)</span>';
        $html .= '</div>';
        $html .= '<div class="agency-toggle" id="toggle-agency-' . $agency['id'] . '"><i class="fas fa-chevron-down"></i></div>';
        $html .= '</div>';
        
        $html .= '<div class="agency-content show" id="agency-' . $agency['id'] . '">';
        
        // Construire l'arbre hiérarchique pour cette agence
        $html .= '<div class="agency-tree">';
        $html .= buildAgencyTree($agencyUsers, $userModel, $roleModel, $agencyModel);
        $html .= '</div>';
        
        $html .= '</div>'; // agency-content
        $html .= '</div>'; // agency-group
    }
    
    // Afficher les utilisateurs sans agence
    if (!empty($usersWithoutAgency)) {
        $html .= '<div class="agency-group no-agency-group">';
        $html .= '<div class="agency-header" onclick="toggleAgency(\'no-agency\')">';
        $html .= '<div class="d-flex align-items-center gap-3">';
        $html .= '<h4><i class="fas fa-exclamation-triangle"></i> Sans agence</h4>';
        $html .= '<span class="badge bg-danger">' . count($usersWithoutAgency) . ' personne(s)</span>';
        $html .= '</div>';
        $html .= '<div class="agency-toggle" id="toggle-agency-no-agency"><i class="fas fa-chevron-down"></i></div>';
        $html .= '</div>';
        
        $html .= '<div class="agency-content show" id="agency-no-agency">';
        $html .= '<div class="agency-tree">';
        $html .= buildAgencyTree($usersWithoutAgency, $userModel, $roleModel, $agencyModel);
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
    }
    
    if (empty($agencies) && empty($usersWithoutAgency)) {
        return '<div class="text-center text-muted py-5">
            <i class="fas fa-sitemap fa-3x mb-3"></i>
            <p>Aucun utilisateur trouvé.</p>
        </div>';
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

?>?>
