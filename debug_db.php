<?php
// Debug script: check what's actually in DB
require_once __DIR__ . '/vendor/autoload.php';

try {
    $db = new PDO("mysql:host=localhost;dbname=website_tin_tuc;charset=utf8", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Bài viết (anh_dai_dien)</h2>";
    $stmt = $db->query("SELECT id, tieu_de, anh_dai_dien FROM bai_viet LIMIT 3");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo "<pre>";
        echo "ID: " . htmlspecialchars($row['id']) . "\n";
        echo "Title: " . htmlspecialchars($row['tieu_de']) . "\n";
        echo "Image: " . htmlspecialchars($row['anh_dai_dien']) . "\n";
        echo "</pre>";
    }
    
    echo "<h2>Quảng cáo (hinh_anh)</h2>";
    $stmt = $db->query("SELECT id, tieu_de, hinh_anh, vi_tri FROM quang_cao LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        echo "<pre>";
        echo "ID: " . htmlspecialchars($row['id']) . "\n";
        echo "Title: " . htmlspecialchars($row['tieu_de']) . "\n";
        echo "Image: " . htmlspecialchars($row['hinh_anh']) . "\n";
        echo "Vi tri: " . htmlspecialchars($row['vi_tri']) . "\n";
        echo "</pre>";
    }
    
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
