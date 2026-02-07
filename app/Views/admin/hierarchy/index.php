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
        <div class="pyramid-wrapper">
            <div class="pyramid-container" id="orgChart">
                <?= renderPyramidChart() ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('styles') ?>
<style>
    /* Pyramid Wrapper */
    .pyramid-wrapper {
        overflow: auto;
        background: #f8f9fa;
        height: calc(100vh - 350px);
        min-height: 700px;
    }
    
    .pyramid-container {
        padding: 40px;
        transform-origin: top center;
        transition: transform 0.3s ease;
        min-width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
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
    
    /* Pyramid Structure */
    .org-level {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        margin-bottom: 60px;
        position: relative;
        width: 100%;
    }
    
    .org-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 0 20px;
        position: relative;
    }
    
    /* Entity Card (Siège/Agence) */
    .entity-box {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 20px;
        min-width: 200px;
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.3);
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        text-align: center;
        color: white;
        margin-bottom: 20px;
    }
    
    .entity-box.headquarters {
        background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
        box-shadow: 0 8px 24px rgba(255, 215, 0, 0.4);
        min-width: 250px;
    }
    
    .entity-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 32px rgba(102, 126, 234, 0.4);
    }
    
    .entity-box .icon {
        font-size: 32px;
        margin-bottom: 12px;
    }
    
    .entity-box .name {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .entity-box .count {
        font-size: 13px;
        opacity: 0.9;
    }
    
    /* User Card */
    .user-box {
        background: white;
        border-radius: 10px;
        padding: 16px;
        min-width: 180px;
        max-width: 180px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: all 0.3s;
        position: relative;
        margin: 0 10px 30px;
        border: 2px solid #e0e0e0;
    }
    
    .user-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        border-color: #667eea;
    }
    
    .user-box.ceo {
        border-color: #ffd700;
        background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
    }
    
    .user-box.manager {
        border-color: #667eea;
    }
    
    .user-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 600;
        margin: 0 auto 12px;
    }
    
    .user-box.ceo .user-avatar {
        background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
    }
    
    .user-name {
        font-size: 14px;
        font-weight: 600;
        color: #1d1d1f;
        margin-bottom: 4px;
        text-align: center;
    }
    
    .user-role {
        font-size: 11px;
        color: #86868b;
        text-align: center;
        margin-bottom: 8px;
    }
    
    /* Connection Lines */
    .org-level::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 50%;
        width: 2px;
        height: 30px;
        background: #cbd5e0;
        transform: translateX(-50%);
    }
    
    .org-level:first-child::before {
        display: none;
    }
    
    .users-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        position: relative;
        gap: 10px;
    }
    
    .users-row::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 10%;
        right: 10%;
        height: 2px;
        background: #cbd5e0;
    }
    
    .users-row .user-box::before {
        content: '';
        position: absolute;
        top: -30px;
        left: 50%;
        width: 2px;
        height: 30px;
        background: #cbd5e0;
        transform: translateX(-50%);
    }
    
    /* Collapse/Expand */
    .collapsible-section {
        width: 100%;
        transition: all 0.3s;
    }
    
    .collapsible-section.collapsed {
        display: none;
    }
    
    .toggle-btn {
        position: absolute;
        bottom: -12px;
        left: 50%;
        transform: translateX(-50%);
        width: 24px;
        height: 24px;
        background: white;
        border: 2px solid #667eea;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #667eea;
        font-size: 12px;
        z-index: 5;
        transition: all 0.3s;
    }
    
    .toggle-btn:hover {
        background: #667eea;
        color: white;
        transform: translateX(-50%) scale(1.1);
    }
    
    .toggle-btn.collapsed i {
        transform: rotate(180deg);
    }
    
    /* Empty State */
    .empty-message {
        text-align: center;
        padding: 40px;
        color: #86868b;
        font-style: italic;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .user-box {
            min-width: 160px;
            max-width: 160px;
        }
    }
    
    @media (max-width: 768px) {
        .pyramid-container {
            padding: 20px;
        }
        
        .org-level {
            margin-bottom: 40px;
        }
        
        .user-box {
            min-width: 140px;
            max-width: 140px;
            margin: 0 5px 20px;
        }
        
        .entity-box {
            min-width: 160px;
        }
        
        .pyramid-wrapper {
            height: calc(100vh - 400px);
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

function goToUser(userId) {
    window.location.href = '<?= base_url("admin/hierarchy/view-user/") ?>' + userId;
}
</script>
<?= $this->endSection() ?>

<?php
function renderPyramidChart() {
    $userModel = new \App\Models\UserModel();
    $allUsers = $userModel->findAll();
    
    $ceos = array_filter($allUsers, function($u) { return !$u['manager_id']; });
    
    $html = '';
    if (!empty($ceos)) {
        $html .= '<div class="org-level"><div class="users-row">';
        foreach ($ceos as $ceo) {
            $html .= renderUserCard($ceo, new \App\Models\RoleModel(), 'ceo');
            $subs = array_filter($allUsers, function($u) use ($ceo) { return $u['manager_id'] == $ceo['id']; });
            if (!empty($subs)) {
                $html .= '<div class="toggle-btn" id="btn-ceo-' . $ceo['id'] . '" onclick="toggleSection(\'ceo-' . $ceo['id'] . '\')"><i class="fas fa-minus"></i></div>';
            }
        }
        $html .= '</div></div>';
        foreach ($ceos as $ceo) {
            $html .= renderSubordinates($ceo['id'], $allUsers, new \App\Models\RoleModel(), 'ceo-' . $ceo['id']);
        }
    } else {
        $html = '<div class="empty-message"><i class="fas fa-sitemap fa-3x mb-3"></i><p>Aucune structure hiérarchique trouvée</p></div>';
    }
    return $html;
}

function renderSubordinates($managerId, $allUsers, $roleModel, $parentId) {
    $subs = array_filter($allUsers, function($u) use ($managerId) { return $u['manager_id'] == $managerId; });
    if (empty($subs)) return '';
    
    $html = '<div class="collapsible-section" id="section-' . $parentId . '"><div class="org-level"><div class="users-row">';
    foreach ($subs as $sub) {
        $html .= renderUserCard($sub, $roleModel, 'manager');
        $subSubs = array_filter($allUsers, function($u) use ($sub) { return $u['manager_id'] == $sub['id']; });
        if (!empty($subSubs)) {
            $html .= '<div class="toggle-btn" id="btn-sub-' . $sub['id'] . '" onclick="toggleSection(\'sub-' . $sub['id'] . '\')"><i class="fas fa-minus"></i></div>';
        }
    }
    $html .= '</div></div>';
    foreach ($subs as $sub) {
        $html .= renderSubordinates($sub['id'], $allUsers, $roleModel, 'sub-' . $sub['id']);
    }
    $html .= '</div>';
    return $html;
}

function renderUserCard($user, $roleModel, $class = '') {
    $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
    $role = $roleModel->find($user['role_id']);
    $roleName = $role ? ucfirst(str_replace('_', ' ', $role['name'])) : 'N/A';
    
    $html = '<div class="user-box ' . $class . '" onclick="goToUser(' . $user['id'] . ')">';
    $html .= '<div class="user-avatar">' . $initials . '</div>';
    $html .= '<div class="user-name">' . esc($user['first_name'] . ' ' . $user['last_name']) . '</div>';
    $html .= '<div class="user-role">' . esc($roleName) . '</div>';
    $html .= '</div>';
    return $html;
}
?>
