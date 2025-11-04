<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ThanhVienModel;
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\BinhLuanModel;


class ThanhVienController
{
    private $model;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
        }
        $this->model = new ThanhVienModel();
    }

    // Hiển thị danh sách
    public function index()
    {
        $role = $_GET['role'] ?? null;
        $dsThanhVien = $this->model->getAll($role);
        include __DIR__ . '/../../views/backend/Thanh_Vien.php';
    }

    // Khóa / Mở khóa tài khoản
    public function khoaMoTaiKhoan()
    {
        $id = $_GET['id'] ?? null;
        $hanhDong = $_GET['hanhDong'] ?? null;
        $role = $_GET['role'] ?? null;

        if ($id && $hanhDong) {
            // Model chỉ có phương thức toggleStatus để chuyển trạng thái
            $this->model->toggleStatus($id);
        }

        // Trả về trang quản lý (dùng admin.php khi đang test cục bộ)
        $loc = 'admin.php?action=index' . ($role ? '&role=' . urlencode($role) : '');
        header("Location: $loc");
        exit;
    }

    // Phân quyền
    public function phanQuyen()
    {
        $id = $_POST['id'] ?? null;
        // Chấp nhận cả 'quyen' (view mới) và 'role' (nếu có)
        $quyen = $_POST['quyen'] ?? $_POST['role'] ?? null;
        $role = $_POST['role'] ?? null; // preserve filter role if form sent it

        if ($id && $quyen) {
            $this->model->updateRole($id, $quyen);
        }

        // Trả về trang quản lý admin
        $loc = 'admin.php?action=index' . ($role ? '&role=' . urlencode($role) : '');
        header("Location: $loc");
        exit;
    }

    // Wrapper methods để tương thích với router trong public/index.php
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $role = $_GET['role'] ?? null;
        if ($keyword === '') {
            // nếu không có từ khóa, show all (có thể kèm role)
            $dsThanhVien = $this->model->getAll($role);
            include __DIR__ . '/../../views/backend/Thanh_Vien.php';
            return;
        }

        // gọi model search và include view với kết quả
        $dsThanhVien = $this->model->search($keyword, $role);
        include __DIR__ . '/../../views/backend/Thanh_Vien.php';
    }

    public function updateRole()
    {
        // Gọi lại xử lý phân quyền
        $this->phanQuyen();
    }

    public function lock()
    {
        // Gọi xử lý khóa/mở tài khoản (khi action là 'khoa_tk')
        $this->khoaMoTaiKhoan();
    }

    public function unlock()
    {
        // Gọi xử lý khóa/mở tài khoản (khi action là 'mo_tk')
        $this->khoaMoTaiKhoan();
    }
      public function userPage()
{
    // đảm bảo session
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) $_SESSION['user_id'] = 1;

    $thanhVienModel = new ThanhVienModel();
    $baiVietModel = new BaiVietModel();
    $binhLuanModel = new BinhLuanModel();

    $user = $thanhVienModel->layThongTinNguoiDung($_SESSION['user_id']);
    $yeuThich = $baiVietModel->layBaiVietYeuThich($_SESSION['user_id']);
    $daLuu = $baiVietModel->layBaiVietDaLuu($_SESSION['user_id']);
    $binhLuan = $binhLuanModel->layBinhLuanTheoNguoiDung($_SESSION['user_id']);

          include __DIR__ . '/../../views/frontend/Trangnguoidung.php';
}
public function updateProfile() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $id = $_SESSION['user_id'];
        $hoTen = $_POST['ho_ten'];
        $email = $_POST['email'];
        $anhDaiDien = null;

        if (!empty($_FILES['anh_dai_dien']['name'])) {
            $fileName = basename($_FILES['anh_dai_dien']['name']);
            $target = __DIR__ . '/../../public/uploads/' . $fileName;
            move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $target);
            $anhDaiDien = $fileName;
        }

        $model = new ThanhVienModel();
        $model->capNhatThongTin($id, $hoTen, $email, $anhDaiDien);

        // 🔹 Ghi thông báo vào session flash
        $_SESSION['flash_message'] = "✅ Cập nhật thông tin thành công!";

        // 🔹 Redirect lại (tránh việc người dùng refresh gửi lại form)
        header("Location: admin.php?action=userPage");
        exit;
    }
}
}