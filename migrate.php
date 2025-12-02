<?php
require_once __DIR__ . '/vendor/autoload.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();

try {
    // Kiểm tra xem cột có tồn tại chưa
    $checkSql = "SHOW COLUMNS FROM bai_viet LIKE 'id_the_tag'";
    $stmt = $conn->prepare($checkSql);
    $stmt->execute();
    $col = $stmt->fetch(\PDO::FETCH_ASSOC);
    
    if ($col) {
        echo "✓ Column id_the_tag already exists.\n";
    } else {
        // Thêm cột nếu chưa có
        $conn->exec('ALTER TABLE bai_viet ADD COLUMN id_the_tag INT NULL AFTER id_chuyen_muc');
        echo "✓ Column id_the_tag added successfully!\n";
    }
    
    // Hiển thị cấu trúc bảng
    echo "\nTable structure:\n";
    $stmt = $conn->prepare("DESCRIBE bai_viet");
    $stmt->execute();
    $cols = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "- " . $c['Field'] . " (" . $c['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
