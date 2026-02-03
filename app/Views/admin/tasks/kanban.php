<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-tasks text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
    </div>
    <div>
        <a href="<?= base_url('admin/tasks/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvelle Tâche
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total</h6>
                        <h2><?= $statistics['total'] ?></h2>
                    </div>
                    <i class="fas fa-list fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Complétées</h6>
                        <h2><?= $statistics['completed'] ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-2x text-success"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">En Attente</h6>
                        <h2><?= $statistics['pending'] ?></h2>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">En Retard</h6>
                        <h2><?= $statistics['overdue'] ?></h2>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kanban Board -->
<div class="kanban-board">
    <?php 
    $columns = [
        'todo' => ['label' => 'À Faire', 'color' => 'secondary'],
        'in_progress' => ['label' => 'En Cours', 'color' => 'primary'],
        'review' => ['label' => 'Révision', 'color' => 'warning'],
        'completed' => ['label' => 'Terminé', 'color' => 'success']
    ];
    ?>
    
    <?php foreach ($columns as $status => $config): ?>
        <div class="kanban-column" data-status="<?= $status ?>">
            <div class="kanban-column-header bg-<?= $config['color'] ?> bg-opacity-10">
                <h5 class="mb-0 text-<?= $config['color'] ?>">
                    <?= $config['label'] ?>
                    <span class="badge bg-<?= $config['color'] ?> ms-2">
                        <?= count($tasksByStatus[$status]) ?>
                    </span>
                </h5>
            </div>
            
            <div class="kanban-column-content" id="column-<?= $status ?>">
                <?php foreach ($tasksByStatus[$status] as $task): ?>
                    <div class="kanban-card" data-task-id="<?= $task['id'] ?>" draggable="true">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="mb-0"><?= esc($task['title']) ?></h6>
                            <span class="badge bg-<?= 
                                $task['priority'] === 'urgent' ? 'danger' : 
                                ($task['priority'] === 'high' ? 'warning' : 
                                ($task['priority'] === 'medium' ? 'info' : 'secondary')) 
                            ?>">
                                <?= ucfirst($task['priority']) ?>
                            </span>
                        </div>
                        
                        <?php if ($task['description']): ?>
                            <p class="text-muted small mb-2">
                                <?= esc(substr($task['description'], 0, 80)) ?>...
                            </p>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-user"></i>
                                <?= esc($task['assigned_name']) ?>
                            </small>
                            <?php if ($task['due_date']): ?>
                                <small class="text-<?= strtotime($task['due_date']) < time() ? 'danger' : 'muted' ?>">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($task['due_date'])) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// Drag and Drop functionality
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.kanban-card');
    const columns = document.querySelectorAll('.kanban-column-content');
    
    cards.forEach(card => {
        card.addEventListener('dragstart', dragStart);
        card.addEventListener('dragend', dragEnd);
    });
    
    columns.forEach(column => {
        column.addEventListener('dragover', dragOver);
        column.addEventListener('drop', drop);
        column.addEventListener('dragenter', dragEnter);
        column.addEventListener('dragleave', dragLeave);
    });
    
    let draggedCard = null;
    
    function dragStart(e) {
        draggedCard = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
    }
    
    function dragEnd(e) {
        this.classList.remove('dragging');
    }
    
    function dragOver(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
    }
    
    function dragEnter(e) {
        this.classList.add('drag-over');
    }
    
    function dragLeave(e) {
        this.classList.remove('drag-over');
    }
    
    function drop(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        
        if (draggedCard) {
            this.appendChild(draggedCard);
            
            // Update status via AJAX
            const taskId = draggedCard.dataset.taskId;
            const newStatus = this.closest('.kanban-column').dataset.status;
            
            updateTaskStatus(taskId, newStatus);
        }
    }
    
    function updateTaskStatus(taskId, status) {
        fetch('<?= base_url('admin/tasks/updateStatus') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${taskId}&status=${status}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log('Task updated');
            }
        });
    }
});
</script>

<style>
.kanban-board {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.kanban-column {
    background: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.kanban-column-header {
    padding: 1rem;
    border-bottom: 2px solid rgba(0,0,0,0.1);
}

.kanban-column-content {
    padding: 1rem;
    min-height: 500px;
}

.kanban-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: move;
    transition: all 0.3s;
}

.kanban-card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.kanban-card.dragging {
    opacity: 0.5;
}

.kanban-column-content.drag-over {
    background: rgba(13, 110, 253, 0.1);
}

@media (max-width: 992px) {
    .kanban-board {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 576px) {
    .kanban-board {
        grid-template-columns: 1fr;
    }
}
</style>

<?= $this->endSection() ?>
