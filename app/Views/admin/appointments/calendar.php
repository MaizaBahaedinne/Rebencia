<?= $this->extend('layouts/admin_modern') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-calendar-alt text-primary"></i>
            <?= esc($page_title) ?>
        </h1>
        <nav aria-label="breadcrumb" class="page-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item active">Calendrier</li>
            </ol>
        </nav>
    </div>
    <div>
        <a href="<?= base_url('admin/appointments/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Rendez-vous
        </a>
        <a href="<?= base_url('admin/appointments/list') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-list"></i> Vue Liste
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Calendar -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-body" style="min-height: 600px;">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Upcoming Appointments -->
    <div class="col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="fas fa-clock text-warning"></i> 
                    Rendez-vous à venir
                </h5>
            </div>
            <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                <?php if (empty($upcoming)): ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fas fa-calendar-check fa-3x mb-3"></i>
                        <p>Aucun rendez-vous prévu</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($upcoming as $apt): ?>
                        <div class="appointment-item p-3 border-bottom">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0"><?= esc($apt['title']) ?></h6>
                                <span class="badge bg-<?= $apt['status'] === 'confirmed' ? 'success' : 'warning' ?>">
                                    <?= ucfirst($apt['status']) ?>
                                </span>
                            </div>
                            <div class="text-muted small">
                                <div class="mb-1">
                                    <i class="fas fa-calendar"></i>
                                    <?= date('d/m/Y', strtotime($apt['scheduled_at'])) ?>
                                    <i class="fas fa-clock ms-2"></i>
                                    <?= date('H:i', strtotime($apt['scheduled_at'])) ?>
                                </div>
                                <?php if ($apt['client_name']): ?>
                                    <div>
                                        <i class="fas fa-user"></i>
                                        <?= esc($apt['client_name']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if ($apt['property_title']): ?>
                                    <div>
                                        <i class="fas fa-home"></i>
                                        <?= esc($apt['property_title']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar CSS & JS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/fr.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        buttonText: {
            today: "Aujourd'hui",
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        height: 'auto',
        events: {
            url: '<?= base_url('admin/appointments/getEvents') ?>',
            method: 'GET',
            failure: function() {
                alert('Erreur lors du chargement des rendez-vous');
            }
        },
        eventClick: function(info) {
            if (confirm('Voulez-vous voir les détails de ce rendez-vous?')) {
                window.location.href = '<?= base_url('admin/appointments/edit/') ?>' + info.event.id;
            }
        },
        dateClick: function(info) {
            if (confirm('Créer un nouveau rendez-vous le ' + info.dateStr + '?')) {
                window.location.href = '<?= base_url('admin/appointments/create') ?>?date=' + info.dateStr;
            }
        },
        eventDidMount: function(info) {
            // Add tooltips
            info.el.title = info.event.extendedProps.type + ' - ' + info.event.extendedProps.status;
        }
    });
    
    calendar.render();
});
</script>

<style>
.appointment-item {
    transition: background-color 0.2s;
}
.appointment-item:hover {
    background-color: #f8f9fa;
}
.fc .fc-button-primary {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
.fc .fc-button-primary:hover {
    background-color: var(--primary-color);
    opacity: 0.9;
}
</style>

<?= $this->endSection() ?>
