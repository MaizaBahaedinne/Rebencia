<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-project-diagram text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Pipeline</li>
            </ol>
        </nav>
    </div>
    <div>
        <div class="btn-group" role="group">
            <a href="<?= base_url('admin/workflows/pipeline/property') ?>" 
               class="btn btn-sm <?= $entityType === 'property' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-building"></i> Propriétés
            </a>
            <a href="<?= base_url('admin/workflows/pipeline/client') ?>" 
               class="btn btn-sm <?= $entityType === 'client' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-users"></i> Clients
            </a>
            <a href="<?= base_url('admin/workflows/pipeline/transaction') ?>" 
               class="btn btn-sm <?= $entityType === 'transaction' ? 'btn-primary' : 'btn-outline-primary' ?>">
                <i class="fas fa-exchange-alt"></i> Transactions
            </a>
        </div>
    </div>
</div>

<div class="kanban-board">
    <?php foreach ($stages as $stage): ?>
        <div class="kanban-column" data-stage="<?= esc($stage) ?>">
            <div class="kanban-header">
                <h5><?= esc($stage) ?></h5>
                <span class="badge bg-secondary"><?= count($pipeline[$stage] ?? []) ?></span>
            </div>
            <div class="kanban-cards" id="stage-<?= esc(str_replace(' ', '-', $stage)) ?>">
                <?php if (isset($pipeline[$stage])): ?>
                    <?php foreach ($pipeline[$stage] as $instance): ?>
                        <div class="kanban-card" data-instance-id="<?= $instance['id'] ?>" draggable="true">
                            <div class="card-header-kanban">
                                <?php if ($instance['entity_data']['image']): ?>
                                    <img src="<?= base_url('uploads/properties/' . $instance['entity_data']['image']) ?>" alt="Image">
                                <?php else: ?>
                                    <div class="card-placeholder">
                                        <i class="fas fa-<?= $entityType === 'property' ? 'building' : ($entityType === 'client' ? 'user' : 'exchange-alt') ?>"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-body-kanban">
                                <h6><?= esc($instance['entity_data']['title']) ?></h6>
                                <p class="text-muted small mb-2"><?= esc($instance['entity_data']['reference']) ?></p>
                                <?php if ($instance['entity_data']['price']): ?>
                                    <div class="card-price">
                                        <?= number_format($instance['entity_data']['price'], 0, ',', ' ') ?> TND
                                    </div>
                                <?php endif; ?>
                                <?php if ($instance['assigned_to']): ?>
                                    <div class="card-agent mt-2">
                                        <i class="fas fa-user-tie"></i>
                                        <small>Assigné</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer-kanban">
                                <small class="text-muted">
                                    <i class="far fa-clock"></i>
                                    <?= date('d/m/Y', strtotime($instance['started_at'])) ?>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<style>
.kanban-board {
    display: flex;
    gap: 1.5rem;
    overflow-x: auto;
    padding: 1rem 0;
    min-height: 500px;
}

.kanban-column {
    flex: 0 0 320px;
    background: #f8f9fa;
    border-radius: 12px;
    padding: 1rem;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
}

.kanban-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #dee2e6;
}

.kanban-header h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
    color: #212529;
}

.kanban-cards {
    flex: 1;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.kanban-card {
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    cursor: move;
    transition: all 0.3s;
}

.kanban-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.kanban-card.dragging {
    opacity: 0.5;
}

.card-header-kanban {
    height: 120px;
    overflow: hidden;
    border-radius: 10px 10px 0 0;
    background: #e9ecef;
}

.card-header-kanban img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #adb5bd;
}

.card-body-kanban {
    padding: 1rem;
}

.card-body-kanban h6 {
    font-size: 0.95rem;
    font-weight: 600;
    margin-bottom: 0.25rem;
    color: #212529;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #0d6efd;
}

.card-agent {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #6c757d;
}

.card-footer-kanban {
    padding: 0.75rem 1rem;
    border-top: 1px solid #e9ecef;
}

.kanban-cards::-webkit-scrollbar {
    width: 6px;
}

.kanban-cards::-webkit-scrollbar-thumb {
    background: #ced4da;
    border-radius: 3px;
}

/* Drag over effect */
.kanban-cards.drag-over {
    background: rgba(13, 110, 253, 0.1);
    border: 2px dashed #0d6efd;
    border-radius: 8px;
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-cards');
    
    let draggedElement = null;

    // Make cards draggable
    cards.forEach(card => {
        card.addEventListener('dragstart', function(e) {
            draggedElement = this;
            this.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
        });

        card.addEventListener('dragend', function() {
            this.classList.remove('dragging');
            draggedElement = null;
        });
    });

    // Make columns droppable
    columns.forEach(column => {
        column.addEventListener('dragover', function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            this.classList.add('drag-over');
        });

        column.addEventListener('dragleave', function() {
            this.classList.remove('drag-over');
        });

        column.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            if (draggedElement) {
                this.appendChild(draggedElement);
                
                const instanceId = draggedElement.dataset.instanceId;
                const newStage = this.closest('.kanban-column').dataset.stage;
                
                // Update via AJAX
                fetch('<?= base_url('admin/workflows/move-stage') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        instance_id: instanceId,
                        new_stage: newStage
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateColumnCounts();
                    } else {
                        alert('Erreur lors du déplacement');
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
            }
        });
    });

    function updateColumnCounts() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const count = column.querySelectorAll('.kanban-card').length;
            column.querySelector('.badge').textContent = count;
        });
    }
});
</script>
<?= $this->endSection() ?>
