<?php

namespace App\Models;

use CodeIgniter\Model;

class PushSubscriptionModel extends Model
{
    protected $table         = 'push_subscriptions';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'endpoint', 'public_key', 'auth_token', 'user_agent',
        'created_at', 'last_used_at',
    ];

    /**
     * Upsert : si l'endpoint existe déjà pour cet utilisateur, met à jour les clés.
     * Évite les doublons quand le navigateur renouvelle la subscription.
     */
    public function upsert(int $userId, string $endpoint, string $publicKey, string $authToken, ?string $userAgent = null): void
    {
        $existing = $this->where('user_id', $userId)
                         ->where('endpoint', $endpoint)
                         ->first();

        $now = date('Y-m-d H:i:s');

        if ($existing) {
            $this->update($existing['id'], [
                'public_key'   => $publicKey,
                'auth_token'   => $authToken,
                'user_agent'   => $userAgent,
                'last_used_at' => $now,
            ]);
        } else {
            $this->insert([
                'user_id'    => $userId,
                'endpoint'   => $endpoint,
                'public_key' => $publicKey,
                'auth_token' => $authToken,
                'user_agent' => $userAgent,
                'created_at' => $now,
            ]);
        }
    }

    /**
     * Toutes les subscriptions actives d'un utilisateur.
     */
    public function getForUser(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }

    /**
     * Supprime un endpoint (désabonnement ou endpoint invalide).
     */
    public function removeEndpoint(string $endpoint): void
    {
        $this->where('endpoint', $endpoint)->delete();
    }

    /**
     * Subscriptions de plusieurs utilisateurs (pour envoi groupé).
     */
    public function getForUsers(array $userIds): array
    {
        if (empty($userIds)) {
            return [];
        }
        return $this->whereIn('user_id', $userIds)->findAll();
    }
}
