<?php
require_once __DIR__ . '/vendor/autoload.php';

use Website\TinTuc\Models\QuangCaoModel;

$model = new QuangCaoModel();

// Lấy tất cả quảng cáo
$all = $model->all();
echo "=== TẤT CẢ QUẢNG CÁO ===\n";
print_r($all);

// Thử update status của quảng cáo đầu tiên nếu có
if (!empty($all)) {
    $firstId = $all[0]['id'];
    $currentStatus = $all[0]['trang_thai'] ?? 'active';
    $newStatus = ($currentStatus === 'active') ? 'inactive' : 'active';
    
    echo "\n=== TEST TOGGLE STATUS ===\n";
    echo "ID: $firstId\n";
    echo "Trạng thái hiện tại: $currentStatus\n";
    echo "Trạng thái mới: $newStatus\n";
    
    $result = $model->updateStatus($firstId, $newStatus);
    echo "Kết quả update: " . ($result ? 'THÀNH CÔNG' : 'THẤT BẠI') . "\n";
    
    // Kiểm tra lại
    $updated = $model->find($firstId);
    echo "Trạng thái sau update: " . $updated['trang_thai'] . "\n";
}
?>
