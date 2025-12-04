-- Migration: create table for tracking last view time per user per post
-- Run this once (or the code will create the table automatically if missing).

CREATE TABLE IF NOT EXISTS `bai_viet_views_users` (
  `id_bai_viet` INT NOT NULL,
  `id_nguoi_dung` INT NOT NULL,
  `last_view` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bai_viet`, `id_nguoi_dung`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
