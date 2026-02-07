<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-bars me-2"></i>Gestion des Menus par Rôle
        </h1>
        <a href="<?= base_url('admin/menus') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour aux menus
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif ?>

    <div class="row">
        <!-- Role Selector -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tag me-2"></i>Sélectionner un Rôle</h5>
                </div>
                <div class="list-group list-group-flush">
                    <?php foreach ($roles as $role): ?>
                        <a href="<?= base_url('admin/menus/role-menus/' . $role['id']) ?>" 
                           class="list-group-item list-group-item-action <?= $role['id'] == $currentRole['id'] ? 'active' : '' ?>">
                            <i class="fas fa-circle me-2" style="font-size: 8px;"></i>
                            <?= esc($role['display_name']) ?>
                            <span class="badge bg-secondary float-end"><?= esc($role['name']) ?></span>
                        </a>
                    <?php endforeach ?>
                </div>
            </div>
        </div>

        <!-- Menu Manager -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list me-2"></i>
                            Menus pour: <strong><?= esc($currentRole['display_name']) ?></strong>
                        </h5>
                        <button class="btn btn-success" onclick="saveMenus()">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Available Menus -->
                        <div class="col-md-5">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-clipboard-list me-2"></i>Menus Disponibles
                            </h6>
                            <div id="availableMenus" class="menu-list p-3 bg-light rounded" style="min-height: 400px;">
                                <?php renderAvailableMenus($allMenus, $assignedMenuIds); ?>
                            </div>
                        </div>

                        <!-- Arrow -->
                        <div class="col-md-2 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas fa-exchange-alt fa-2x text-primary"></i>
                                <p class="small text-muted mt-2">Glisser-Déposer</p>
                            </div>
                        </div>

                        <!-- Assigned Menus -->
                        <div class="col-md-5">
                            <h6 class="text-muted mb-3">
                                <i class="fas fa-check-circle me-2"></i>Menus Assignés
                            </h6>
                            <div id="assignedMenus" class="menu-list p-3 bg-light rounded" style="min-height: 400px;">
                                <?php renderAssignedMenus($allMenus, $assignedMenuIds); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Instructions:</strong> Faites glisser les éléments de menu entre les deux listes pour les assigner ou les retirer. 
                L'ordre dans la liste "Menus Assignés" détermine l'ordre d'affichage dans le menu.
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .menu-list {
        border: 2px dashed #dee2e6;
        transition: all 0.3s;
    }
    
    .menu-list.drag-over {
        border-color: #0d6efd;
        background: #e7f1ff !important;
    }
    
    .menu-item {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 12px 15px;
        margin-bottom: 10px;
        cursor: grab;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .menu-item:hover {
        background: #f8f9fa;
        border-color: #0d6efd;
        transform: translateX(5px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .menu-item.dragging {
        opacity: 0.5;
        cursor: grabbing;
    }
    
    .menu-item .drag-handle {
        color: #6c757d;
        font-size: 16px;
    }
    
    .menu-item .menu-icon {
        width: 30px;
        text-align: center;
        color: #0d6efd;
    }
    
    .menu-item .menu-title {
        flex: 1;
        font-weight: 500;
    }
    
    .menu-item .menu-url {
        font-size: 11px;
        color: #6c757d;
    }
    
    .menu-item.submenu {
        margin-left: 30px;
        background: #f8f9fa;
    }
    
    .menu-item.submenu::before {
        content: '└─';
        color: #6c757d;
        margin-right: 5px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const roleId = <?= $currentRole['id'] ?>;
let draggedElement = null;

// Initialize drag and drop
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    const menuItems = document.querySelectorAll('.menu-item');
    const dropZones = document.querySelectorAll('.menu-list');
    
    // Make menu items draggable
    menuItems.forEach(item => {
        item.setAttribute('draggable', 'true');
        
        item.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        });
        
        item.addEventListener('dragend', function(e) {
            this.classList.remove('dragging');
            draggedElement = null;
        });
    });
    
    // Setup drop zones
    dropZones.forEach(zone => {
        zone.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });
        
        zone.addEventListener('dragleave', function(e) {
            this.classList.remove('drag-over');
        });
        
        zone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedElement) {
                this.appendChild(draggedElement);
            }
        });
    });
}

function saveMenus() {
    const assignedMenus = document.querySelectorAll('#assignedMenus .menu-item');
    const menus = [];
    
    assignedMenus.forEach((item, index) => {
        menus.push({
            id: item.dataset.menuId,
            visible: 1
        });
    });
    
    console.log('Sending data:', { role_id: roleId, menus: menus });
    
    // Send via AJAX with FormData
    const formData = new FormData();
    formData.append('role_id', roleId);
    formData.append('menus', JSON.stringify(menus));
    
    fetch('<?= base_url('admin/menus/update-role-menus') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('Response:', data);
        if (data.success) {
            alert('✅ ' + data.message);
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Erreur: ' + error);
    });
}
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Erreur: ' + error);
    });
}
</script>
<?= $this->endSection() ?>

<?php
function renderAvailableMenus($menus, $assignedIds, $level = 0) {
    foreach ($menus as $menu) {
        if (!in_array($menu['id'], $assignedIds)) {
            $indent = $level > 0 ? ' submenu' : '';
            echo '<div class="menu-item' . $indent . '" data-menu-id="' . $menu['id'] . '">';
            echo '<i class="fas fa-grip-vertical drag-handle"></i>';
            if ($menu['icon']) {
                echo '<i class="' . esc($menu['icon']) . ' menu-icon"></i>';
            } else {
                echo '<i class="fas fa-circle menu-icon" style="font-size: 8px;"></i>';
            }
            echo '<div class="flex-fill">';
            echo '<div class="menu-title">' . esc($menu['title']) . '</div>';
            if ($menu['url']) {
                echo '<div class="menu-url">' . esc($menu['url']) . '</div>';
            }
            echo '</div>';
            echo '</div>';
            
            if (isset($menu['children'])) {
                renderAvailableMenus($menu['children'], $assignedIds, $level + 1);
            }
        }
    }
}

function renderAssignedMenus($menus, $assignedIds, $level = 0) {
    foreach ($menus as $menu) {
        if (in_array($menu['id'], $assignedIds)) {
            $indent = $level > 0 ? ' submenu' : '';
            echo '<div class="menu-item' . $indent . '" data-menu-id="' . $menu['id'] . '">';
            echo '<i class="fas fa-grip-vertical drag-handle"></i>';
            if ($menu['icon']) {
                echo '<i class="' . esc($menu['icon']) . ' menu-icon"></i>';
            } else {
                echo '<i class="fas fa-circle menu-icon" style="font-size: 8px;"></i>';
            }
            echo '<div class="flex-fill">';
            echo '<div class="menu-title">' . esc($menu['title']) . '</div>';
            if ($menu['url']) {
                echo '<div class="menu-url">' . esc($menu['url']) . '</div>';
            }
            echo '</div>';
            echo '</div>';
            
            if (isset($menu['children'])) {
                renderAssignedMenus($menu['children'], $assignedIds, $level + 1);
            }
        }
    }
}
?>
