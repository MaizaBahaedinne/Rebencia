<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.appointment-type-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}
.type-card {
    cursor: pointer;
    transition: all 0.3s;
    border: 2px solid #dee2e6;
}
.type-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.type-card.selected {
    border-color: #0d6efd;
    background-color: #f0f7ff;
}
.type-card input[type="radio"] {
    display: none;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-calendar-plus text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('admin/appointments') ?>">Rendez-vous</a></li>
                <li class="breadcrumb-item active"><?= isset($appointment) ? 'Modifier' : 'Créer' ?></li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= base_url('admin/appointments') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
</div>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <strong>Erreurs:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif ?>

<form action="<?= isset($appointment) ? base_url('admin/appointments/update/' . $appointment['id']) : base_url('admin/appointments/store') ?>" 
      method="post" 
      id="appointmentForm"
      autocomplete="off">
    <?= csrf_field() ?>

    <div class="row g-4">
        <!-- Main Form -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        Informations de base
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="title" class="form-label">
                                Titre du rendez-vous <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?= old('title', $appointment['title'] ?? '') ?>" 
                                   required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">
                                Type de rendez-vous <span class="text-danger">*</span>
                            </label>
                            <div class="row g-3">
                                <?php 
                                $types = [
                                    ['value' => 'visit', 'label' => 'Visite', 'icon' => 'fa-home', 'color' => 'primary'],
                                    ['value' => 'meeting', 'label' => 'Réunion', 'icon' => 'fa-handshake', 'color' => 'success'],
                                    ['value' => 'call', 'label' => 'Appel', 'icon' => 'fa-phone', 'color' => 'info'],
                                    ['value' => 'follow_up', 'label' => 'Suivi', 'icon' => 'fa-clipboard-check', 'color' => 'warning'],
                                    ['value' => 'other', 'label' => 'Autre', 'icon' => 'fa-ellipsis-h', 'color' => 'secondary']
                                ];
                                $selectedType = old('appointment_type', $appointment['appointment_type'] ?? 'visit');
                                foreach ($types as $type): 
                                ?>
                                <div class="col-md-4 col-6">
                                    <label class="type-card text-center p-3 rounded h-100 d-flex flex-column align-items-center justify-content-center <?= $selectedType === $type['value'] ? 'selected' : '' ?>">
                                        <input type="radio" 
                                               name="appointment_type" 
                                               value="<?= $type['value'] ?>" 
                                               <?= $selectedType === $type['value'] ? 'checked' : '' ?>>
                                        <i class="fas <?= $type['icon'] ?> appointment-type-icon text-<?= $type['color'] ?>"></i>
                                        <strong><?= $type['label'] ?></strong>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <label for="scheduled_at" class="form-label">
                                Date et heure <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="scheduled_at" 
                                   name="scheduled_at" 
                                   value="<?= old('scheduled_at', $appointment['scheduled_at'] ?? '') ?>" 
                                   required>
                            <small class="text-muted">Format: JJ/MM/AAAA HH:MM</small>
                        </div>

                        <div class="col-md-4">
                            <label for="duration" class="form-label">
                                Durée (minutes)
                            </label>
                            <input type="number" 
                                   class="form-control" 
                                   id="duration" 
                                   name="duration" 
                                   value="<?= old('duration', $appointment['duration'] ?? '60') ?>" 
                                   min="15" 
                                   step="15">
                        </div>

                        <div class="col-12">
                            <label for="location" class="form-label">
                                Lieu
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="location" 
                                   name="location" 
                                   value="<?= old('location', $appointment['location'] ?? '') ?>" 
                                   placeholder="Adresse ou nom du lieu">
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">
                                Description
                            </label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="4"><?= old('description', $appointment['description'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Relations -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-link text-primary"></i>
                        Relations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">
                                Client
                            </label>
                            <select class="form-select" id="client_id" name="client_id">
                                <option value="">-- Sélectionner un client --</option>
                                <?php if (!empty($clients)): ?>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?= $client['id'] ?>" 
                                                <?= old('client_id', $appointment['client_id'] ?? '') == $client['id'] ? 'selected' : '' ?>>
                                            <?= esc($client['first_name'] . ' ' . $client['last_name']) ?>
                                            <?php if (!empty($client['phone'])): ?>
                                                - <?= esc($client['phone']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Aucun client disponible</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="property_id" class="form-label">
                                Bien
                            </label>
                            <select class="form-select" id="property_id" name="property_id">
                                <option value="">-- Sélectionner un bien --</option>
                                <?php if (!empty($properties)): ?>
                                    <?php foreach ($properties as $property): ?>
                                        <option value="<?= $property['id'] ?>" 
                                                <?= old('property_id', $appointment['property_id'] ?? '') == $property['id'] ? 'selected' : '' ?>>
                                            <?= esc($property['title']) ?>
                                            <?php if (!empty($property['reference'])): ?>
                                                - Ref: <?= esc($property['reference']) ?>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <option value="" disabled>Aucun bien disponible</option>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">
                                Notes internes
                            </label>
                            <textarea class="form-control" 
                                      id="notes" 
                                      name="notes" 
                                      rows="3"><?= old('notes', $appointment['notes'] ?? '') ?></textarea>
                            <small class="text-muted">Ces notes ne sont visibles que par l'équipe</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-flag text-primary"></i>
                        Statut
                    </h5>
                </div>
                <div class="card-body">
                    <?php 
                    $statuses = [
                        'scheduled' => ['label' => 'Programmé', 'color' => 'warning'],
                        'confirmed' => ['label' => 'Confirmé', 'color' => 'info'],
                        'completed' => ['label' => 'Terminé', 'color' => 'success'],
                        'cancelled' => ['label' => 'Annulé', 'color' => 'danger'],
                        'no_show' => ['label' => 'Absent', 'color' => 'secondary']
                    ];
                    $selectedStatus = old('status', $appointment['status'] ?? 'scheduled');
                    ?>
                    <?php foreach ($statuses as $value => $status): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input" 
                               type="radio" 
                               name="status" 
                               id="status_<?= $value ?>" 
                               value="<?= $value ?>" 
                               <?= $selectedStatus === $value ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_<?= $value ?>">
                            <span class="badge bg-<?= $status['color'] ?>">
                                <?= $status['label'] ?>
                            </span>
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <button type="submit" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-save"></i>
                        <?= isset($appointment) ? 'Mettre à jour' : 'Créer le rendez-vous' ?>
                    </button>
                    <a href="<?= base_url('admin/appointments') ?>" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                </div>
            </div>

            <!-- Info -->
            <?php if (isset($appointment)): ?>
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <p class="text-muted mb-1 small">
                        <i class="fas fa-clock"></i>
                        Créé le: <?= date('d/m/Y H:i', strtotime($appointment['created_at'])) ?>
                    </p>
                    <?php if (!empty($appointment['updated_at'])): ?>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-edit"></i>
                        Modifié le: <?= date('d/m/Y H:i', strtotime($appointment['updated_at'])) ?>
                    </p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for datetime picker
    flatpickr("#scheduled_at", {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        time_24hr: true,
        locale: "fr",
        minuteIncrement: 15,
        defaultDate: "<?= old('scheduled_at', $appointment['scheduled_at'] ?? 'now') ?>",
        minDate: "today"
    });

    // Type card selection
    document.querySelectorAll('.type-card').forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            document.querySelectorAll('.type-card').forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Check the radio button
            const radio = this.querySelector('input[type="radio"]');
            if (radio) {
                radio.checked = true;
            }
        });
    });

    // Form validation
    const form = document.getElementById('appointmentForm');
    form.addEventListener('submit', function(e) {
        const title = document.getElementById('title').value.trim();
        const scheduledAt = document.getElementById('scheduled_at').value.trim();

        if (!title || !scheduledAt) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
