<?php
// 🚀 Bắt đầu session để quản lý đăng nhập
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Composer Autoload
require_once __DIR__ . '/../vendor/autoload.php';

// Controllers
require_once __DIR__ . '/../src/Controllers/LoginController.php';
require_once __DIR__ . '/../src/Controllers/RegisterController.php';
require_once __DIR__ . '/../src/Controllers/TrangChuController.php';
require_once __DIR__ . '/../src/Controllers/BaiVietController.php';
require_once __DIR__ . '/../src/Controllers/ForgotPasswordController.php';
require_once __DIR__ . '/../src/Controllers/ChuyenMucController.php';
require_once __DIR__ . '/../src/Controllers/ProfileController.php';
require_once __DIR__ . '/../src/Controllers/searchController.php';
require_once __DIR__ . '/../src/Controllers/SuggestController.php';

use Website\TinTuc\Controllers\LoginController;
use Website\TinTuc\Controllers\RegisterController;
use Website\TinTuc\Controllers\TrangChuController;
use Website\TinTuc\Controllers\ForgotPasswordController;
use Website\TinTuc\Controllers\ChuyenMucController;
use Website\TinTuc\Controllers\ThanhVienController;

// Action
$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'home':
        $controller = new TrangChuController();
        $controller->index();
        break;

    // LOGIN
    case 'login':
        $controller = new LoginController();
        $controller->showLoginForm();
        break;

    case 'do_login':
        $controller = new LoginController();
        $controller->login();
        break;

    // LOGOUT
    case 'logout':
        $controller = new LoginController();
        $controller->logout();
        break;

    // REGISTER
    case 'register':
        $controller = new RegisterController();
        $controller->showForm();
        break;

    case 'do_register':
        $controller = new RegisterController();
        $controller->handleRegister();
        break;

    // FORGOT PASSWORD
    case 'forgot_password':
        $controller = new ForgotPasswordController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->submit();
        } else {
            $controller->index();
        }
        break;

    // RESET PASSWORD
    case 'reset':
        $controller = new ForgotPasswordController();
        $controller->reset();
        break;

    case 'submit_reset':
        $controller = new ForgotPasswordController();
        $controller->submitReset();
        break;

    // SEND RESET (giữ code cũ)
    case 'send_reset':
        $controller = new ForgotPasswordController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->submit();
        } else {
            $controller->index();
        }
        break;

    // CATEGORY
    case 'chuyenmuc':
        $id = $_GET['id'] ?? 0;
        $controller = new ChuyenMucController();
        $controller->hienThiTheoChuyenMuc($id);
        break;

    // DETAIL POST
    case 'chi_tiet_bai_viet':
        $controller = new \Website\TinTuc\Controllers\BaiVietController();
        $controller->chiTiet($_GET['id']);
        break;

        case 'suggest':
    $controller = new \Website\TinTuc\Controllers\SuggestController();
    $controller->index();
    break;
//            case 'profile':
//     include 'views/frontend/profile.php';
//     break;

// case 'profile_edit':
//     include 'views/frontend/profile_edit.php';
//     break;

// case 'profile_update':
//     // xử lý form
//     $_SESSION['user']['name'] = $_POST['name'];
//     header("Location: index.php?action=profile");
//     break;

    // ❌ Mặc định: về trang chủ
    // SEARCH
    case 'search':
        $controller = new \Website\TinTuc\Controllers\searchController();
        $controller->index();
        break;

    // SUGGEST
    case 'suggest':
        $controller = new \Website\TinTuc\Controllers\SuggestController();
        $controller->index();
        break;

    // PROFILE
    case 'profile':
        include __DIR__ . '/../views/frontend/profile.php';
        break;

    case 'profile_edit':
        include __DIR__ . '/../views/frontend/profile_edit.php';
        break;

    case 'profile_update':
        $_SESSION['user']['name'] = $_POST['name'];
        header("Location: index.php?action=profile");
        break;
   case 'userPage':
    $controller = new \Website\TinTuc\Controllers\ThanhVienController();
    $controller->userPage();
    break;

case 'updateProfile':
    $controller = new \Website\TinTuc\Controllers\ThanhVienController();
    $controller->updateProfile();
    break;
    case 'dathich':
    include __DIR__ . '/../views/frontend/dathich.php';

    break;

case 'daluu':
  include __DIR__ . '/../views/frontend/daluu.php';

    break;

case 'binhluancuatoi':
 include __DIR__ . '/../views/frontend/binhluancuatoi.php';

    break;



    // DEFAULT

    default:
        $controller = new TrangChuController();
        $controller->index();
        break;
}
