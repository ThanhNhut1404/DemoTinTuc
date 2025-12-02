<?php
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=website_tin_tuc;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Kiểm tra schema nguoi_dung
echo "=== SCHEMA BẢNG nguoi_dung ===\n";
$stmt = $pdo->query("SHOW COLUMNS FROM nguoi_dung");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . "\n";
}

// Kiểm tra schema binh_luan
echo "\n=== SCHEMA BẢNG binh_luan ===\n";
$stmt = $pdo->query("SHOW COLUMNS FROM binh_luan");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    echo $col['Field'] . "\n";
}
?>
