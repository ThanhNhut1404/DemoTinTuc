-- Migration: add avatar column to nguoi_dung before ho_ten
ALTER TABLE `nguoi_dung`
  ADD COLUMN `avatar` VARCHAR(255) DEFAULT NULL AFTER `id`;
