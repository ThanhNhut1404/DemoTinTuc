<?php
namespace Website\TinTuc\Routes;

require_once __DIR__ . '/../vendor/autoload.php';

use Website\TinTuc\Controllers\ThanhVienController;

// Lấy tham số action từ URL, mặc định là 'thanhvien'
$action = $_GET['action'] ?? 'thanhvien';

$controller = new ThanhVienController();

switch ($action) {
    case 'thanhvien':
        $controller->index();
        break;

    case 'khoa':
        $controller->khoaMoTaiKhoan();
        break;

    case 'phanquyen':
        $controller->phanQuyen();
        break;

    default:
        echo "❌ Trang không tồn tại!";
        break;
}
