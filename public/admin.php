<?php
// Entrypoint thử nghiệm cho module Quản lý thành viên (chỉ dùng trong môi trường dev)
// Bảo vệ: mặc định yêu cầu biến môi trường APP_ENV=dev. Tuy nhiên để tiện dev cục bộ
// (chạy qua XAMPP/Apache trên localhost) cho phép truy cập khi request đến từ localhost.
// Điều này giữ an toàn cho môi trường production nhưng không yêu cầu cấu hình server thêm.

$isDevEnv = (getenv('APP_ENV') === 'dev');
$remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocalRequest = in_array($remoteAddr, ['127.0.0.1', '::1']) || stripos($host, 'localhost') !== false;

if (! $isDevEnv && ! $isLocalRequest) {
    echo "Admin test endpoint disabled. Set APP_ENV=dev to enable or access from localhost.";
    exit;
}

require_once __DIR__ . '/../vendor/autoload.php';

use Website\TinTuc\Controllers\ThanhVienController;

$action = $_GET['action'] ?? 'index';
$controller = new ThanhVienController();

switch ($action) {
    case 'index':
        $controller->index();
        break;

    case 'search':
        $controller->search();
        break;

    case 'updateRole':
        // updateRole expects POST data (id, quyen)
        $controller->updateRole();
        break;

    case 'lock':
        // expects id and hanhDong=khoa
        $controller->lock();
        break;

    case 'unlock':
        // expects id and hanhDong=mo
        $controller->unlock();
        break;

    default:
        echo "Action không tồn tại in admin.php";
        break;
}
