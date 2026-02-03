<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title', 'description', 'appointment_type', 'scheduled_at', 'duration',
        'location', 'status', 'user_id', 'client_id', 'property_id', 
        'transaction_id', 'reminder_sent', 'reminder_sent_at', 'notes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[200]',
        'scheduled_at' => 'required|valid_date',
        'user_id' => 'required|is_natural_no_zero'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Le titre est obligatoire',
            'min_length' => 'Le titre doit contenir au moins 3 caractères'
        ],
        'scheduled_at' => [
            'required' => 'La date et l\'heure sont obligatoires',
            'valid_date' => 'Format de date invalide'
        ]
    ];

    /**
     * Get appointments with related data
     */
    public function getWithRelations($id = null)
    {
        $builder = $this->select('appointments.*, 
            CONCAT(users.first_name, " ", users.last_name) as agent_name,
            CONCAT(clients.first_name, " ", clients.last_name) as client_name,
            properties.title as property_title,
            properties.reference as property_reference,
            transactions.reference as transaction_reference')
            ->join('users', 'users.id = appointments.user_id', 'left')
            ->join('clients', 'clients.id = appointments.client_id', 'left')
            ->join('properties', 'properties.id = appointments.property_id', 'left')
            ->join('transactions', 'transactions.id = appointments.transaction_id', 'left');

        if ($id) {
            return $builder->where('appointments.id', $id)->first();
        }

        return $builder->orderBy('appointments.scheduled_at', 'DESC')->findAll();
    }

    /**
     * Get appointments for calendar (FullCalendar format)
     */
    public function getForCalendar($userId = null, $start = null, $end = null)
    {
        $builder = $this->select('appointments.id, appointments.title, 
            appointments.scheduled_at as start, 
            appointments.status, appointments.appointment_type,
            CONCAT(clients.first_name, " ", clients.last_name) as client_name')
            ->join('clients', 'clients.id = appointments.client_id', 'left');

        if ($userId) {
            $builder->where('appointments.user_id', $userId);
        }

        if ($start && $end) {
            $builder->where('appointments.scheduled_at >=', $start);
            $builder->where('appointments.scheduled_at <=', $end);
        }

        $appointments = $builder->findAll();

        // Format for FullCalendar
        $events = [];
        foreach ($appointments as $apt) {
            $colors = [
                'scheduled' => '#ffc107',
                'confirmed' => '#0d6efd',
                'completed' => '#198754',
                'cancelled' => '#dc3545',
                'no_show' => '#6c757d'
            ];

            $events[] = [
                'id' => $apt['id'],
                'title' => $apt['title'] . ($apt['client_name'] ? ' - ' . $apt['client_name'] : ''),
                'start' => $apt['start'],
                'backgroundColor' => $colors[$apt['status']] ?? '#6c757d',
                'borderColor' => $colors[$apt['status']] ?? '#6c757d',
                'extendedProps' => [
                    'type' => $apt['appointment_type'],
                    'status' => $apt['status']
                ]
            ];
        }

        return $events;
    }

    /**
     * Get upcoming appointments
     */
    public function getUpcoming($userId = null, $limit = 5)
    {
        $builder = $this->select('appointments.*, 
            CONCAT(clients.first_name, " ", clients.last_name) as client_name,
            properties.title as property_title')
            ->join('clients', 'clients.id = appointments.client_id', 'left')
            ->join('properties', 'properties.id = appointments.property_id', 'left')
            ->where('appointments.scheduled_at >', date('Y-m-d H:i:s'))
            ->where('appointments.status', 'scheduled')
            ->orWhere('appointments.status', 'confirmed');

        if ($userId) {
            $builder->where('appointments.user_id', $userId);
        }

        return $builder->orderBy('appointments.scheduled_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get appointments needing reminders (24h before)
     */
    public function getNeedingReminders()
    {
        return $this->select('appointments.*, 
            users.email as agent_email,
            CONCAT(users.first_name, " ", users.last_name) as agent_name,
            clients.email as client_email,
            CONCAT(clients.first_name, " ", clients.last_name) as client_name')
            ->join('users', 'users.id = appointments.user_id')
            ->join('clients', 'clients.id = appointments.client_id', 'left')
            ->where('appointments.reminder_sent', 0)
            ->where('appointments.status', 'scheduled')
            ->orWhere('appointments.status', 'confirmed')
            ->where('appointments.scheduled_at <=', date('Y-m-d H:i:s', strtotime('+24 hours')))
            ->where('appointments.scheduled_at >', date('Y-m-d H:i:s'))
            ->findAll();
    }

    /**
     * Mark reminder as sent
     */
    public function markReminderSent($id)
    {
        return $this->update($id, [
            'reminder_sent' => 1,
            'reminder_sent_at' => date('Y-m-d H:i:s')
        ]);
    }
}
