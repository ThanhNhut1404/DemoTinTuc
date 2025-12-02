<?php
include __DIR__ . '/config.php';

$result = $conn->query('SELECT id, tieu_de, anh_dai_dien FROM bai_viet LIMIT 3');
echo "Dữ liệu ảnh đại diện:\n";
echo "=============================\n";
while($row = $result->fetch_assoc()) {
    echo "ID: " . $row['id'] . "\n";
    echo "Title: " . $row['tieu_de'] . "\n";
    echo "Image: " . ($row['anh_dai_dien'] ?? 'NULL') . "\n";
    echo "-----\n";
}

// Kiểm tra hàm img_url
function img_url($path) {
    return '/Demotintuc/' . ltrim($path, '/');
}

echo "\nKiểm tra URL ảnh:\n";
$test_path = 'public/uploads/wallpapers/cau_cantho.jpg';
echo "Input: " . $test_path . "\n";
echo "Output: " . img_url($test_path) . "\n";
?>
