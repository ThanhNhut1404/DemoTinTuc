<?php
// Direct debug using Database class
require_once 'src/Database.php';

use Website\TinTuc\Database;

echo "<h1>🔍 Debug: Kiểm tra Danh mục</h1>";

// Connect to DB
try {
    $db = new Database();
    $pdo = $db->connect();
    echo "<p style='color:green'>✅ Kết nối DB thành công</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Lỗi kết nối: " . $e->getMessage() . "</p>";
    exit;
}

// 1. Check if tables exist
echo "<h2>1. Kiểm tra bảng</h2>";
$tables = ['chuyen_muc', 'chuyen_muc_cha'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color:green'>✅ Bảng '$table' tồn tại</p>";
    } else {
        echo "<p style='color:red'>❌ Bảng '$table' không tồn tại</p>";
    }
}

// 2. Count rows
echo "<h2>2. Đếm dữ liệu</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM chuyen_muc");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = $result['cnt'] ?? 0;
    echo "<p>Số bản ghi trong 'chuyen_muc': <strong>$count</strong></p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM chuyen_muc_cha");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $count_cha = $result['cnt'] ?? 0;
    echo "<p>Số bản ghi trong 'chuyen_muc_cha': <strong>$count_cha</strong></p>";
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// 3. Show all danh_muc
echo "<h2>3. Danh sách tất cả danh mục con</h2>";
try {
    $stmt = $pdo->query("SELECT id, ten_chuyen_muc, mo_ta, id_cha, thu_tu FROM chuyen_muc ORDER BY thu_tu ASC");
    $danh_muc_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($danh_muc_list) > 0) {
        echo "<table style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#f0f0f0;'>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>ID</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>Tên</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>Mô tả</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>ID cha</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>Thứ tự</th>";
        echo "</tr>";
        
        foreach ($danh_muc_list as $dm) {
            echo "<tr>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . $dm['id'] . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . htmlspecialchars($dm['ten_chuyen_muc']) . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . htmlspecialchars($dm['mo_ta'] ?? '') . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . ($dm['id_cha'] ?? 'NULL') . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . $dm['thu_tu'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange;'>⚠️ Bảng 'chuyen_muc' trống, chưa có danh mục nào</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Lỗi truy vấn: " . $e->getMessage() . "</p>";
}

// 4. Show all danh_muc_cha
echo "<h2>4. Danh sách danh mục cha</h2>";
try {
    $stmt = $pdo->query("SELECT id, ten_chuyen_muc, mo_ta, thu_tu FROM chuyen_muc_cha ORDER BY thu_tu ASC");
    $danh_muc_cha_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($danh_muc_cha_list) > 0) {
        echo "<table style='border-collapse:collapse; width:100%;'>";
        echo "<tr style='background:#f0f0f0;'>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>ID</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>Tên</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>Mô tả</th>";
        echo "<th style='border:1px solid #ddd; padding:8px;'>Thứ tự</th>";
        echo "</tr>";
        
        foreach ($danh_muc_cha_list as $dmc) {
            echo "<tr>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . $dmc['id'] . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . htmlspecialchars($dmc['ten_chuyen_muc']) . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . htmlspecialchars($dmc['mo_ta'] ?? '') . "</td>";
            echo "<td style='border:1px solid #ddd; padding:8px;'>" . $dmc['thu_tu'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:orange;'>⚠️ Bảng 'chuyen_muc_cha' trống</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Lỗi truy vấn: " . $e->getMessage() . "</p>";
}

// 5. Check FK constraint
echo "<h2>5. Kiểm tra Foreign Key</h2>";
try {
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
                         FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                         WHERE TABLE_NAME = 'chuyen_muc' AND REFERENCED_TABLE_NAME IS NOT NULL");
    $fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($fks) > 0) {
        echo "<pre>" . json_encode($fks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    } else {
        echo "<p style='color:orange;'>⚠️ Không tìm thấy FK trên bảng 'chuyen_muc'</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

?>
