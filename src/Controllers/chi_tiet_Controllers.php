<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php'; // Thêm helpers để dùng base_url (tùy chọn nhưng cực kỳ khuyến khích)

session_start();

use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect(); // PDO instance

// === Kiểm tra dữ liệu đầu vào ===
if (!isset($_POST['action']) || !isset($_POST['id_bai_viet'])) {
    header('Location: ../index.php');
    exit;
}

$id_bai_viet = (int)$_POST['id_bai_viet'];
$action      = $_POST['action'];

// === Bắt buộc đăng nhập cho các hành động tương tác ===
$actions_require_login = ['yeu_thich', 'luu_bai', 'binh_luan'];
if (in_array($action, $actions_require_login) && !isset($_SESSION['id_nguoi_dung'])) {
    $_SESSION['flash_message'] = "Vui lòng đăng nhập để thực hiện hành động này!";
    header("Location: " . base_url('views/auth/login.php'));
    exit;
}

$id_nguoi_dung = (int)$_SESSION['id_nguoi_dung'];

try {
    switch ($action) {

        // Bình luận
        case 'binh_luan':
            $noi_dung = trim($_POST['noi_dung'] ?? '');
            if ($noi_dung === '') {
                throw new Exception("Nội dung bình luận không được để trống!");
            }

            $stmt = $conn->prepare("
                INSERT INTO binh_luan (id_bai_viet, id_nguoi_dung, noi_dung, ngay_binh_luan) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$id_bai_viet, $id_nguoi_dung, $noi_dung]);
            $_SESSION['flash_message'] = "Bình luận thành công!";
            break;

        // Thích / Bỏ thích
        case 'yeu_thich':
            $stmt = $conn->prepare("
                SELECT id FROM yeu_thich 
                WHERE id_bai_viet = ? AND id_nguoi_dung = ?
            ");
            $stmt->execute([$id_bai_viet, $id_nguoi_dung]);

            if ($stmt->rowCount() === 0) {
                $conn->prepare("
                    INSERT INTO yeu_thich (id_bai_viet, id_nguoi_dung, ngay_yeu_thich) 
                    VALUES (?, ?, NOW())
                ")->execute([$id_bai_viet, $id_nguoi_dung]);
                $_SESSION['flash_message'] = "Đã thích bài viết!";
            } else {
                $conn->prepare("
                    DELETE FROM yeu_thich 
                    WHERE id_bai_viet = ? AND id_nguoi_dung = ?
                ")->execute([$id_bai_viet, $id_nguoi_dung]);
                $_SESSION['flash_message'] = "Đã bỏ thích.";
            }
            break;

        // Lưu / Bỏ lưu bài viết
        case 'luu_bai':
            $stmt = $conn->prepare("
                SELECT id FROM luu_bai 
                WHERE id_bai_viet = ? AND id_nguoi_dung = ?
            ");
            $stmt->execute([$id_bai_viet, $id_nguoi_dung]);

            if ($stmt->rowCount() === 0) {
                $conn->prepare("
                    INSERT INTO luu_bai (id_bai_viet, id_nguoi_dung, ngay_luu) 
                    VALUES (?, ?, NOW())
                ")->execute([$id_bai_viet, $id_nguoi_dung]);
                $_SESSION['flash_message'] = "Đã lưu bài viết!";
            } else {
                $conn->prepare("
                    DELETE FROM luu_bai 
                    WHERE id_bai_viet = ? AND id_nguoi_dung = ?
                ")->execute([$id_bai_viet, $id_nguoi_dung]);
                $_SESSION['flash_message'] = "Đã bỏ lưu bài viết.";
            }
            break;

        default:
            header('Location: ../index.php');
            exit;
    }

} catch (Exception $e) {
    $_SESSION['flash_error'] = $e->getMessage();
}

// Redirect đúng về trang chi tiết (file nằm ở views/frontend/)
$redirect_url = base_url("views/frontend/chi_tiet_bai_viet.php?id={$id_bai_viet}");
header("Location: {$redirect_url}");
exit;