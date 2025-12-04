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
use Website\TinTuc\Models\ThanhVienModel;
use Website\TinTuc\Controllers\BaiVietController;
use Website\TinTuc\Controllers\ChuyenMucController;
use Website\TinTuc\Controllers\QuangCaoController;
use Website\TinTuc\Controllers\BannerController;
use Website\TinTuc\Controllers\BgWallpaperController;
use Website\TinTuc\Controllers\BinhLuanAdminController;

// Actions: allow login/logout without authentication
$action = $_GET['action'] ?? 'index';

// Simple helpers for tab registration/unregistration used by the admin layout JS.
if ($action === 'register_tab') {
    $token = $_POST['token'] ?? $_GET['token'] ?? '';
    if ($token) {
        if (!isset($_SESSION['admin_tab_tokens']) || !is_array($_SESSION['admin_tab_tokens'])) {
            $_SESSION['admin_tab_tokens'] = [];
        }
        $_SESSION['admin_tab_tokens'][$token] = time();
        // cancel any pending logout because a tab re-registered
        if (isset($_SESSION['pending_logout'])) {
            unset($_SESSION['pending_logout']);
        }
    }
    // Return a minimal response
    header('Content-Type: text/plain');
    echo 'ok';
    exit;
}

if ($action === 'unregister_tab') {
    $token = $_POST['token'] ?? $_GET['token'] ?? '';
    if ($token && isset($_SESSION['admin_tab_tokens'][$token])) {
        unset($_SESSION['admin_tab_tokens'][$token]);
    }
    // if no tabs remain, schedule a pending logout a few seconds in the future
    if (empty($_SESSION['admin_tab_tokens'])) {
        $_SESSION['pending_logout'] = time() + 5; // grace period (seconds)
    }
    header('Content-Type: text/plain');
    echo 'ok';
    exit;
}

// If a pending logout was scheduled and its time has passed, destroy the session now
if (!empty($_SESSION['pending_logout']) && is_numeric($_SESSION['pending_logout'])) {
    if (time() >= (int)$_SESSION['pending_logout']) {
        session_unset();
        session_destroy();
        // redirect to login page
        header('Location: admin.php?action=login');
        exit;
    }
}

if ($action === 'login') {
    include __DIR__ . '/../views/backend/admin_login.php';
    exit;
}

if ($action === 'login_submit') {
    // process POST login
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $tv = new ThanhVienModel();
    $user = $tv->findByEmailNormalized(trim($email));
    $allowed = ['admin', 'editor'];
    $ok = false;
    $failureMessages = [];
    if ($user) {
        $role = isset($user['quyen']) ? strtolower(trim((string)$user['quyen'])) : '';
        $hash = $user['mat_khau'] ?? null;
        if (! $hash || ! password_verify($password, $hash)) {
            $failureMessages[] = 'Sai email hoặc mật khẩu.';
        } elseif (! in_array($role, $allowed, true)) {
            $failureMessages[] = 'Bạn không có quyền truy cập Admin.';
        } else {
            $ok = true;
        }
    } else {
        $failureMessages[] = 'Sai email hoặc mật khẩu.';
    }

    if ($ok) {
        // Store admin-specific session keys so admin login doesn't overwrite frontend user session
        $_SESSION['admin_user_id'] = $user['id'];
        $_SESSION['admin_user_role'] = $user['quyen'];
        // store normalized admin user data in session for quick access in admin views
        $_SESSION['admin_user'] = $user;
        // flash success to show on login page briefly before redirect
        $_SESSION['flash_success'] = 'Đăng nhập thành công';
        // Ensure frontend session keys are not set by admin login (keep admin/frontend isolated)
        $frontendKeys = ['user', 'user_id', 'id_nguoi_dung', 'ho_ten', 'avatar', 'anh_dai_dien'];
        foreach ($frontendKeys as $k) {
            if (isset($_SESSION[$k])) unset($_SESSION[$k]);
        }
        // Render the login view so the flash is visible briefly; client JS will redirect
        include __DIR__ . '/../views/backend/admin_login.php';
        exit;
    }
    // set a clear failure message (string or array) to be shown on the login page
    if (!empty($failureMessages)) {
        // if there's only one message, store as string for simpler display
        $_SESSION['flash_login_error'] = count($failureMessages) === 1 ? $failureMessages[0] : $failureMessages;
    } else {
        $_SESSION['flash_login_error'] = 'Đăng nhập thất bại.';
    }
    include __DIR__ . '/../views/backend/admin_login.php';
    exit;
}

if ($action === 'logout') {
    // Only remove admin-related session keys so frontend session remains intact
    unset($_SESSION['admin_user_id'], $_SESSION['admin_user_role'], $_SESSION['admin_user']);
    // also clear admin flashes
    unset($_SESSION['flash_success'], $_SESSION['flash_error'], $_SESSION['flash_login_error']);
    header('Location: admin.php?action=login');
    exit;
}

// require authentication for all other admin actions
// Check admin-specific session key first (keeps frontend session separate)
if (empty($_SESSION['admin_user_id'])) {
    header('Location: admin.php?action=login');
    exit;
}

// Determine role and whether the current action is forbidden for Editors.
$role = strtolower(trim((string)($_SESSION['user_role'] ?? '')));
$forbiddenForEditor = ['dashboard', 'index', 'thanh_vien_roles'];
$isForbiddenForEditor = ($role === 'editor' && in_array($action, $forbiddenForEditor, true));

$controller = new ThanhVienController();

switch ($action) {
    case 'dashboard':
        // render the admin layout which will include Dashboard.php fragment
        include __DIR__ . '/../views/backend/layout.php';
        break;
    case 'index':
        if ($isForbiddenForEditor) {
            // Editor: allow clicking but show empty content via layout (will render the permission message)
            include __DIR__ . '/../views/backend/layout.php';
        } else {
            $controller->index();
        }
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
        if ($isForbiddenForEditor) {
            include __DIR__ . '/../views/backend/layout.php';
        } else {
            $controller->index();
        }
        break;

    case 'bai_viet':
        // Quản lí bài viết - delegating to BaiVietController
        $baiVietController = new BaiVietController();
        $baiVietController->index();
        break;

    // Bad-words admin (standalone)
    case 'bad_words':
        // show the admin layout which will include the bad_words fragment
        include __DIR__ . '/../views/backend/layout.php';
        break;

    case 'bad_words_add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $kw = trim($_POST['word'] ?? '');
            if ($kw !== '') {
                try {
                    $bw = new \Website\TinTuc\Models\BadWordsModel();
                    $bw->add($kw);
                    $_SESSION['flash_success'] = 'Thêm từ khoá thành công.';
                } catch (Exception $e) {
                    $_SESSION['flash_error'] = 'Lỗi khi thêm từ khoá.';
                }
            }
        }
        header('Location: admin.php?action=bad_words');
        exit;

    case 'bad_words_delete':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            try {
                $bw = new \Website\TinTuc\Models\BadWordsModel();
                $bw->delete($id);
                $_SESSION['flash_success'] = 'Xóa từ khoá thành công.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Lỗi khi xóa từ khoá.';
            }
        }
        header('Location: admin.php?action=bad_words');
        exit;

    case 'bad_words_update':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $word = trim($_POST['word'] ?? '');
            $active = isset($_POST['active']) ? 1 : 0;
            if ($id > 0 && $word !== '') {
                try {
                    $bw = new \Website\TinTuc\Models\BadWordsModel();
                    $bw->update($id, $word, $active);
                    $_SESSION['flash_success'] = 'Cập nhật thành công.';
                } catch (Exception $e) {
                    $_SESSION['flash_error'] = 'Lỗi khi cập nhật.';
                }
            }
        }
        header('Location: admin.php?action=bad_words');
        exit;

    case 'bad_words_copy':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            try {
                $bw = new \Website\TinTuc\Models\BadWordsModel();
                $bw->copy($id);
                $_SESSION['flash_success'] = 'Chép từ khoá thành công.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Lỗi khi chép từ khoá.';
            }
        }
        header('Location: admin.php?action=bad_words');
        exit;

    case 'bad_words_toggle':
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            try {
                $bw = new \Website\TinTuc\Models\BadWordsModel();
                $bw->toggleActive($id);
                $_SESSION['flash_success'] = 'Đổi trạng thái thành công.';
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Lỗi khi đổi trạng thái.';
            }
        }
        header('Location: admin.php?action=bad_words');
        exit;

    case 'bad_words_apply':
        try {
            // apply censoring on-the-fly is already done in BinhLuanModel->getByPostId
            // but if you want to permanently replace stored comments, implement here.
            $_SESSION['flash_success'] = 'Áp dụng thành công (hiển thị sẽ bị che từ khi xem chi tiết).';
        } catch (Exception $e) {
            $_SESSION['flash_error'] = 'Lỗi khi áp dụng.';
        }
        header('Location: admin.php?action=bad_words');
        exit;

    case 'danh_muc':   
        // Quản lý chuyên mục
        $chuyenMucController = new ChuyenMucController();
        $chuyenMucController->index();
        break; 
    
    case 'tag':
        // Quản lý thẻ tag
        // Handle direct deletion (GET &sub=xoa&id=...) before any output so header() redirects work
        if (($_SERVER['REQUEST_METHOD'] === 'GET') && (($_GET['sub'] ?? '') === 'xoa') && isset($_GET['id'])) {
            try {
                $tagModel = new \Website\TinTuc\Models\TagModel();
                $id = $_GET['id'];
                if ($tagModel->delete($id)) {
                    $_SESSION['flash_success'] = 'Xóa thẻ tag thành công!';
                } else {
                    $_SESSION['flash_error'] = 'Lỗi khi xóa thẻ tag!';
                }
            } catch (Exception $e) {
                $_SESSION['flash_error'] = 'Lỗi khi xóa thẻ tag!';
            }
            header('Location: admin.php?action=tag');
            exit;
        }

        // If this is an AJAX POST for create/update (expects JSON), include the fragment directly
        // so the fragment can return JSON without the surrounding layout HTML.
        $isAjaxPost = ($_SERVER['REQUEST_METHOD'] === 'POST') && !empty($_GET['sub']);
        if ($isAjaxPost) {
            include __DIR__ . '/../views/backend/QuanLyTag.php';
        } else {
            // normal page view: render the admin layout which will include the fragment inside <main class="content">
            include __DIR__ . '/../views/backend/layout.php';
        }
        break;

    // Quản lý bình luận
    case 'binh_luan':
        $blAdminController = new BinhLuanAdminController();
        $blAdminController->index();
        break;

    case 'comment_toggle_status':
        $blAdminController = new BinhLuanAdminController();
        $blAdminController->toggleStatus($_GET['id'] ?? 0);
        break;

    case 'comment_delete':
        $blAdminController = new BinhLuanAdminController();
        $blAdminController->delete($_GET['id'] ?? 0);
        break;

    case 'comment_toggle_status_ajax':
        header('Content-Type: application/json');
        $blAdminController = new BinhLuanAdminController();
        $result = $blAdminController->toggleStatusAjax($_GET['id'] ?? 0);
        echo json_encode($result);
        exit;

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

    case 'qc_toggle_status':
        $qcController = new QuangCaoController();
        $qcController->toggleStatus($_GET['id'] ?? 0);
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

    

    case 'deleteUser':
        $controller->deleteUser();
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
