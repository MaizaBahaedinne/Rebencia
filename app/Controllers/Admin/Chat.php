<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Chat extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Chat interface
     */
    public function index()
    {
        $userId = session()->get('user_id');
        
        $data = [
            'title' => 'Chat Interne',
            'page_title' => 'Messagerie',
            'users' => model('UserModel')->where('id !=', $userId)->findAll(),
            'conversations' => $this->getConversations($userId)
        ];

        return view('admin/chat/index', $data);
    }

    /**
     * Get user conversations
     */
    private function getConversations($userId)
    {
        return $this->db->query(
            "SELECT 
                conversation_id,
                MAX(created_at) as last_message_time,
                COUNT(*) as message_count,
                SUM(CASE WHEN is_read = 0 AND sender_id != ? THEN 1 ELSE 0 END) as unread_count
             FROM chat_messages
             WHERE conversation_id LIKE ?
             GROUP BY conversation_id
             ORDER BY last_message_time DESC",
            [$userId, '%' . $userId . '%']
        )->getResultArray();
    }

    /**
     * Get messages for conversation
     */
    public function getMessages()
    {
        $conversationId = $this->request->getGet('conversation_id');
        
        $messages = $this->db->table('chat_messages')
            ->select('chat_messages.*, CONCAT(users.first_name, " ", users.last_name) as sender_name')
            ->join('users', 'users.id = chat_messages.sender_id')
            ->where('conversation_id', $conversationId)
            ->orderBy('created_at', 'ASC')
            ->get()
            ->getResultArray();

        // Mark as read
        $this->db->table('chat_messages')
            ->where('conversation_id', $conversationId)
            ->where('sender_id !=', session()->get('user_id'))
            ->update(['is_read' => 1]);

        return $this->response->setJSON([
            'success' => true,
            'messages' => $messages
        ]);
    }

    /**
     * Send message
     */
    public function sendMessage()
    {
        $data = $this->request->getPost();
        $userId = session()->get('user_id');
        $recipientId = $data['recipient_id'];
        
        // Create conversation ID (sorted user IDs)
        $conversationId = min($userId, $recipientId) . '_' . max($userId, $recipientId);

        $messageData = [
            'conversation_id' => $conversationId,
            'sender_id' => $userId,
            'message' => $data['message'],
            'message_type' => 'text',
            'is_read' => 0
        ];

        $this->db->table('chat_messages')->insert($messageData);

        // Create notification for recipient
        $notificationModel = model('NotificationModel');
        $notificationModel->insert([
            'user_id' => $recipientId,
            'type' => 'info',
            'title' => 'Nouveau message',
            'message' => substr($data['message'], 0, 50) . '...',
            'link' => '/admin/chat'
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message_id' => $this->db->insertID()
        ]);
    }

    /**
     * Get unread count
     */
    public function getUnreadCount()
    {
        $userId = session()->get('user_id');
        
        $count = $this->db->table('chat_messages')
            ->where('sender_id !=', $userId)
            ->where('is_read', 0)
            ->where('conversation_id LIKE', '%' . $userId . '%')
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'count' => $count
        ]);
    }
}
