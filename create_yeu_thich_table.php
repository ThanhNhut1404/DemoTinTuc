<?php
include 'config.php';

// Tạo bảng yeu_thich nếu chưa tồn tại
$sql = "CREATE TABLE IF NOT EXISTS yeu_thich (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_bai_viet INT NOT NULL,
    id_nguoi_dung INT NOT NULL,
    ngay_yeu_thich TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (id_bai_viet, id_nguoi_dung),
    FOREIGN KEY (id_bai_viet) REFERENCES bai_viet(id) ON DELETE CASCADE,
    FOREIGN KEY (id_nguoi_dung) REFERENCES nguoi_dung(id) ON DELETE CASCADE
)";

if ($conn->query($sql) === TRUE) {
    echo "✓ Bảng yeu_thich đã được tạo thành công!\n";
} else {
    echo "✗ Lỗi: " . $conn->error . "\n";
}

$conn->close();
?>
