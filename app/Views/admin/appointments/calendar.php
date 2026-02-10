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
    <div class="d-flex gap-2">
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
    <div class="col-lg-<?= $is_manager ? '8' : '9' ?>">
        <div class="card border-0 shadow-sm">
            <div class="card-body" style="min-height: 600px;">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <!-- Team Members Filter (Manager only) -->
    <?php if ($is_manager): ?>
    <div class="col-lg-2">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-users text-primary"></i> 
                    Mon équipe
                </h6>
            </div>
            <div class="card-body p-2">
                <div class="form-check mb-2">
                    <input class="form-check-input team-member-check" 
                           type="checkbox" 
                           value="<?= $current_user_id ?>" 
                           id="user_<?= $current_user_id ?>" 
                           checked>
                    <label class="form-check-label" for="user_<?= $current_user_id ?>">
                        <small>Moi</small>
                    </label>
                </div>
                <?php foreach ($team_members as $member): ?>
                    <div class="form-check mb-2">
                        <input class="form-check-input team-member-check" 
                               type="checkbox" 
                               value="<?= $member['id'] ?>" 
                               id="user_<?= $member['id'] ?>">
                        <label class="form-check-label" for="user_<?= $member['id'] ?>">
                            <small><?= esc($member['first_name'] . ' ' . $member['last_name']) ?></small>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Upcoming Appointments -->
    <div class="col-lg-<?= $is_manager ? '2' : '3' ?>">
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
                                <?php if (!empty($apt['agent_name'])): ?>
                                    <div>
                                        <i class="fas fa-user-tie"></i>
                                        <?= esc($apt['agent_name']) ?>
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
    
    function getSelectedUserIds() {
        var checkboxes = document.querySelectorAll('.team-member-check:checked');
        var ids = [];
        checkboxes.forEach(function(cb) {
            ids.push(cb.value);
        });
        return ids.join(',');
    }
    
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
        events: function(info, successCallback, failureCallback) {
            var userIds = getSelectedUserIds();
            fetch('<?= base_url('admin/appointments/getEvents') ?>?start=' + info.startStr + '&end=' + info.endStr + '&user_ids=' + userIds)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(() => {
                    alert('Erreur lors du chargement des rendez-vous');
                    failureCallback();
                });
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
            // Add tooltips with agent name
            var tooltip = info.event.extendedProps.type + ' - ' + info.event.extendedProps.status;
            if (info.event.extendedProps.agent_name) {
                tooltip += '\nAgent: ' + info.event.extendedProps.agent_name;
            }
            info.el.title = tooltip;
        }
    });
    
    calendar.render();
    
    // Reload calendar when checkboxes change
    <?php if ($is_manager): ?>
    document.querySelectorAll('.team-member-check').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            calendar.refetchEvents();
        });
    });
    <?php endif; ?>
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
