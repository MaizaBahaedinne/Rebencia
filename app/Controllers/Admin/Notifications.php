<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = model('NotificationModel');
    }

    /**
     * Get notifications for current user (AJAX)
     */
    public function index()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/dashboard');
        }

        $userId = session()->get('user_id');
        $notifications = $this->notificationModel->getAllForUser($userId, 20);
        $unreadCount = $this->notificationModel->countUnread($userId);

        return $this->response->setJSON([
            'success' => true,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/dashboard');
        }

        $userId = session()->get('user_id');
        $notification = $this->notificationModel->find($id);

        if (!$notification || $notification['user_id'] != $userId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Notification non trouvée']);
        }

        $this->notificationModel->markAsRead($id);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/dashboard');
        }

        $userId = session()->get('user_id');
        $this->notificationModel->markAllAsRead($userId);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Get unread count (AJAX)
     */
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/admin/dashboard');
        }

        $userId = session()->get('user_id');
        $count = $this->notificationModel->countUnread($userId);

        return $this->response->setJSON([
            'success' => true,
            'count' => $count
        ]);
    }
}
