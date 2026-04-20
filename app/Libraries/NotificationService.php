<?php

namespace App\Libraries;

use App\Models\NotificationModel;
use App\Models\PushSubscriptionModel;

/**
 * NotificationService – Service centralisé pour :
 *   1. notifications internes (in-app)
 *   2. notifications push navigateur (Web Push / VAPID)
 *
 * Utilisation :
 *   $ns = new \App\Libraries\NotificationService();
 *   $ns->send($userId, 'Nouveau lead', 'Abdellah Bouzid a soumis une demande', 'lead', '/admin/leads/42');
 */
class NotificationService
{
    private NotificationModel     $notifModel;
    private PushSubscriptionModel $pushModel;

    // Clés VAPID chargées depuis .env
    private string $vapidPublicKey;
    private string $vapidPrivateKey;
    private string $vapidSubject;

    // Indique si la lib web-push est disponible
    private bool $pushEnabled;

    public function __construct()
    {
        $this->notifModel      = new NotificationModel();
        $this->pushModel       = new PushSubscriptionModel();

        $this->vapidPublicKey  = env('VAPID_PUBLIC_KEY',  '');
        $this->vapidPrivateKey = env('VAPID_PRIVATE_KEY', '');
        $this->vapidSubject    = env('VAPID_SUBJECT', 'mailto:admin@rebencia.com');

        // La lib Web Push est optionnelle pour ne pas bloquer l'app si non installée
        $this->pushEnabled = class_exists('\Minishlink\WebPush\WebPush')
                          && $this->vapidPublicKey !== ''
                          && $this->vapidPrivateKey !== '';
    }

    // ── API principale ───────────────────────────────────────────────

    /**
     * Envoie une notification in-app (base de données) à un ou plusieurs utilisateurs.
     * Déclenche aussi la notification push si disponible.
     *
     * @param int|array $userIds  ID utilisateur ou tableau d'IDs
     * @param string    $title    Titre court
     * @param string    $message  Corps du message
     * @param string    $type     info|success|warning|lead|property|task|system
     * @param string|null $url    Lien de redirection (relatif depuis base_url)
     * @param bool      $push     Envoyer aussi en push navigateur
     */
    public function send(
        int|array $userIds,
        string    $title,
        string    $message,
        string    $type   = 'info',
        ?string   $url    = null,
        bool      $push   = true
    ): void {
        // 1. Enregistrer en base
        $this->notifModel->notify($userIds, $title, $message, $type, $url);

        // 2. Envoyer en push si activé
        if ($push && $this->pushEnabled) {
            $ids          = is_array($userIds) ? $userIds : [$userIds];
            $subscriptions = $this->pushModel->getForUsers($ids);
            if (! empty($subscriptions)) {
                $this->sendPush($subscriptions, $title, $message, $url);
            }
        }
    }

    // ── Raccourcis sémantiques ───────────────────────────────────────

    public function notifyNewLead(int $agentId, string $leadName, int $leadId): void
    {
        $this->send(
            $agentId,
            'Nouveau lead',
            "{$leadName} a soumis une demande.",
            'lead',
            base_url("admin/leads/{$leadId}")
        );
    }

    public function notifyPropertyStatusChange(int $agentId, string $propTitle, string $newStatus, int $propId): void
    {
        $this->send(
            $agentId,
            'Statut bien modifié',
            "« {$propTitle} » est maintenant : {$newStatus}",
            'property',
            base_url("admin/properties/{$propId}")
        );
    }

    public function notifyTaskAssigned(int $assigneeId, string $taskTitle, int $taskId): void
    {
        $this->send(
            $assigneeId,
            'Tâche assignée',
            "Une nouvelle tâche vous a été attribuée : {$taskTitle}",
            'task',
            base_url("admin/tasks/{$taskId}")
        );
    }

    public function notifySystem(array $adminIds, string $message): void
    {
        $this->send($adminIds, 'Système', $message, 'system', null, false);
    }

    // ── Gestion des subscriptions push ──────────────────────────────

    /**
     * Enregistre ou met à jour une subscription push pour un utilisateur.
     */
    public function saveSubscription(int $userId, array $sub, ?string $userAgent = null): void
    {
        $endpoint   = $sub['endpoint']              ?? '';
        $publicKey  = $sub['keys']['p256dh']        ?? '';
        $authToken  = $sub['keys']['auth']           ?? '';

        if ($endpoint === '' || $publicKey === '' || $authToken === '') {
            return;
        }

        $this->pushModel->upsert($userId, $endpoint, $publicKey, $authToken, $userAgent);
    }

    /**
     * Supprime une subscription push (désabonnement ou endpoint expiré).
     */
    public function removeSubscription(string $endpoint): void
    {
        $this->pushModel->removeEndpoint($endpoint);
    }

    /**
     * Retourne la clé publique VAPID (envoyée au frontend pour l'abonnement).
     */
    public function getVapidPublicKey(): string
    {
        return $this->vapidPublicKey;
    }

    // ── Envoi Web Push (interne) ─────────────────────────────────────

    /**
     * Envoie une notification push à une liste de subscriptions.
     * Supprime automatiquement les endpoints invalides/expirés.
     *
     * @param array  $subscriptions  Lignes de push_subscriptions
     * @param string $title
     * @param string $body
     * @param string|null $url
     */
    private function sendPush(array $subscriptions, string $title, string $body, ?string $url): void
    {
        if (! $this->pushEnabled) {
            return;
        }

        $webPush = new \Minishlink\WebPush\WebPush([
            'VAPID' => [
                'subject'    => $this->vapidSubject,
                'publicKey'  => $this->vapidPublicKey,
                'privateKey' => $this->vapidPrivateKey,
            ],
        ]);

        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => base_url('icons/icon-192.png'),
            'badge' => base_url('icons/badge-96.png'),
            'url'   => $url ?? base_url('admin/notifications'),
            'tag'   => 'rebencia-' . md5($title . $body),
        ]);

        foreach ($subscriptions as $sub) {
            $subscription = \Minishlink\WebPush\Subscription::create([
                'endpoint'        => $sub['endpoint'],
                'keys'            => [
                    'p256dh' => $sub['public_key'],
                    'auth'   => $sub['auth_token'],
                ],
            ]);

            $webPush->queueNotification($subscription, $payload);
        }

        foreach ($webPush->flush() as $report) {
            $endpoint = $report->getRequest()->getUri()->__toString();
            if (! $report->isSuccess()) {
                // Endpoint invalide / expiré : supprimer de la base
                if ($report->isSubscriptionExpired()) {
                    $this->pushModel->removeEndpoint($endpoint);
                }
                log_message('warning', '[WebPush] Échec envoi : ' . $report->getReason() . ' — ' . $endpoint);
            } else {
                // Mettre à jour last_used_at
                $this->db()->table('push_subscriptions')
                    ->where('endpoint', $endpoint)
                    ->set('last_used_at', date('Y-m-d H:i:s'))
                    ->update();
            }
        }
    }

    private function db(): \CodeIgniter\Database\BaseConnection
    {
        return \Config\Database::connect();
    }
}
