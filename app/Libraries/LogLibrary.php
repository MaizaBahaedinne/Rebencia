<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;
use App\Models\SystemLogModel;

/**
 * LogLibrary – Enregistrement centralisé des logs activité et système.
 */
class LogLibrary
{
    /**
     * Enregistre une action utilisateur dans activity_logs.
     */
    public function activity(
        string $action,
        string $module = '',
        string $entityType = '',
        ?int   $entityId = null,
        string $description = ''
    ): void {
        try {
            $model = new ActivityLogModel();
            $model->insert([
                'user_id'     => session()->get('user_id'),
                'action'      => $action,
                'module'      => $module,
                'entity_type' => $entityType,
                'entity_id'   => $entityId,
                'description' => $description,
                'ip_address'  => service('request')->getIPAddress(),
                'user_agent'  => substr((string) service('request')->getUserAgent(), 0, 500),
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // Ne pas bloquer l'application si le log échoue
            log_message('error', 'LogLibrary::activity failed: ' . $e->getMessage());
        }
    }

    /**
     * Enregistre un message dans system_logs.
     */
    public function system(
        string $level,
        string $message,
        array  $context = [],
        string $channel = 'app'
    ): void {
        try {
            $model = new SystemLogModel();
            $model->insert([
                'level'      => $level,
                'channel'    => $channel,
                'message'    => $message,
                'context'    => ! empty($context) ? json_encode($context) : null,
                'ip_address' => service('request')->getIPAddress(),
                'url'        => substr(current_url(), 0, 1000),
                'user_id'    => session()->get('user_id'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'LogLibrary::system failed: ' . $e->getMessage());
        }
    }

    // Raccourcis par niveau
    public function info(string $message, array $context = []): void
    {
        $this->system('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->system('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->system('error', $message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->system('critical', $message, $context);
    }
}
