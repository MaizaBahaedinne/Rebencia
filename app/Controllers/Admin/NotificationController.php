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

        // Tout marquer lu en arrivant sur la page complète
        $this->model->markAllRead($userId);

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
        $notifs = $this->model->getUnread($userId, 8);
        $count  = $this->model->countUnread($userId);

        return $this->json([
            'count'         => $count,
            'notifications' => $notifs,
            'types'         => NotificationModel::TYPES,
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
}
