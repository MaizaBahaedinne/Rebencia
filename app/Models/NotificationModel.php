<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table          = 'notifications';
    protected $primaryKey     = 'id';
    protected $useTimestamps  = false; // géré manuellement
    protected $allowedFields  = [
        'user_id', 'type', 'title', 'message', 'url', 'is_read', 'read_at', 'created_at',
    ];

    // ── Types disponibles ────────────────────────────────────────────
    public const TYPES = [
        'info'     => ['icon' => 'bi-info-circle-fill',    'color' => 'text-primary'],
        'success'  => ['icon' => 'bi-check-circle-fill',   'color' => 'text-success'],
        'warning'  => ['icon' => 'bi-exclamation-triangle-fill', 'color' => 'text-warning'],
        'lead'     => ['icon' => 'bi-person-lines-fill',   'color' => 'text-info'],
        'property' => ['icon' => 'bi-building-fill',       'color' => 'text-secondary'],
        'task'     => ['icon' => 'bi-kanban-fill',         'color' => 'text-purple'],
        'system'   => ['icon' => 'bi-gear-fill',           'color' => 'text-dark'],
    ];

    // ── Getters ──────────────────────────────────────────────────────

    /**
     * Notifications non lues d'un utilisateur (pour le badge et dropdown).
     */
    public function getUnread(int $userId, int $limit = 10): array
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Toutes les notifications paginées.
     */
    public function getForUser(int $userId, int $perPage = 20): array
    {
        $total = $this->where('user_id', $userId)->countAllResults(false);
        $rows  = $this->where('user_id', $userId)
                      ->orderBy('created_at', 'DESC')
                      ->paginate($perPage);

        return ['rows' => $rows, 'total' => $total, 'pager' => $this->pager];
    }

    /**
     * Nombre de notifications non lues, mis en cache 60s.
     */
    public function countUnread(int $userId): int
    {
        return (int) $this->where('user_id', $userId)
                          ->where('is_read', 0)
                          ->countAllResults();
    }

    // ── Actions ──────────────────────────────────────────────────────

    /**
     * Crée une notification pour un ou plusieurs utilisateurs.
     */
    public function notify(int|array $userIds, string $title, string $message, string $type = 'info', ?string $url = null): void
    {
        $ids = is_array($userIds) ? $userIds : [$userIds];
        $now = date('Y-m-d H:i:s');

        $rows = [];
        foreach ($ids as $uid) {
            $rows[] = [
                'user_id'    => (int) $uid,
                'type'       => $type,
                'title'      => $title,
                'message'    => $message,
                'url'        => $url,
                'is_read'    => 0,
                'created_at' => $now,
            ];
        }

        $this->insertBatch($rows);
    }

    /**
     * Marque une notification comme lue (vérifie l'appartenance à l'utilisateur).
     */
    public function markRead(int $id, int $userId): bool
    {
        return $this->where('id', $id)
                    ->where('user_id', $userId)
                    ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
                    ->update() > 0;
    }

    /**
     * Marque toutes les notifications d'un utilisateur comme lues.
     */
    public function markAllRead(int $userId): void
    {
        $this->where('user_id', $userId)
             ->where('is_read', 0)
             ->set(['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')])
             ->update();
    }

    /**
     * Supprime les notifications de plus de $days jours.
     */
    public function pruneOld(int $days = 30): int
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        $this->where('created_at <', $cutoff)->delete();
        return $this->db->affectedRows();
    }
}
