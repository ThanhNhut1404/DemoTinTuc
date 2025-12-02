-- Migration: Tạo bảng quản lý nền website
-- Created: 2025-12-01
-- Description: Bảng lưu trữ hình ảnh nền website với chỉ một nền được kích hoạt tại một thời điểm

CREATE TABLE IF NOT EXISTS `bg_wallpaper` (
  `id` int AUTO_INCREMENT PRIMARY KEY COMMENT 'ID duy nhất của nền',
  `ten_wallpaper` varchar(255) NOT NULL COMMENT 'Tên nền website',
  `duong_dan_file` varchar(500) NOT NULL UNIQUE COMMENT 'Đường dẫn file hình ảnh (uploads/wallpapers/filename.ext)',
  `mo_ta` text COMMENT 'Mô tả chi tiết về nền',
  `trang_thai` enum('on','off') NOT NULL DEFAULT 'off' COMMENT 'Trạng thái: on = đang dùng, off = ẩn',
  `ngay_tao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo nền',
  `ngay_cap_nhat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ngày cập nhật lần cuối'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Quản lý hình ảnh nền website';

-- Index để tối ưu tìm kiếm nền đang kích hoạt
CREATE INDEX idx_trang_thai ON `bg_wallpaper`(trang_thai);

-- Dữ liệu mẫu (tùy chọn)
INSERT INTO `bg_wallpaper` (`ten_wallpaper`, `duong_dan_file`, `mo_ta`, `trang_thai`) 
VALUES (
  'Nền Mặc Định',
  'default_wallpaper.jpg',
  'Nền website mặc định',
  'on'
);
