<?php
session_start();
require_once 'src/Models/ChuyenMucModel.php';

use Website\TinTuc\Models\ChuyenMucModel;

$model = new ChuyenMucModel();

echo "<h2>Debug: Danh mục</h2>";

// Test 1: Kết nối DB
echo "<h3>1. Kiểm tra kết nối DB</h3>";
if ($model->db) {
    echo "<p style='color:green'>✅ DB kết nối thành công</p>";
} else {
    echo "<p style='color:red'>❌ DB không kết nối</p>";
    exit;
}

// Test 2: Kiểm tra bảng tồn tạihttp://localhost/DemoTinTuc/debug_check_danh_muc.php
echo "<h3>2. Kiểm tra bảng 'chuyen_muc'</h3>";
try {
    $result = $model->db->query("SHOW TABLES LIKE 'chuyen_muc'");
    if ($result->rowCount() > 0) {
        echo "<p style='color:green'>✅ Bảng 'chuyen_muc' tồn tại</p>";
    } else {
        echo "<p style='color:red'>❌ Bảng 'chuyen_muc' không tồn tại</p>";
        exit;
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
    exit;
}

// Test 3: Lấy danh sách
echo "<h3>3. Gọi getAll()</h3>";
try {
    $list = $model->getAll();
    echo "<p>Số bản ghi: " . count($list) . "</p>";
    if (count($list) > 0) {
        echo "<pre>" . json_encode($list, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    } else {
        echo "<p style='color:orange'>⚠️ Bảng trống, không có danh mục nào</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// Test 4: Cấu trúc bảng
echo "<h3>4. Cấu trúc bảng 'chuyen_muc'</h3>";
try {
    $result = $model->db->query("DESCRIBE chuyen_muc");
    $fields = $result->fetchAll(\PDO::FETCH_ASSOC);
    echo "<pre>" . json_encode($fields, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
} catch (\Exception $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
}

// Test 5: Kiểm tra bảng 'chuyen_muc_cha'
echo "<h3>5. Kiểm tra bảng 'chuyen_muc_cha'</h3>";
try {
    $result = $model->db->query("SHOW TABLES LIKE 'chuyen_muc_cha'");
    if ($result->rowCount() > 0) {
        echo "<p style='color:green'>✅ Bảng 'chuyen_muc_cha' tồn tại</p>";
        $result2 = $model->db->query("SELECT * FROM chuyen_muc_cha");
        $parents = $result2->fetchAll(\PDO::FETCH_ASSOC);
        echo "<p>Số bản ghi cha: " . count($parents) . "</p>";
        if (count($parents) > 0) {
            echo "<pre>" . json_encode($parents, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
        }
    } else {
        echo "<p style='color:orange'>⚠️ Bảng 'chuyen_muc_cha' không tồn tại (chưa migration)</p>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>❌ Lỗi: " . $e->getMessage() . "</p>";
}
?>
