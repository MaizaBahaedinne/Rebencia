<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\NotificationService;

/**
 * PushController – Endpoints Web Push (VAPID).
 *
 * Routes (groupe 'api', filtre auth) :
 *   POST api/push/subscribe    → enregistrer une subscription navigateur
 *   POST api/push/unsubscribe  → supprimer une subscription
 *   GET  api/push/vapid-key    → retourne la clé publique VAPID (pour le JS frontend)
 *
 * Notes sécurité :
 *   - Toutes les entrées JSON sont validées avant utilisation.
 *   - Les routes sont derrière le filtre 'auth' → seuls les utilisateurs
 *     authentifiés peuvent s'abonner/se désabonner.
 */
class PushController extends BaseController
{
    private NotificationService $service;

    public function __construct()
    {
        $this->service = new NotificationService();
    }

    // ── Clé publique VAPID ───────────────────────────────────────────

    public function vapidKey()
    {
        return $this->json(['publicKey' => $this->service->getVapidPublicKey()]);
    }

    // ── Subscription ─────────────────────────────────────────────────

    public function subscribe()
    {
        $body = $this->request->getJSON(true);

        $endpoint  = $body['endpoint']            ?? null;
        $publicKey = $body['keys']['p256dh']      ?? null;
        $authToken = $body['keys']['auth']         ?? null;

        if (! $endpoint || ! $publicKey || ! $authToken) {
            return $this->json(['error' => 'Données de subscription incomplètes.'], 422);
        }

        // Valider l'URL de l'endpoint (SSRF prevention)
        $parsed = parse_url($endpoint);
        if (($parsed['scheme'] ?? '') !== 'https') {
            return $this->json(['error' => 'Endpoint invalide.'], 422);
        }

        $userAgent = $this->request->getUserAgent()->getAgentString();

        $this->service->saveSubscription(
            $this->auth->id(),
            [
                'endpoint' => $endpoint,
                'keys'     => ['p256dh' => $publicKey, 'auth' => $authToken],
            ],
            $userAgent
        );

        return $this->json(['success' => true]);
    }

    // ── Unsubscribe ───────────────────────────────────────────────────

    public function unsubscribe()
    {
        $body     = $this->request->getJSON(true);
        $endpoint = $body['endpoint'] ?? null;

        if (! $endpoint) {
            return $this->json(['error' => 'Endpoint manquant.'], 422);
        }

        $this->service->removeSubscription($endpoint);

        return $this->json(['success' => true]);
    }
}
