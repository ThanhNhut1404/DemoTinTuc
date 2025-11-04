<?php
include __DIR__ . '/../config.php';
session_start();

if (!isset($_POST['action']) || !isset($_POST['id_bai_viet'])) {
    header('Location: ../index.php');
    exit;
}

$id_bai_viet = (int)$_POST['id_bai_viet'];
$action = $_POST['action'];

switch ($action) {
    case 'binh_luan':
        if (!isset($_SESSION['id_nguoi_dung'])) {
            header("Location: ../login.php");
            exit;
        }

        $noi_dung = trim($_POST['noi_dung']);
        if ($noi_dung !== '') {
            $id_nguoi_dung = $_SESSION['id_nguoi_dung'];
            $stmt = $conn->prepare("INSERT INTO binh_luan (id_bai_viet, id_nguoi_dung, noi_dung, ngay_binh_luan) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iis", $id_bai_viet, $id_nguoi_dung, $noi_dung);
            $stmt->execute();
        }
        break;

    case 'yeu_thich':
        if (!isset($_SESSION['id_nguoi_dung'])) {
            header("Location: ../login.php");
            exit;
        }

        $id_nguoi_dung = $_SESSION['id_nguoi_dung'];

        // Kiểm tra xem đã thích chưa
        $check = $conn->query("SELECT * FROM yeu_thich WHERE id_bai_viet = $id_bai_viet AND id_nguoi_dung = $id_nguoi_dung");
        if ($check->num_rows == 0) {
            $conn->query("INSERT INTO yeu_thich (id_bai_viet, id_nguoi_dung, ngay_yeu_thich) VALUES ($id_bai_viet, $id_nguoi_dung, NOW())");
        }
        break;
}

// Quay lại trang chi tiết
header("Location: ../views/chi_tiet_bai_viet.php?id=$id_bai_viet");
exit;
