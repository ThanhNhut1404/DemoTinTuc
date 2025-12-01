<?php
require_once __DIR__ . '/vendor/autoload.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();

echo "===== BAI_VIET TABLE STRUCTURE =====\n";
$stmt = $conn->query("DESCRIBE bai_viet");
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $col) {
    echo "- " . $col['Field'] . " (" . $col['Type'] . ") " . ($col['Null'] === 'NO' ? 'NOT NULL' : 'NULLABLE') . "\n";
}

echo "\n===== SAMPLE ROW FROM BAI_VIET =====\n";
$stmt = $conn->query("SELECT * FROM bai_viet ORDER BY id DESC LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    foreach ($row as $key => $val) {
        echo "- $key: " . var_export($val, true) . "\n";
    }
} else {
    echo "No rows found.\n";
}

echo "\n===== ALL ROWS (FIRST 3) =====\n";
$stmt = $conn->query("SELECT id, tieu_de, trang_thai FROM bai_viet ORDER BY id DESC LIMIT 3");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo json_encode($r) . "\n";
}
?>
