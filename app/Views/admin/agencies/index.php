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
