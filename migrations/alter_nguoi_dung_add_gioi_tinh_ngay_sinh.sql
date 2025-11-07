-- Migration: add gioi_tinh and ngay_sinh columns to nguoi_dung
-- Adds columns before `email` (i.e., after `ho_ten`)
ALTER TABLE `nguoi_dung`
  ADD COLUMN `gioi_tinh` ENUM('Nam','Nữ','Khác') DEFAULT 'Khác' AFTER `ho_ten`,
  ADD COLUMN `ngay_sinh` DATE AFTER `gioi_tinh`;
