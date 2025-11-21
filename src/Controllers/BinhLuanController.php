<?php
// Không có namespace cho Controller
// hoặc bạn tự thêm nếu muốn

use Website\TinTuc\Database;   // <-- Quan trọng
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/BinhLuanModel.php';

class BinhLuanController {

    public function themBinhLuan() {
        session_start();

        // Kiểm tra đăng nhập
        if (!isset($_SESSION['id_nguoi_dung'])) {
            header("Location: /views/login.php");
            exit;
        }

        // Chỉ xử lý POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id_bai_viet = (int)($_POST['id_bai_viet'] ?? 0);
            $noi_dung = trim($_POST['noi_dung'] ?? "");
            $id_nguoi_dung = $_SESSION['id_nguoi_dung'];

            // Check nội dung rỗng
            if ($noi_dung === "") {
                header("Location: /views/chi_tiet_bai_viet.php?id=$id_bai_viet&err=empty");
                exit;
            }

            // **Kết nối database theo namespace**
            $db = new Database();  
            $conn = $db->connect();

            if (!$conn) {
                die("Không thể kết nối database");
            }

            // Gọi Model
            $model = new BinhLuanModel($conn);

            // Lưu bình luận
            $model->add($id_bai_viet, $id_nguoi_dung, $noi_dung);

            // Quay lại trang chi tiết
            header("Location: /views/chi_tiet_bai_viet.php?id=$id_bai_viet");
            exit;
        }
    }
}
