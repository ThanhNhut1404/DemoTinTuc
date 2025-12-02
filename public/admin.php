<?php
session_start();
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
use Website\TinTuc\Controllers\BaiVietController;
use Website\TinTuc\Controllers\QuangCaoController;
use Website\TinTuc\Controllers\BannerController;
use Website\TinTuc\Controllers\BgWallpaperController;

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

    case 'thanh_vien_roles':
        // Show member list (same as index)
        $controller->index();
        break;

    case 'bai_viet':
        // Quản lí bài viết - delegating to BaiVietController
        $baiVietController = new BaiVietController();
        $baiVietController->index();
        break;

    // Quản lý quảng cáo
    case 'quang_cao':
        $qcController = new QuangCaoController();
        $qcController->index();
        break;

    case 'qc_create':
        $qcController = new QuangCaoController();
        $qcController->create();
        break;

    case 'qc_store':
        $qcController = new QuangCaoController();
        $qcController->store();
        break;

    case 'qc_edit':
        $qcController = new QuangCaoController();
        $qcController->edit($_GET['id'] ?? 0);
        break;

    case 'qc_update':
        $qcController = new QuangCaoController();
        $qcController->update($_GET['id'] ?? ($_POST['id'] ?? 0));
        break;

    case 'qc_delete':
        $qcController = new QuangCaoController();
        $qcController->delete($_GET['id'] ?? 0);
        break;

    // Các hành động quản trị cho bài viết
    case 'create':
        $baiVietController = new BaiVietController();
        $baiVietController->create();
        break;

    case 'store':
        $baiVietController = new BaiVietController();
        $baiVietController->store();
        break;

    case 'edit':
        $baiVietController = new BaiVietController();
        $baiVietController->edit($_GET['id'] ?? 0);
        break;

    case 'update':
        $baiVietController = new BaiVietController();
        $baiVietController->update($_POST['id'] ?? 0);
        break;

    case 'delete':
        $baiVietController = new BaiVietController();
        $baiVietController->delete($_GET['id'] ?? 0);
        break;

    case 'upload_image':
        $baiVietController = new BaiVietController();
        $baiVietController->uploadImage();
        break;

    case 'userPage':
        $controller->userPage();
        break;

    case 'updateProfile':
        $controller->updateProfile();
        break;

    // Quản lý banner
    case 'banner':
        $bannerController = new BannerController();
        $bannerController->index();
        break;

    case 'banner_create':
        $bannerController = new BannerController();
        $bannerController->create();
        break;

    case 'banner_store':
        $bannerController = new BannerController();
        $bannerController->store();
        break;

    case 'banner_edit':
        $bannerController = new BannerController();
        $bannerController->edit($_GET['id'] ?? 0);
        break;

    case 'banner_update':
        $bannerController = new BannerController();
        $bannerController->update($_GET['id'] ?? ($_POST['id'] ?? 0));
        break;

    case 'banner_delete':
        $bannerController = new BannerController();
        $bannerController->delete($_GET['id'] ?? 0);
        break;

    case 'banner_toggle':
        $bannerController = new BannerController();
        $bannerController->toggle($_GET['id'] ?? 0);
        break;

    // Quản lý nền website
    case 'bg_wallpaper':
        $bgController = new BgWallpaperController();
        $bgController->index();
        break;

    case 'bg_wallpaper_create':
        $bgController = new BgWallpaperController();
        $bgController->create();
        break;

    case 'bg_wallpaper_store':
        $bgController = new BgWallpaperController();
        $bgController->store();
        break;

    case 'bg_wallpaper_edit':
        $bgController = new BgWallpaperController();
        $bgController->edit($_GET['id'] ?? 0);
        break;

    case 'bg_wallpaper_update':
        $bgController = new BgWallpaperController();
        $bgController->update($_GET['id'] ?? ($_POST['id'] ?? 0));
        break;

    case 'bg_wallpaper_delete':
        $bgController = new BgWallpaperController();
        $bgController->delete($_GET['id'] ?? 0);
        break;

    case 'bg_wallpaper_toggle':
        $bgController = new BgWallpaperController();
        $bgController->toggle($_GET['id'] ?? 0);
        break;

    default:
        echo "Action không tồn tại in admin.php";
        break;
}
