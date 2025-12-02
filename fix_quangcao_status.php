<?php
require_once __DIR__ . '/vendor/autoload.php';

$pdo = new PDO('mysql:host=localhost;dbname=website_tin_tuc;charset=utf8', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== CẬP NHẬT DỮ LIỆU QUẢNG CÁO ===\n";

// Cập nhật toàn bộ NULL thành 'on'
$stmt = $pdo->prepare("UPDATE quang_cao SET trang_thai = 'on' WHERE trang_thai IS NULL OR trang_thai = ''");
$result = $stmt->execute();

// Kiểm tra số lượng đã cập nhật
$stmt = $pdo->query("SELECT COUNT(*) as total FROM quang_cao WHERE trang_thai = 'on'");
$data = $stmt->fetch(PDO::FETCH_ASSOC);

echo "Đã cập nhật tất cả quảng cáo\n";
echo "Tổng quảng cáo có trạng thái 'on': " . $data['total'] . "\n";

// Hiển thị dữ liệu sau khi cập nhật
echo "\n=== DỮ LIỆU SAU CẬP NHẬT ===\n";
$stmt = $pdo->query("SELECT id, tieu_de, trang_thai FROM quang_cao");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $row) {
    echo "ID: {$row['id']}, Tiêu đề: {$row['tieu_de']}, Trạng thái: {$row['trang_thai']}\n";
}
?>
