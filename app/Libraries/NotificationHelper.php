<?php

namespace App\Libraries;

class NotificationHelper
{
    protected $notificationModel;
    protected $emailService;

    public function __construct()
    {
        $this->notificationModel = model('NotificationModel');
        $this->emailService = new \App\Libraries\EmailService();
        // La logique de notification pour les agents doit être appelée via une méthode dédiée
        // Exemple : $helper->notifyPropertyCreated($propertyId, $propertyData, $creatorId);
    }

    /**
     * Notifier tous les agents de l'agence lors de la création d'un bien
     */
    public function notifyPropertyCreated($propertyId, $propertyData, $creatorId)
    {
        $userModel = model('UserModel');
        $creator = $userModel->find($creatorId);
        if ($creator && $creator['agency_id']) {
            $agents = $userModel->where('agency_id', $creator['agency_id'])
                ->where('status', 'active')
                ->where('id !=', $creatorId)
                ->findAll();
            foreach ($agents as $agent) {
                $this->notificationModel->createNotification(
                    $agent['id'],
                    'info',
                    'Nouveau bien ajouté',
                    'Un nouveau bien a été ajouté: ' . $propertyData['title'],
                    '/admin/properties/view/' . $propertyId,
                    'fa-building'
                );
            }
        }
    }

    /**
     * Send notification when client is created
     */
    public function notifyClientCreated($clientId, $clientData, $creatorId)
    {
        // Notify assigned agent if different from creator
        if (isset($clientData['agent_id']) && $clientData['agent_id'] != $creatorId) {
            $this->notificationModel->createNotification(
                $clientData['agent_id'],
                'success',
                'Nouveau client assigné',
                'Un nouveau client vous a été assigné: ' . $clientData['full_name'],
                '/admin/clients/view/' . $clientId,
                'fa-user'
            );
        }
    }

    /**
     * Send notification when transaction is created
     */
    public function notifyTransactionCreated($transactionId, $transactionData, $creatorId)
    {
        // Notify agent
        if (isset($transactionData['agent_id'])) {
            $this->notificationModel->createNotification(
                $transactionData['agent_id'],
                'success',
                'Nouvelle transaction créée',
                'Une nouvelle transaction a été enregistrée',
                '/admin/transactions/view/' . $transactionId,
                'fa-file-invoice-dollar'
            );
        }

        // Notify agency manager
        $userModel = model('UserModel');
        $agent = $userModel->find($transactionData['agent_id']);
        if ($agent && $agent['manager_id']) {
            $this->notificationModel->createNotification(
                $agent['manager_id'],
                'info',
                'Transaction de votre équipe',
                'Une transaction a été créée par votre équipe',
                '/admin/transactions/view/' . $transactionId,
                'fa-chart-line'
            );
        }
    }

    /**
     * Send notification when transaction status changes
     */
    public function notifyTransactionStatusChanged($transactionId, $oldStatus, $newStatus, $transactionData)
    {
        $statusLabels = [
            'pending' => 'En attente',
            'in_progress' => 'En cours',
            'completed' => 'Complétée',
            'cancelled' => 'Annulée'
        ];

        $type = $newStatus === 'completed' ? 'success' : ($newStatus === 'cancelled' ? 'danger' : 'info');

        // Notify agent
        if (isset($transactionData['agent_id'])) {
            $this->notificationModel->createNotification(
                $transactionData['agent_id'],
                $type,
                'Statut transaction modifié',
                'Transaction passée de "' . $statusLabels[$oldStatus] . '" à "' . $statusLabels[$newStatus] . '"',
                '/admin/transactions/view/' . $transactionId,
                'fa-exchange-alt'
            );
        }
    }

    /**
     * Send notification when commission is approved
     */
    public function notifyCommissionApproved($commissionId, $commissionData)
    {
        $this->notificationModel->createNotification(
            $commissionData['user_id'],
            'success',
            'Commission approuvée',
            'Votre commission de ' . number_format($commissionData['amount'], 0, ',', ' ') . ' TND a été approuvée',
            '/admin/commissions',
            'fa-check-circle'
        );
    }

    /**
     * Send notification when commission is paid
     */
    public function notifyCommissionPaid($commissionId, $commissionData)
    {
        $this->notificationModel->createNotification(
            $commissionData['user_id'],
            'success',
            'Commission payée',
            'Votre commission de ' . number_format($commissionData['amount'], 0, ',', ' ') . ' TND a été payée',
            '/admin/commissions',
            'fa-money-bill-wave'
        );
    }

    /**
     * Send notification for property matches client preferences
     */
    public function notifyPropertyMatchesClient($propertyId, $propertyData, $clientId, $clientData)
    {
        // Notify agent about potential match
        if (isset($clientData['agent_id'])) {
            $this->notificationModel->createNotification(
                $clientData['agent_id'],
                'info',
                'Bien correspondant à un client',
                'Le bien "' . $propertyData['title'] . '" correspond aux préférences de ' . $clientData['full_name'],
                '/admin/properties/view/' . $propertyId,
                'fa-star'
            );
        }
    }

    /**
     * Send notification reminder for follow-up
     */
    public function notifyFollowUpReminder($clientId, $clientData, $daysInactive)
    {
        if (isset($clientData['agent_id'])) {
            $this->notificationModel->createNotification(
                $clientData['agent_id'],
                'warning',
                'Rappel de suivi client',
                'Le client ' . $clientData['full_name'] . ' n\'a pas été contacté depuis ' . $daysInactive . ' jours',
                '/admin/clients/view/' . $clientId,
                'fa-bell'
            );
        }
    }

    /**
     * Check and notify for property-client matches
     */
    public function checkPropertyClientMatches($propertyId, $propertyData)
    {
        // TODO: Uncomment when search_preferences column is added to clients table
        /*
        $clientModel = model('ClientModel');
        $clients = $clientModel->where('status', 'active')
                               ->where('search_preferences IS NOT NULL')
                               ->where('search_preferences !=', '')
                               ->findAll();

        foreach ($clients as $client) {
            $preferences = json_decode($client['search_preferences'], true);
            if (!$preferences) continue;

            $isMatch = true;

            // Check type
            if (isset($preferences['property_types']) && !in_array($propertyData['type'], $preferences['property_types'])) {
                $isMatch = false;
            }

            // Check price range
            if (isset($preferences['budget_min']) && $propertyData['price'] < $preferences['budget_min']) {
                $isMatch = false;
            }
            if (isset($preferences['budget_max']) && $propertyData['price'] > $preferences['budget_max']) {
                $isMatch = false;
            }

            // Check zones
            if (isset($preferences['zones']) && !in_array($propertyData['zone_id'], $preferences['zones'])) {
                $isMatch = false;
            }

            if ($isMatch) {
                $this->notifyPropertyMatchesClient($propertyId, $propertyData, $client['id'], $client);
            }
        }
        */
    }
}
