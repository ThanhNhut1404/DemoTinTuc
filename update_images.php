<?php
include 'config.php';

// Cập nhật bài viết để dùng ảnh có sẵn
$updates = [
    1 => 'bau_troi.jpg',      // Khởi công cầu Cần Thơ
    2 => 'giaitri9.jpg',       // Triển lãm nghệ thuật
    3 => 'congnghe10.jpg',     // Phòng dịch
    4 => 'caphe.jpg',          // Festival
    5 => 'ca_phe.jpg'          // Hội nghị du lịch
];

foreach ($updates as $id => $img) {
    $sql = "UPDATE bai_viet SET anh_dai_dien = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $img, $id);
    $stmt->execute();
    echo "Cập nhật ID $id: $img\n";
}

echo "\nDone! Các bài viết đã có ảnh mới.\n";
?>
