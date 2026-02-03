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
}
