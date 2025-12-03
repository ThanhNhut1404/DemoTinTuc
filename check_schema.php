<?php
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=website_tin_tuc;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Kiểm tra schema
$stmt = $pdo->query("SHOW COLUMNS FROM quang_cao");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "=== SCHEMA CỦA BẢNG quang_cao ===\n";
foreach ($columns as $col) {
    echo $col['Field'] . " - " . $col['Type'] . " - Null: " . $col['Null'] . " - Default: " . ($col['Default'] ?? 'KHÔNG') . "\n";
}

// Kiểm tra dữ liệu raw
echo "\n=== DỮ LIỆU RAW ===\n";
$stmt = $pdo->query("SELECT * FROM quang_cao WHERE id = 5");
$data = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($data);
?>
