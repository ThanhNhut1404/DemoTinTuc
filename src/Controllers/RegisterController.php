<?php
namespace Website\TinTuc\Controllers;

use PDO;
use Exception;

class RegisterController
{
    private $conn;

    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=website_tin_tuc;charset=utf8', 'root', '');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }

    public function showForm()
    {
        include __DIR__ . '/../../views/register.php';
    }

    /** ===============================
     *  HÀM KIỂM TRA ĐỘ MẠNH MẬT KHẨU
     *  =============================== */
    private function checkPasswordStrength($password)
    {
        $length = strlen($password);

        // Mật khẩu mạnh
        if (
            $length >= 10 &&
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[\W]/', $password)
        ) {
            return "strong";
        }

        // Mật khẩu trung bình
        if (
            $length >= 8 &&
            preg_match('/[a-zA-Z]/', $password) &&
            preg_match('/[0-9]/', $password)
        ) {
            return "medium";
        }

        // Mật khẩu yếu
        return "weak";
    }

    /** ===============================
     *  XỬ LÝ ĐĂNG KÝ
     *  =============================== */
    public function handleRegister()
    {
        $ho_ten  = trim($_POST['ho_ten'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');
        $gioi_tinh  = trim($_POST['gioi_tinh'] ?? '');
        $ngay_sinh = trim($_POST['ngay_sinh'] ?? '');

        // Kiểm tra dữ liệu bắt buộc
        if (empty($ho_ten) || empty($email) || empty($password) || empty($confirm)) {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('❌ Email không hợp lệ!'); window.history.back();</script>";
            exit;
        }

        if ($password !== $confirm) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Lỗi',
            text: 'Mật khẩu xác nhận không khớp!',
        });
    </script>";
    return; // ở lại trang hiện tại
}


        // 🔥 KIỂM TRA ĐỘ MẠNH CỦA MẬT KHẨU
        $strength = $this->checkPasswordStrength($password);

        if ($strength === "weak") {
            echo "<script>alert('❌ Mật khẩu quá yếu!\\nYêu cầu tối thiểu: 6 ký tự.'); history.back();</script>";
            exit;
        }

        if ($strength === "medium") {
            echo "<script>alert('⚠️ Mật khẩu trung bình!\\nNên dùng ≥ 10 ký tự + chữ hoa + số + ký tự đặc biệt để mạnh hơn.'); history.back();</script>";
            exit;
        }

        try {
            // Kiểm tra email tồn tại
            $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<script>alert('❌ Email đã tồn tại!'); window.history.back();</script>";
                return;
            }

            // Hash mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // INSERT dữ liệu
            $stmt = $this->conn->prepare("
                INSERT INTO nguoi_dung (ho_ten, email, mat_khau, gioi_tinh, ngay_sinh, ngay_tao)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");

            $stmt->execute([$ho_ten, $email, $hashedPassword, $gioi_tinh, $ngay_sinh]);

            echo "<script>alert('🎉 Đăng ký thành công!');</script>";
            header("Location: index.php?action=login");
            exit;

        } catch (Exception $e) {
            echo '⚠️ Lỗi khi đăng ký: ' . $e->getMessage();
        }
    }
}
