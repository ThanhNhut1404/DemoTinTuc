<?php
// 🚀 Bắt đầu session để quản lý đăng nhập
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Nạp autoload (nếu có Composer)
require_once __DIR__ . '/../vendor/autoload.php';

// ✅ Nạp các controller cần thiết
require_once __DIR__ . '/../src/Controllers/LoginController.php';
require_once __DIR__ . '/../src/Controllers/RegisterController.php';
require_once __DIR__ . '/../src/Controllers/TrangChuController.php';
require_once __DIR__ . '/../src/Controllers/BaiVietController.php';
require_once __DIR__ . '/../src/Controllers/ForgotPasswordController.php';
require_once __DIR__ . '/../src/Controllers/ChuyenMucController.php'; // 🆕 thêm dòng này nè

use Website\TinTuc\Controllers\LoginController;
use Website\TinTuc\Controllers\RegisterController;
use Website\TinTuc\Controllers\TrangChuController;
use Website\TinTuc\Controllers\ForgotPasswordController;
use Website\TinTuc\Controllers\ChuyenMucController;
// ✅ Lấy tham số "action" trên URL (vd: ?action=login)
$action = $_GET['action'] ?? 'home';

switch ($action) {
    // 🏠 Trang chủ
    case 'home':
        $controller = new TrangChuController();
        $controller->index();
        break;

    // 🔑 Đăng nhập
    case 'login':
        $controller = new LoginController();
        $controller->showLoginForm();
        break;

    case 'do_login':
        $controller = new LoginController();
        $controller->login();
        break;

    // 🚪 Đăng xuất
    case 'logout':
        $controller = new LoginController();
        $controller->logout();
        break;

    // 📝 Đăng ký
    case 'register':
        $controller = new RegisterController();
        $controller->showForm();
        break;

    case 'do_register':
        $controller = new RegisterController();
        $controller->handleRegister();
        break;



    // CRUD ví dụ
    case 'create':
        $controller->create();
        break;

    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit($_GET['id']);
        break;

    case 'update':
        $controller->update($_POST['id']);
        break;

    case 'delete':
        $controller->delete($_GET['id']);
        break;

    // 🔑 Quên mật khẩu
    case 'forgot':
        $controller = new ForgotPasswordController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->submit();
        } else {
            $controller->index();
        }
        break;
    case 'chuyenmuc':
        $id = $_GET['id'] ?? 0;
        $controller = new ChuyenMucController();
        $controller->hienThiTheoChuyenMuc($id);
        break;
    case 'chi_tiet_bai_viet':
        $controller = new \Website\TinTuc\Controllers\BaiVietController();
        $controller->chiTiet($_GET['id']);
        break;
        case 'search':
    $controller = new \Website\TinTuc\Controllers\searchController();
    $controller->index();
    break;

case 'ajax_search':
    $controller = new \Website\TinTuc\Controllers\BaiVietController();
    $controller->ajaxSearch();
    break;

    // ❌ Mặc định: về trang chủ
    default:
        $controller = new TrangChuController();
        $controller->index();
        break;
}
