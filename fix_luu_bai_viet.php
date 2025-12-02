<?php
include 'config.php';

// Xóa bảng cũ
$conn->query("DROP TABLE IF EXISTS luu_bai_viet");

// Tạo bảng mới
$sql = "CREATE TABLE luu_bai_viet (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_bai_viet INT NOT NULL,
    id_nguoi_dung INT NOT NULL,
    ngay_luu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_save (id_bai_viet, id_nguoi_dung),
    FOREIGN KEY (id_bai_viet) REFERENCES bai_viet(id) ON DELETE CASCADE,
    FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Bảng luu_bai_viet đã được tạo lại thành công!\n";
} else {
    echo "✗ Lỗi: " . $conn->error . "\n";
}

$conn->close();
?>
