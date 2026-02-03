<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationsSeeder extends Seeder
{
    public function run()
    {
        // Create notifications table if not exists
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `notifications` (
                `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                `user_id` int(10) unsigned NOT NULL,
                `type` enum('info','success','warning','danger') DEFAULT 'info',
                `title` varchar(255) NOT NULL,
                `message` text,
                `link` varchar(255) DEFAULT NULL,
                `icon` varchar(50) DEFAULT NULL,
                `is_read` tinyint(1) DEFAULT 0,
                `created_at` timestamp NULL DEFAULT current_timestamp(),
                `read_at` timestamp NULL DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `user_id` (`user_id`),
                KEY `is_read` (`is_read`),
                CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }
}
