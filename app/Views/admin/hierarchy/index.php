<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb -->
<nav class="page-breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Hiérarchie organisationnelle</li>
    </ol>
</nav>

<!-- Page Title -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-sitemap text-primary"></i> Hiérarchie organisationnelle
    </h1>
</div>

<?php if (!empty($usersWithoutManager)): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <h5><i class="fas fa-exclamation-triangle"></i> Attention : Utilisateurs sans manager</h5>
        <p class="mb-2">Les utilisateurs suivants n'ont pas de manager assigné :</p>
        <ul class="mb-0">
            <?php foreach ($usersWithoutManager as $user): ?>
                <li>
                    <strong><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></strong> 
                    (<?= esc($user['email']) ?>) - 
                    <a href="<?= base_url('admin/hierarchy/assign-manager?user=' . $user['id']) ?>" class="alert-link">
                        Assigner un manager
                    </a>
                </li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<!-- Hierarchy Tree -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-folder-tree"></i> Arbre hiérarchique
        </h5>
    </div>
    <div class="card-body">
        <?php if (empty($tree)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-sitemap fa-3x mb-3"></i>
                <p>Aucune entité trouvée</p>
            </div>
        <?php else: ?>
            <div class="hierarchy-tree">
                <?= renderHierarchyTree($tree) ?>
            </div>
        <?php endif ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .hierarchy-tree {
        font-family: Arial, sans-serif;
    }
    
    .entity-node {
        margin-left: 20px;
        margin-bottom: 15px;
        border-left: 2px solid #dee2e6;
        padding-left: 15px;
    }
    
    .entity-header {
        display: flex;
        align-items: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .entity-header:hover {
        background: #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .entity-header .entity-icon {
        width: 40px;
        height: 40px;
        background: #0d6efd;
        color: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        font-size: 18px;
    }
    
    .entity-header .entity-info {
        flex: 1;
    }
    
    .entity-header .entity-name {
        font-weight: 600;
        color: #212529;
        margin-bottom: 2px;
    }
    
    .entity-header .entity-type {
        font-size: 12px;
        color: #6c757d;
    }
    
    .users-list {
        margin-left: 10px;
        margin-bottom: 10px;
    }
    
    .user-item {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        margin-bottom: 8px;
        transition: all 0.2s;
    }
    
    .user-item:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.1);
    }
    
    .user-item .user-avatar {
        width: 32px;
        height: 32px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .user-item .user-info {
        flex: 1;
    }
    
    .user-item .user-name {
        font-weight: 500;
        color: #212529;
        margin-bottom: 2px;
    }
    
    .user-item .user-role {
        font-size: 11px;
        color: #6c757d;
    }
    
    .user-item .user-manager {
        font-size: 11px;
        color: #0d6efd;
        margin-left: 5px;
    }
    
    .user-item .warning-badge {
        background: #ffc107;
        color: #000;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
    }
    
    .badge-entity-type {
        font-size: 11px;
        padding: 4px 8px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Toggle entity visibility
    document.querySelectorAll('.entity-header').forEach(header => {
        header.addEventListener('click', function() {
            const node = this.closest('.entity-node');
            const content = node.querySelector('.entity-content');
            if (content) {
                content.style.display = content.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
</script>
<?= $this->endSection() ?>

<?php
/**
 * Helper function to render hierarchy tree recursively
 */
function renderHierarchyTree($tree, $level = 0) {
    $html = '';
    
    foreach ($tree as $node) {
        $entity = $node['entity'];
        $users = $node['users'];
        $children = $node['children'];
        
        $html .= '<div class="entity-node" data-level="' . $level . '">';
        
        // Entity header
        $html .= '<div class="entity-header">';
        $html .= '<div class="entity-icon"><i class="fas fa-building"></i></div>';
        $html .= '<div class="entity-info">';
        $html .= '<div class="entity-name">' . esc($entity['name']) . '</div>';
        $html .= '<div class="entity-type">';
        $html .= '<span class="badge badge-entity-type bg-primary">' . esc($entity['type'] ?? 'Entité') . '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div><i class="fas fa-chevron-down"></i></div>';
        $html .= '</div>';
        
        // Entity content (users + children)
        $html .= '<div class="entity-content">';
        
        // Users in this entity
        if (!empty($users)) {
            $html .= '<div class="users-list">';
            foreach ($users as $user) {
                $initials = strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1));
                $html .= '<div class="user-item">';
                $html .= '<div class="user-avatar">' . $initials . '</div>';
                $html .= '<div class="user-info">';
                $html .= '<div class="user-name">' . esc($user['first_name'] . ' ' . $user['last_name']) . '</div>';
                $html .= '<div class="user-role">Role ID: ' . esc($user['role_id'] ?? 'N/A') . '</div>';
                
                if ($user['manager_id']) {
                    $html .= '<span class="user-manager"><i class="fas fa-user-tie"></i> A un manager</span>';
                }
                $html .= '</div>';
                
                if (!$user['manager_id'] && $user['role_id'] != 1) {
                    $html .= '<span class="warning-badge"><i class="fas fa-exclamation-triangle"></i> Sans manager</span>';
                }
                
                $html .= '<a href="' . base_url('admin/hierarchy/view-user/' . $user['id']) . '" class="btn btn-sm btn-outline-primary ms-2">';
                $html .= '<i class="fas fa-eye"></i>';
                $html .= '</a>';
                
                $html .= '</div>';
            }
            $html .= '</div>';
        }
        
        // Child entities
        if (!empty($children)) {
            $html .= renderHierarchyTree($children, $level + 1);
        }
        
        $html .= '</div>'; // entity-content
        $html .= '</div>'; // entity-node
    }
    
    return $html;
}
?>