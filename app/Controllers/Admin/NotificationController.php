<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NotificationModel;
use App\Libraries\NotificationService;

/**
 * NotificationController – Gestion des notifications in-app et push.
 *
 * Routes admin (auth filtre appliqué) :
 *   GET  admin/notifications            → liste paginée
 *   GET  admin/notifications/unread     → JSON badge + dropdown (AJAX)
 *   POST admin/notifications/:id/read   → marquer une comme lue
 *   POST admin/notifications/read-all   → tout marquer comme lu
 *   POST admin/notifications/:id/delete → supprimer
 *
 * Routes API (pas de vue, retourne JSON) :
 *   POST api/push/subscribe    → enregistrer subscription push
 *   POST api/push/unsubscribe  → supprimer subscription push
 *   GET  api/push/vapid-key    → retourne la clé publique VAPID
 */
class NotificationController extends BaseController
{
    private NotificationModel   $model;
    private NotificationService $service;

    public function __construct()
    {
        $this->model   = new NotificationModel();
        $this->service = new NotificationService();
    }

    // ── Vue liste ────────────────────────────────────────────────────

    public function index(): string
    {
        $userId = $this->auth->id();
        $result = $this->model->getForUser($userId, 20);

        return $this->render('admin/notifications/index', [
            'page_title' => 'Notifications',
            'rows'       => $result['rows'],
            'pager'      => $result['pager'],
            'types'      => NotificationModel::TYPES,
        ]);
    }

    // ── AJAX : données pour le dropdown ─────────────────────────────

    /**
     * Retourne les X dernières non-lues + le compteur total.
     * Appelé périodiquement depuis le frontend.
     */
    public function unread()
    {
        $userId = $this->auth->id();
        // Retourne les 8 dernières (lues + non-lues) pour que le dropdown
        // ne soit jamais vide, + le compteur pour le badge
        $notifs = $this->model->getRecent($userId, 8);
        $count  = $this->model->countUnread($userId);

        return $this->json([
            'count'         => $count,
            'notifications' => $notifs,
        ]);
    }

    // ── Actions ──────────────────────────────────────────────────────

    public function markRead(int $id)
    {
        $this->model->markRead($id, $this->auth->id());
        return $this->json(['success' => true]);
    }

    public function markAllRead()
    {
        $this->model->markAllRead($this->auth->id());
        return $this->json(['success' => true]);
    }

    public function delete(int $id)
    {
        $notif = $this->model->find($id);
        if ($notif && (int) $notif['user_id'] === $this->auth->id()) {
            $this->model->delete($id);
        }
        return redirect()->to(base_url('admin/notifications'))->with('success', 'Notification supprimée.');
    }

    // ── Test / seed ──────────────────────────────────────────────────

    /**
     * Insère une notification de chaque type pour l'utilisateur connecté.
     * À utiliser en DEV uniquement — supprimer la route en production.
     */
    public function seedTest()
    {
        $userId = $this->auth->id();
        $now    = date('Y-m-d H:i:s');

        $samples = [
            [
                'type'    => 'info',
                'title'   => 'Bienvenue sur Rebencia',
                'message' => 'Votre compte a été activé avec succès. Bonne navigation !',
                'url'     => '/admin/dashboard',
            ],
            [
                'type'    => 'success',
                'title'   => 'Bien publié avec succès',
                'message' => 'Le bien « Villa Carthage F5 » est maintenant visible sur le site.',
                'url'     => '/admin/properties',
            ],
            [
                'type'    => 'warning',
                'title'   => 'Contrat expirant bientôt',
                'message' => 'Le mandat du bien #42 expire dans 7 jours. Pensez à le renouveler.',
                'url'     => '/admin/properties/42',
            ],
            [
                'type'    => 'lead',
                'title'   => 'Nouveau lead assigné',
                'message' => 'Karim Benali (06 12 34 56 78) cherche un appartement F3 à Tunis.',
                'url'     => '/admin/leads',
            ],
            [
                'type'    => 'property',
                'title'   => 'Bien modifié par un agent',
                'message' => 'L\'agent Sara Mansouri a mis à jour le prix du bien « Appart Lac 3 ».',
                'url'     => '/admin/properties',
            ],
            [
                'type'    => 'task',
                'title'   => 'Tâche assignée',
                'message' => 'Vous avez été assigné à la tâche « Appeler client Bouzid » (échéance demain).',
                'url'     => '/admin/tasks',
            ],
            [
                'type'    => 'system',
                'title'   => 'Mise à jour système effectuée',
                'message' => 'La version 2.4.1 a été déployée avec succès. Voir le changelog.',
                'url'     => '/admin/system/deploy',
            ],
        ];

        $rows = [];
        foreach ($samples as $i => $s) {
            $rows[] = [
                'user_id'    => $userId,
                'type'       => $s['type'],
                'title'      => $s['title'],
                'message'    => $s['message'],
                'url'        => $s['url'],
                'is_read'    => 0,
                'read_at'    => null,
                'created_at' => date('Y-m-d H:i:s', strtotime($now) - ($i * 600)),
            ];
        }

        $this->model->insertBatch($rows);

        return redirect()->to(base_url('admin/notifications'))
                         ->with('success', count($rows) . ' notifications de test insérées.');
    }
}
