-- ============================================================
-- Script de création des tables tasks + task_comments
-- À exécuter via phpMyAdmin sur rebe_RebenciaDB
-- ============================================================

-- 1. Table tasks
CREATE TABLE IF NOT EXISTS `tasks` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `reference`   VARCHAR(30)  NULL,
  `title`       VARCHAR(255) NOT NULL,
  `description` TEXT         NULL,
  `type`        ENUM('bug','feature','improvement','task','question') NOT NULL DEFAULT 'task',
  `status`      ENUM('backlog','todo','in_progress','review','done','cancelled') NOT NULL DEFAULT 'todo',
  `priority`    ENUM('low','medium','high','critical') NOT NULL DEFAULT 'medium',
  `created_by`  INT UNSIGNED NOT NULL,
  `assigned_to` INT UNSIGNED NULL,
  `due_date`    DATE         NULL,
  `labels`      VARCHAR(255) NULL,
  `created_at`  DATETIME     NULL,
  `updated_at`  DATETIME     NULL,
  `deleted_at`  DATETIME     NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_status`      (`status`),
  INDEX `idx_priority`    (`priority`),
  INDEX `idx_assigned_to` (`assigned_to`),
  CONSTRAINT `fk_tasks_created_by`  FOREIGN KEY (`created_by`)  REFERENCES `users`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_tasks_assigned_to` FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`) ON DELETE SET NULL  ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table task_comments
CREATE TABLE IF NOT EXISTS `task_comments` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `task_id`    INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `content`    TEXT         NOT NULL,
  `created_at` DATETIME     NULL,
  `updated_at` DATETIME     NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_task_id` (`task_id`),
  CONSTRAINT `fk_tc_task` FOREIGN KEY (`task_id`) REFERENCES `tasks`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_tc_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Permissions tasks
INSERT IGNORE INTO `permissions` (`name`, `label`, `module`, `created_at`) VALUES
  ('tasks.view',   'Voir les tâches',      'tasks', NOW()),
  ('tasks.create', 'Créer des tâches',     'tasks', NOW()),
  ('tasks.edit',   'Modifier des tâches',  'tasks', NOW()),
  ('tasks.delete', 'Supprimer des tâches', 'tasks', NOW());

-- 4. Assigner permissions aux rôles (super_admin, admin, director = tout)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name IN ('super_admin','admin','director') AND p.module = 'tasks';

-- 5. expert + coordinator = view, create, edit
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name IN ('expert','coordinator') AND p.name IN ('tasks.view','tasks.create','tasks.edit');

-- 6. collaborator = view seulement
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `created_at`)
SELECT r.id, p.id, NOW()
FROM `permissions` p
CROSS JOIN `roles` r
WHERE r.name = 'collaborator' AND p.name = 'tasks.view';

-- 7. Marquer les migrations comme exécutées dans CI4
SET @next_batch = (SELECT IFNULL(MAX(batch), 0) + 1 FROM migrations);
INSERT IGNORE INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
  ('2026-04-19-110000', 'App\\Database\\Migrations\\CreateTasksTable',    'default', 'App', UNIX_TIMESTAMP(), @next_batch),
  ('2026-04-19-120000', 'App\\Database\\Migrations\\AddTasksPermissions', 'default', 'App', UNIX_TIMESTAMP(), @next_batch);
