ALTER TABLE banner
ADD COLUMN trang_thai ENUM('on', 'off') DEFAULT 'off' AFTER ngay_tao;