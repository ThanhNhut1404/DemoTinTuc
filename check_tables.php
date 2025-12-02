<?php
include 'config.php';

echo "=== KIỂM TRA CẤU TRÚC BẢNG yeu_thich ===\n";
$result = $conn->query('DESCRIBE yeu_thich');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} else {
    echo "Lỗi: " . $conn->error . "\n";
}

echo "\n=== KIỂM TRA CẤU TRÚC BẢNG luu_bai_viet ===\n";
$result = $conn->query('DESCRIBE luu_bai_viet');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo $row['Field'] . ' - ' . $row['Type'] . "\n";
    }
} else {
    echo "Lỗi: " . $conn->error . "\n";
}

$conn->close();
?>
