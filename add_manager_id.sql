-- Migration: Add manager_id to users table
-- Date: 2026-05-03
-- Allows building org chart tree within a team

ALTER TABLE `users`
    ADD COLUMN `manager_id` INT UNSIGNED NULL DEFAULT NULL AFTER `agency_id`;

ALTER TABLE `users`
    ADD CONSTRAINT `fk_users_manager_id`
    FOREIGN KEY (`manager_id`) REFERENCES `users`(`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;
