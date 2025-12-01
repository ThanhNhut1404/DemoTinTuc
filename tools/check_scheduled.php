<?php
require __DIR__ . '/../vendor/autoload.php';

use Website\TinTuc\Database;

try {
    $db = new Database();
    $conn = $db->connect();

    $sql = "SELECT bv.id, bv.tieu_de, bv.ngay_dang, bv.trang_thai, bv.id_chuyen_muc FROM bai_viet bv ORDER BY bv.ngay_dang DESC LIMIT 10";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "LAST 10 BY ngay_dang (desc):\n";
    foreach ($rows as $r) {
        echo sprintf("ID:%s | STATUS:%s | DATE:%s | CAT:%s | TITLE:%s\n", $r['id'], $r['trang_thai'], $r['ngay_dang'], $r['id_chuyen_muc'], str_replace("\n", ' ', substr($r['tieu_de'],0,120)));
    }

    echo "\nSCHEDULED (ngay_dang > NOW() excluding Tu_choi):\n";
    $sql2 = "SELECT bv.id, bv.tieu_de, bv.ngay_dang, bv.trang_thai, bv.id_chuyen_muc FROM bai_viet bv WHERE bv.ngay_dang > NOW() AND LOWER(TRIM(bv.trang_thai)) != 'tu_choi' ORDER BY bv.ngay_dang ASC";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->execute();
    $rows2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    echo "FOUND:" . count($rows2) . "\n";
    foreach ($rows2 as $r) {
        echo sprintf("ID:%s | STATUS:%s | DATE:%s | CAT:%s | TITLE:%s\n", $r['id'], $r['trang_thai'], $r['ngay_dang'], $r['id_chuyen_muc'], str_replace("\n", ' ', substr($r['tieu_de'],0,120)));
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
