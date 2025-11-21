<?php
require_once __DIR__ . '/../config/database.php';
session_start();

use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect(); // $conn là PDO instance

// --- Kiểm tra dữ liệu gửi đến ---
if (!isset($_POST['action']) || !isset($_POST['id_bai_viet'])) {
    header('Location: ../index.php');
    exit;
}

$id_bai_viet = (int)$_POST['id_bai_viet'];
$action = $_POST['action'];

// --- Yêu cầu đăng nhập cho các hành động có tương tác ---
if (in_array($action, ['yeu_thich', 'luu_bai', 'binh_luan']) && !isset($_SESSION['id_nguoi_dung'])) {
    header("Location: ../login.php");
    exit;
}

$id_nguoi_dung = (int)($_SESSION['id_nguoi_dung'] ?? 0);

switch ($action) {
    // ====================================================
    // 🗨️ Xử lý bình luận
    // ====================================================
    case 'binh_luan':
        $noi_dung = trim($_POST['noi_dung'] ?? '');
        if ($noi_dung !== '') {
            $stmt = $conn->prepare("
                INSERT INTO binh_luan (id_bai_viet, id_nguoi_dung, noi_dung, ngay_binh_luan)
                VALUES (:id_bv, :id_nd, :noi_dung, NOW())
            ");
            $stmt->execute([
                ':id_bv' => $id_bai_viet,
                ':id_nd' => $id_nguoi_dung,
                ':noi_dung' => $noi_dung
            ]);
        }
        break;

    // ====================================================
    // ❤️ Xử lý thích / bỏ thích bài viết
    // ====================================================
    case 'yeu_thich':
        // Kiểm tra đã thích chưa
        $stmt = $conn->prepare("
            SELECT id FROM yeu_thich 
            WHERE id_bai_viet = :id_bv AND id_nguoi_dung = :id_nd
        ");
        $stmt->execute([
            ':id_bv' => $id_bai_viet,
            ':id_nd' => $id_nguoi_dung
        ]);

        if ($stmt->rowCount() === 0) {
            // Chưa thích → thêm mới
            $insert = $conn->prepare("
                INSERT INTO yeu_thich (id_bai_viet, id_nguoi_dung, ngay_yeu_thich)
                VALUES (:id_bv, :id_nd, NOW())
            ");
            $insert->execute([
                ':id_bv' => $id_bai_viet,
                ':id_nd' => $id_nguoi_dung
            ]);
        } else {
            // Đã thích → bỏ thích (toggle)
            $delete = $conn->prepare("
                DELETE FROM yeu_thich 
                WHERE id_bai_viet = :id_bv AND id_nguoi_dung = :id_nd
            ");
            $delete->execute([
                ':id_bv' => $id_bai_viet,
                ':id_nd' => $id_nguoi_dung
            ]);
        }
        break;

    // ====================================================
    // 💾 Xử lý lưu / bỏ lưu bài viết
    // ====================================================
    case 'luu_bai':
        // Kiểm tra đã lưu chưa
        $stmt = $conn->prepare("
            SELECT id FROM luu_bai 
            WHERE id_bai_viet = :id_bv AND id_nguoi_dung = :id_nd
        ");
        $stmt->execute([
            ':id_bv' => $id_bai_viet,
            ':id_nd' => $id_nguoi_dung
        ]);

        if ($stmt->rowCount() === 0) {
            // Chưa lưu → thêm mới
            $insert = $conn->prepare("
                INSERT INTO luu_bai (id_bai_viet, id_nguoi_dung, ngay_luu)
                VALUES (:id_bv, :id_nd, NOW())
            ");
            $insert->execute([
                ':id_bv' => $id_bai_viet,
                ':id_nd' => $id_nguoi_dung
            ]);
        } else {
            // Đã lưu → bỏ lưu (toggle)
            $delete = $conn->prepare("
                DELETE FROM luu_bai 
                WHERE id_bai_viet = :id_bv AND id_nguoi_dung = :id_nd
            ");
            $delete->execute([
                ':id_bv' => $id_bai_viet,
                ':id_nd' => $id_nguoi_dung
            ]);
        }
        break;

    // ====================================================
    // ❌ Mặc định: hành động không hợp lệ
    // ====================================================
    default:
        header('Location: ../index.php');
        exit;
}

// Quay lại trang chi tiết
header("Location: /DemoTinTuc/views/chi_tiet_bai_viet.php?id=$id_bai_viet");

exit;
