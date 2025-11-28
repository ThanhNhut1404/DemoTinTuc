<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ThanhVienModel;

class ProfileController
{
    private $model;

    public function __construct() {
        $this->model = new ThanhVienModel();
    }

    // Hiển thị trang cập nhật hồ sơ
    public function edit() {
        session_start();
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_SESSION['user']['id'];
        $user = $this->model->layThongTinNguoiDung($id);

        include __DIR__ . "/../../views/profile.php";
    }

    // Xử lý cập nhật
    public function update() {
        session_start();
        if (!isset($_SESSION['user']['id'])) {
            header("Location: index.php?action=login");
            exit;
        }

        $id = $_SESSION['user']['id'];
        $hoTen = $_POST['ho_ten'];
        $email = $_POST['email'];
        $ngaySinh = $_POST['ngay_sinh'];
        $gioiTinh = $_POST['gioi_tinh'];

        $avatarName = null;

        if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] > 0) {
            $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $avatarName = "avatar_" . $id . "_" . time() . "." . $ext;

            move_uploaded_file(
                $_FILES['avatar']['tmp_name'],
                __DIR__ . "/../../public/uploads/avatars/" . $avatarName
            );
        }

        try {
            $this->model->capNhatThongTin($id, $hoTen, $email, $avatarName, $ngaySinh, $gioiTinh);

            $_SESSION['success'] = "Cập nhật thông tin thành công!";
            header("Location: index.php?action=edit_profile");
            exit;
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            header("Location: index.php?action=edit_profile");
            exit;
        }
    }
}
