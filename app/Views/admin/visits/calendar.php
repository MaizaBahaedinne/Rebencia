<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- ── EN-TÊTE ───────────────────────────────────────────────────────────── -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-calendar3 me-2 text-primary"></i>Calendrier des visites</h4>
        <p class="text-muted small mb-0">Vue par jour, semaine ou mois</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/visits') ?>" class="btn btn-sm btn-light">
            <i class="bi bi-list-ul me-1"></i>Liste
        </a>
        <a href="<?= base_url('admin/visits/create') ?>" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Nouvelle visite
        </a>
    </div>
</div>

<!-- ── FILTRE AGENT ───────────────────────────────────────────────────────── -->
<div class="card shadow-sm mb-3">
    <div class="card-body py-2 d-flex align-items-center gap-3">
        <label class="form-label mb-0 small fw-semibold text-nowrap">Filtrer par agent :</label>
        <select id="agentFilter" class="form-select form-select-sm" style="max-width:260px">
            <option value="0">Tous les agents</option>
            <?php foreach ($agents as $ag): ?>
            <option value="<?= $ag['id'] ?>">
                <?= esc($ag['first_name'] . ' ' . $ag['last_name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <!-- Légende des statuts -->
        <div class="d-flex gap-2 ms-auto flex-wrap">
            <?php
            $statusColors = [
                'Planifiée'   => '#6c757d',
                'Confirmée'   => '#0d6efd',
                'Effectuée'   => '#198754',
                'Annulée'     => '#dc3545',
                'Replanifiée' => '#ffc107',
            ];
            foreach ($statusColors as $lbl => $hex): ?>
            <span class="small d-flex align-items-center gap-1">
                <span class="d-inline-block rounded-circle" style="width:10px;height:10px;background:<?= $hex ?>"></span>
                <?= $lbl ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── CALENDRIER ────────────────────────────────────────────────────────── -->
<div class="card shadow-sm">
    <div class="card-body p-3">
        <div id="visitsCalendar"></div>
    </div>
</div>

<!-- FullCalendar v6 -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>

<script>
(function () {
    'use strict';

    const BASE_URL     = '<?= base_url('/') ?>';
    let   currentAgent = 0;
    let   calendar;

    document.addEventListener('DOMContentLoaded', function () {

        const calEl = document.getElementById('visitsCalendar');

        calendar = new FullCalendar.Calendar(calEl, {
            locale: 'fr',
            initialView: 'timeGridWeek',
            headerToolbar: {
                left:   'prev,next today',
                center: 'title',
                right:  'dayGridMonth,timeGridWeek,timeGridDay,listWeek',
            },
            height:      680,
            slotMinTime: '07:00:00',
            slotMaxTime: '20:00:00',
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5, 6],
                startTime:  '08:00',
                endTime:    '18:00',
            },
            eventTimeFormat: {
                hour:   '2-digit',
                minute: '2-digit',
                meridiem: false,
            },
            nowIndicator: true,
            navLinks: true,

            events: function (info, successCallback, failureCallback) {
                const url = BASE_URL + 'admin/visits/calendar-events'
                          + '?start='    + info.startStr.substring(0, 10)
                          + '&end='      + info.endStr.substring(0, 10)
                          + '&agent_id=' + currentAgent;
                fetch(url)
                    .then(function (r) { return r.json(); })
                    .then(successCallback)
                    .catch(failureCallback);
            },

            eventClick: function (info) {
                info.jsEvent.preventDefault();
                if (info.event.url) {
                    window.location.href = info.event.url;
                }
            },

            eventDidMount: function (info) {
                // Tooltip Bootstrap avec agent + ville
                const props  = info.event.extendedProps;
                const tipText = (props.agent || '') + (props.city ? ' — ' + props.city : '');
                if (tipText) {
                    info.el.setAttribute('title', tipText);
                    info.el.setAttribute('data-bs-toggle', 'tooltip');
                    new bootstrap.Tooltip(info.el, { trigger: 'hover' });
                }
            },
        });

        calendar.render();

        // Filtre agent
        document.getElementById('agentFilter').addEventListener('change', function () {
            currentAgent = parseInt(this.value, 10) || 0;
            calendar.refetchEvents();
        });

    });

})();
</script>

<style>
.fc .fc-button-primary        { background-color: #0d6efd; border-color: #0d6efd; }
.fc .fc-button-primary:hover  { background-color: #0b5ed7; border-color: #0b5ed7; }
.fc .fc-button-active         { background-color: #0a58ca !important; }
.fc-event                     { cursor: pointer; font-size: .8rem; }
.fc-daygrid-event             { border-radius: 4px; padding: 1px 4px; }
</style>

<?= $this->endSection() ?>
