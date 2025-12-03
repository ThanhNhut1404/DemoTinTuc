-- Migration: add reset_token and reset_token_expiry to users/nguoi_dung table
-- Run in your database (phpMyAdmin or mysql client)

ALTER TABLE `nguoi_dung`
  ADD COLUMN `reset_token` VARCHAR(128) NULL DEFAULT NULL,
  ADD COLUMN `reset_token_expiry` DATETIME NULL DEFAULT NULL;

-- If your users table is named `users`, run:
-- ALTER TABLE `users` ADD COLUMN `reset_token` VARCHAR(128) NULL DEFAULT NULL, ADD COLUMN `reset_token_expiry` DATETIME NULL DEFAULT NULL;
