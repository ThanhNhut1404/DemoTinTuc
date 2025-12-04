-- Migration: create table for tracking guest views by fingerprint
-- Fingerprint is a hash of IP + User-Agent to reduce relying on cookies.

CREATE TABLE IF NOT EXISTS `bai_viet_views_guests` (
  `id_bai_viet` INT NOT NULL,
  `fingerprint` VARCHAR(64) NOT NULL,
  `last_view` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bai_viet`, `fingerprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
