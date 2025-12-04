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
        // if (!isset($_SESSION['user_id'])) {
        //     $_SESSION['user_id'] = 1;
        // }
        $this->model = new ThanhVienModel();
    }

    // Hiển thị danh sách
    public function index()
    {
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null; // trạng thái filter (Hoat_dong / Khoa / ...)
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null; // giới tính filter
        $dsThanhVien = $this->model->getAll($role, $status, $gender);
        // Render inside the admin layout so shared CSS/JS are loaded
        include __DIR__ . '/../../views/backend/layout.php';
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
            include __DIR__ . '/../../views/backend/layout.php';
            return;
        }

        // gọi model search và include view với kết quả (có thể kèm role/status/gender)
        $dsThanhVien = $this->model->search($keyword, $role, $status, $gender);
        // Nếu có áp dụng bộ lọc mà kết quả = 0, thử bỏ bộ lọc để tránh "lọc quá chặt"
        $filterWarning = null;
        if (empty($dsThanhVien) && ($role || $status || $gender)) {
            $fallback = $this->model->search($keyword, null, null, null);
            if (!empty($fallback)) {
                $filterWarning = "Không tìm thấy với bộ lọc hiện tại. Đang hiển thị kết quả chỉ theo từ khóa.";
                $dsThanhVien = $fallback;
            }
        }
        include __DIR__ . '/../../views/backend/layout.php';
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
    
    // Xóa người dùng
    public function deleteUser()
    {
        $id = $_GET['id'] ?? null;
        $role = $_GET['role'] ?? null;
        $status = $_GET['status'] ?? null;
        $gender = $_GET['gender'] ?? $_GET['gioi_tinh'] ?? null;

        if ($id) {
            $this->model->deleteById($id);
        }

        $loc = 'admin.php?action=index';
        if ($role) $loc .= '&role=' . urlencode($role);
        if ($status) $loc .= '&status=' . urlencode($status);
        if ($gender) $loc .= '&gender=' . urlencode($gender);
        header("Location: $loc");
        exit;
    }
    public function userPage()
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    // Kiểm tra đăng nhập đúng cách
    if (!isset($_SESSION['user']['id'])) {
    header("Location: index.php?action=login");
    exit;
}


    $userId = $_SESSION['user']['id']; // chỉ dùng 1 kiểu session

    $thanhVienModel = new ThanhVienModel();
    $baiVietModel = new BaiVietModel();
    $binhLuanModel = new BinhLuanModel();

    // Lấy dữ liệu
    $user = $thanhVienModel->layThongTinNguoiDung($userId);
    $yeuThich = $baiVietModel->layBaiVietYeuThich($userId);
    $daLuu = $baiVietModel->layBaiVietDaLuu($userId);
    $binhLuan = $binhLuanModel->layBinhLuanTheoNguoiDung($userId);

    include __DIR__ . '/../../views/frontend/Trangnguoidung.php';
}


public function updateProfile()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (session_status() === PHP_SESSION_NONE) session_start();

        // Kiểm tra đúng session user
        if (!isset($_SESSION['user']['id'])) {
    header("Location: index.php?action=login");
    exit;
}

        $id = $_SESSION['user']['id'];

        // Lấy dữ liệu từ form — ❌ BỎ EMAIL
        $hoTen = trim($_POST['ho_ten'] ?? '');
        $ngaySinh = $_POST['ngay_sinh'] ?? null;
        $gioiTinh = $_POST['gioi_tinh'] ?? null;
        $anhDaiDien = null;

        // Xử lý ảnh upload
        if (!empty($_FILES['anh_dai_dien']['name'])) {
            $fileName = time() . '_' . basename($_FILES['anh_dai_dien']['name']);
            $target = __DIR__ . '/../../public/uploads/' . $fileName;

            if (move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $target)) {
                $anhDaiDien = $fileName;
            }
        }

        $model = new ThanhVienModel();

        try {
            // Cập nhật DB — ❌ KHÔNG cập nhật email
            $model->capNhatThongTin($id, $hoTen, $anhDaiDien, $ngaySinh, $gioiTinh);

            // cập nhật session — ❌ KHÔNG đổi email
            $_SESSION['user']['ho_ten'] = $hoTen;
            $_SESSION['user']['ngay_sinh'] = $ngaySinh;
            $_SESSION['user']['gioi_tinh'] = $gioiTinh;
            if ($anhDaiDien !== null) {
                $_SESSION['user']['anh_dai_dien'] = $anhDaiDien;
            }

            $_SESSION['flash_message'] = "Cập nhật thành công!";
        } catch (\Exception $e) {
            $_SESSION['flash_message'] = "⚠️ " . $e->getMessage();
        }

       include __DIR__ . '/../../views/frontend/Trangnguoidung.php';
        exit;
    }
}





}
