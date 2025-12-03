-- Migration: create table for parent categories
CREATE TABLE IF NOT EXISTS `chuyen_muc_cha` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ten_chuyen_muc` VARCHAR(191) NOT NULL,
  `mo_ta` TEXT NULL,
  `thu_tu` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: seed example
INSERT INTO `chuyen_muc_cha` (`ten_chuyen_muc`, `mo_ta`, `thu_tu`) VALUES
('Thể thao','Các tin tức về thể thao',1)
ON DUPLICATE KEY UPDATE ten_chuyen_muc = VALUES(ten_chuyen_muc);
