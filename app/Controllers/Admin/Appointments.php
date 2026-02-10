<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Appointments extends BaseController
{
    protected $appointmentModel;
    protected $clientModel;
    protected $propertyModel;
    protected $emailService;

    public function __construct()
    {
        $this->appointmentModel = model('AppointmentModel');
        $this->clientModel = model('ClientModel');
        $this->propertyModel = model('PropertyModel');
        $this->emailService = new \App\Libraries\EmailService();
    }

    /**
     * Calendar view
     */
    public function index()
    {
        $data = [
            'title' => 'Agenda & Rendez-vous',
            'page_title' => 'Calendrier',
            'upcoming' => $this->appointmentModel->getUpcoming(session()->get('user_id'), 10)
        ];

        return view('admin/appointments/calendar', $data);
    }

    /**
     * Get calendar events (AJAX)
     */
    public function getEvents()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $userId = $this->request->getGet('user_id') ?: session()->get('user_id');

        $events = $this->appointmentModel->getForCalendar($userId, $start, $end);

        return $this->response->setJSON($events);
    }

    /**
     * Create appointment
     */
    public function create()
    {
        $data = [
            'title' => 'Nouveau Rendez-vous',
            'page_title' => 'Créer un Rendez-vous',
            'clients' => $this->clientModel->findAll(),
            'properties' => $this->propertyModel->findAll()
        ];

        return view('admin/appointments/form', $data);
    }

    /**
     * Store appointment
     */
    public function store()
    {
        $data = $this->request->getPost();
        $data['user_id'] = session()->get('user_id');

        if (!$this->appointmentModel->insert($data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->appointmentModel->errors());
        }

        $appointmentId = $this->appointmentModel->getInsertID();

        // Send notification
        $this->createNotification($appointmentId, 'created');

        // Send email
        if (!empty($data['client_id'])) {
            $appointment = $this->appointmentModel->getWithRelations($appointmentId);
            $client = $this->clientModel->find($data['client_id']);
            
            if ($client && $client['email']) {
                $this->emailService->sendAppointmentReminder($appointment, $client['email'], $client['first_name'] . ' ' . $client['last_name']);
            }
        }

        return redirect()->to('admin/appointments')
            ->with('success', 'Rendez-vous créé avec succès');
    }

    /**
     * Edit appointment
     */
    public function edit($id)
    {
        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            return redirect()->back()->with('error', 'Rendez-vous non trouvé');
        }

        $data = [
            'title' => 'Modifier Rendez-vous',
            'page_title' => 'Modifier le Rendez-vous',
            'appointment' => $appointment,
            'clients' => $this->clientModel->findAll(),
            'properties' => $this->propertyModel->findAll()
        ];

        return view('admin/appointments/form', $data);
    }

    /**
     * Update appointment
     */
    public function update($id)
    {
        $data = $this->request->getPost();

        if (!$this->appointmentModel->update($id, $data)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->appointmentModel->errors());
        }

        return redirect()->to('admin/appointments')
            ->with('success', 'Rendez-vous modifié avec succès');
    }

    /**
     * Delete appointment
     */
    public function delete($id)
    {
        if (!$this->appointmentModel->delete($id)) {
            return redirect()->back()->with('error', 'Erreur lors de la suppression');
        }

        return redirect()->to('admin/appointments')
            ->with('success', 'Rendez-vous supprimé avec succès');
    }

    /**
     * Update status (AJAX)
     */
    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        if ($this->appointmentModel->update($id, ['status' => $status])) {
            $this->createNotification($id, 'status_changed');
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false], 400);
    }

    /**
     * Send reminders (cron job)
     */
    public function sendReminders()
    {
        $appointments = $this->appointmentModel->getNeedingReminders();

        $sent = 0;
        foreach ($appointments as $appointment) {
            // Send to agent
            if ($appointment['agent_email']) {
                $this->emailService->sendAppointmentReminder(
                    $appointment, 
                    $appointment['agent_email'], 
                    $appointment['agent_name']
                );
            }

            // Send to client if email exists
            if ($appointment['client_email']) {
                $this->emailService->sendAppointmentReminder(
                    $appointment, 
                    $appointment['client_email'], 
                    $appointment['client_name']
                );
            }

            // Mark as sent
            $this->appointmentModel->markReminderSent($appointment['id']);
            $sent++;
        }

        return $this->response->setJSON([
            'success' => true,
            'sent' => $sent
        ]);
    }

    /**
     * List view
     */
    public function list()
    {
        $appointments = $this->appointmentModel->getWithRelations();

        $data = [
            'title' => 'Liste Rendez-vous',
            'page_title' => 'Tous les Rendez-vous',
            'appointments' => $appointments
        ];

        return view('admin/appointments/list', $data);
    }

    /**
     * Create notification for appointment
     */
    private function createNotification($appointmentId, $action)
    {
        $notificationModel = model('NotificationModel');
        $appointment = $this->appointmentModel->getWithRelations($appointmentId);

        if (!$appointment) return;

        $messages = [
            'created' => 'Nouveau rendez-vous: ' . $appointment['title'],
            'status_changed' => 'Statut rendez-vous modifié: ' . $appointment['title']
        ];

        $notificationModel->insert([
            'user_id' => $appointment['user_id'],
            'type' => 'info',
            'title' => 'Rendez-vous',
            'message' => $messages[$action] ?? 'Rendez-vous mis à jour',
            'link' => '/admin/appointments'
        ]);
    }

    /**
     * Check availability for a time slot
     */
    public function checkAvailability()
    {
        $date = $this->request->getPost('date');
        $time = $this->request->getPost('time');
        $duration = $this->request->getPost('duration') ?: 60;
        $agentId = $this->request->getPost('agent_id');

        if (!$date || !$time) {
            return $this->response->setJSON([
                'available' => false,
                'message' => 'Date et heure requises'
            ]);
        }

        // Create datetime
        $startDateTime = $date . ' ' . $time;
        $endDateTime = date('Y-m-d H:i:s', strtotime($startDateTime . ' +' . $duration . ' minutes'));

        // Check for conflicts
        $conflicts = $this->appointmentModel
            ->where('date >=', $date)
            ->where('date <', date('Y-m-d', strtotime($date . ' +1 day')))
            ->where('user_id', $agentId)
            ->where('status !=', 'cancelled')
            ->findAll();

        $hasConflict = false;
        foreach ($conflicts as $appointment) {
            $existingStart = strtotime($appointment['date'] . ' ' . $appointment['time']);
            $existingEnd = strtotime($appointment['date'] . ' ' . $appointment['time'] . ' +60 minutes');
            $newStart = strtotime($startDateTime);
            $newEnd = strtotime($endDateTime);

            // Check overlap
            if (($newStart >= $existingStart && $newStart < $existingEnd) ||
                ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
                ($newStart <= $existingStart && $newEnd >= $existingEnd)) {
                $hasConflict = true;
                break;
            }
        }

        if ($hasConflict) {
            // Suggest alternative times
            $suggestions = [];
            for ($i = 9; $i <= 17; $i++) {
                $testTime = sprintf('%02d:00:00', $i);
                $testStart = strtotime($date . ' ' . $testTime);
                $testEnd = $testStart + ($duration * 60);
                
                $available = true;
                foreach ($conflicts as $appointment) {
                    $existingStart = strtotime($appointment['date'] . ' ' . $appointment['time']);
                    $existingEnd = strtotime($appointment['date'] . ' ' . $appointment['time'] . ' +60 minutes');
                    
                    if (($testStart >= $existingStart && $testStart < $existingEnd) ||
                        ($testEnd > $existingStart && $testEnd <= $existingEnd)) {
                        $available = false;
                        break;
                    }
                }
                
                if ($available) {
                    $suggestions[] = sprintf('%02d:00', $i);
                }
            }

            return $this->response->setJSON([
                'available' => false,
                'message' => 'Ce créneau est déjà réservé',
                'suggestions' => array_slice($suggestions, 0, 3)
            ]);
        }

        return $this->response->setJSON([
            'available' => true,
            'message' => 'Créneau disponible de ' . date('H:i', strtotime($time)) . ' à ' . date('H:i', strtotime($endDateTime))
        ]);
    }

    /**
     * Schedule a visit from property request
     */
    public function scheduleVisit()
    {
        $requestId = $this->request->getPost('request_id');
        $propertyId = $this->request->getPost('property_id');
        $clientId = $this->request->getPost('client_id');
        $visitDate = $this->request->getPost('visit_date');
        $startTime = $this->request->getPost('start_time');
        $duration = $this->request->getPost('duration') ?: 60;
        $agentId = $this->request->getPost('agent_id');
        $notes = $this->request->getPost('notes');

        // Get property and client info
        $property = $this->propertyModel->find($propertyId);
        $client = $this->clientModel->find($clientId);

        if (!$property || !$client) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Propriété ou client introuvable'
            ]);
        }

        // Create appointment
        $appointmentData = [
            'user_id' => $agentId,
            'client_id' => $clientId,
            'property_id' => $propertyId,
            'type' => 'visit',
            'title' => 'Visite - ' . $property['title'],
            'description' => 'Visite de la propriété ' . $property['reference'] . ' avec ' . $client['first_name'] . ' ' . $client['last_name'],
            'date' => $visitDate,
            'time' => $startTime,
            'duration' => $duration,
            'location' => $property['address'] ?? '',
            'status' => 'scheduled',
            'notes' => $notes
        ];

        try {
            $appointmentId = $this->appointmentModel->insert($appointmentData);

            if (!$appointmentId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erreur lors de la création du rendez-vous'
                ]);
            }

            // Update property request status
            $requestModel = model('PropertyRequestModel');
            $requestModel->update($requestId, [
                'status' => 'scheduled'
            ]);

            // Create notification
            $this->createNotification($appointmentId, 'created');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Visite planifiée avec succès',
                'appointment_id' => $appointmentId
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Schedule visit error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur technique: ' . $e->getMessage()
            ]);
        }
    }
}
