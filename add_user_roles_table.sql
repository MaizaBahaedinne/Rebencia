-- Migration : table pivot user_roles (multi-rôles par utilisateur)
-- Exécuter : mysql -u root rebencia < add_user_roles_table.sql

CREATE TABLE IF NOT EXISTS `user_roles` (
  `id`         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED    NOT NULL,
  `role_id`    INT UNSIGNED    NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY  `uq_user_role` (`user_id`, `role_id`),
  CONSTRAINT `ur_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `ur_role_fk` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Peupler à partir des rôles existants (rôle principal de chaque user)
INSERT IGNORE INTO `user_roles` (`user_id`, `role_id`)
SELECT `id`, `role_id` FROM `users` WHERE `deleted_at` IS NULL AND `role_id` IS NOT NULL;
