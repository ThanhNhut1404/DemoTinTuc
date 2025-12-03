<?php
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=website_tin_tuc;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Kiểm tra schema
$stmt = $pdo->query("SHOW COLUMNS FROM binh_luan");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== SCHEMA CỦA BẢNG binh_luan ===\n";
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . " - Null: " . $col['Null'] . " - Default: " . ($col['Default'] ?? 'KHÔNG') . "\n";
}

// Lấy sample data
echo "\n=== SAMPLE DATA ===\n";
$stmt = $pdo->query("SELECT * FROM binh_luan LIMIT 1");
$data = $stmt->fetch(PDO::FETCH_ASSOC);
if ($data) {
    print_r($data);
} else {
    echo "Chưa có dữ liệu bình luận\n";
}
?>
