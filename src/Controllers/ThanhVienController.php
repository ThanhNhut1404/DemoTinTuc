<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ThanhVienModel;

class ThanhVienController
{
    private $model;

    public function __construct()
    {
        $this->model = new ThanhVienModel();
    }

    // Hiển thị danh sách
    public function index()
    {
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null; // trạng thái filter (Hoat_dong / Khoa / ...)
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null; // giới tính filter
        $dsThanhVien = $this->model->getAll($role, $status, $gender);
        include __DIR__ . '/../../views/backend/Thanh_Vien.php';
    }

    // Khóa / Mở khóa tài khoản
    public function khoaMoTaiKhoan()
    {
        $id = $_GET['id'] ?? null;
        $hanhDong = $_GET['hanhDong'] ?? null;
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null;

        if ($id && $hanhDong) {
            // Model chỉ có phương thức toggleStatus để chuyển trạng thái
            $this->model->toggleStatus($id);
        }

        // Trả về trang quản lý (dùng admin.php khi đang test cục bộ)
        $loc = 'admin.php?action=index';
        if ($role) $loc .= '&role=' . urlencode($role);
        if ($status) $loc .= '&status=' . urlencode($status);
        if ($gender) $loc .= '&gender=' . urlencode($gender);
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
        $status = $_POST['status'] ?? null;
        $gender = $_POST['gender'] ?? $_POST['gioi_tinh'] ?? null;

        if ($id && $quyen) {
            $this->model->updateRole($id, $quyen);
        }

        // Trả về trang quản lý admin
        $loc = 'admin.php?action=index';
        if ($role) $loc .= '&role=' . urlencode($role);
        if ($status) $loc .= '&status=' . urlencode($status);
        if ($gender) $loc .= '&gender=' . urlencode($gender);
        header("Location: $loc");
        exit;
    }

    // Wrapper methods để tương thích với router trong public/index.php
    public function search()
    {
        $keyword = trim($_GET['keyword'] ?? '');
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null;

        if ($keyword === '') {
            // nếu không có từ khóa, show all (có thể kèm role/status/gender)
            $dsThanhVien = $this->model->getAll($role, $status, $gender);
            include __DIR__ . '/../../views/backend/Thanh_Vien.php';
            return;
        }

        // gọi model search và include view với kết quả (có thể kèm role/status/gender)
        $dsThanhVien = $this->model->search($keyword, $role, $status, $gender);
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
}
