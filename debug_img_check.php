<?php
include 'config.php';

echo "=== KIỂM TRA DỮ LIỆU ẢNH ===\n";
$result = $conn->query("SELECT id, tieu_de, anh_dai_dien FROM bai_viet LIMIT 5");

while ($row = $result->fetch_assoc()) {
    echo "\nID: " . $row['id'] . "\n";
    echo "Tiêu đề: " . $row['tieu_de'] . "\n";
    echo "Ảnh DB: " . $row['anh_dai_dien'] . "\n";
    
    // Kiểm tra img_url()
    $img_path = $row['anh_dai_dien'];
    if (strpos($img_path, '/') === false) {
        $full_url = '/Demotintuc/public/uploads/bai_viet/' . $img_path;
    } else {
        $full_url = '/Demotintuc/' . ltrim($img_path, '/');
    }
    echo "URL tạo được: " . $full_url . "\n";
    
    // Kiểm tra file tồn tại không
    $file_path = __DIR__ . '/public/uploads/bai_viet/' . $img_path;
    echo "File path: " . $file_path . "\n";
    echo "File tồn tại: " . (file_exists($file_path) ? "CÓ" : "KHÔNG") . "\n";
    echo "---\n";
}
?>
