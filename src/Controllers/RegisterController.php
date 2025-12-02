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

        if ($length >= 10 &&
    preg_match('/[A-Z]/', $password) &&
    preg_match('/[a-z]/', $password) &&
    preg_match('/[0-9]/', $password) &&
    preg_match('/[\W]/', $password)
) {
    return "strong";
}


        if ($length >= 8 && preg_match('/[a-zA-Z]/', $password) && preg_match('/[0-9]/', $password)) {
            return "medium";
        }

        return "weak";
    }

    /** ===============================
     *  XỬ LÝ ĐĂNG KÝ – MỚI VÀ ĐẦY ĐỦ
     *  =============================== */
    public function handleRegister()
    {
        $ho_ten  = trim($_POST['ho_ten']);
        $email   = trim($_POST['email']);
        $password = trim($_POST['mat_khau']);
        $confirm  = trim($_POST['confirm_password']);
        $gioi_tinh  = trim($_POST['gioi_tinh']);
        $ngay_sinh = trim($_POST['ngay_sinh']);

        $errors = [];

        // Validate cơ bản
        if (empty($ho_ten) || empty($email) || empty($password) || empty($confirm)) {
            $errors[] = "⚠️ Vui lòng nhập đầy đủ thông tin!";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "❌ Email không hợp lệ!";
        }

        if ($password !== $confirm) {
            $errors[] = "❌ Mật khẩu xác nhận không khớp!";
        }

        // Nếu có lỗi → show lại form
        if (!empty($errors)) {
            $errorMessage = implode("<br>", $errors);
            include __DIR__ . "/../../views/register.php";
            return;
        }

        // Check email trùng
        $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email=?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errorMessage = "Email đã tồn tại!";
            include __DIR__ . "/../../views/register.php";
            return;
        }

        // Hash password
        $hashed = password_hash($password, PASSWORD_BCRYPT);

        // Insert DB
        $stmt = $this->conn->prepare("
            INSERT INTO nguoi_dung (ho_ten,email,mat_khau,gioi_tinh,ngay_sinh,ngay_tao)
            VALUES (?,?,?,?,?,NOW())
        ");
        $stmt->execute([$ho_ten,$email,$hashed,$gioi_tinh,$ngay_sinh]);

        // Thành công → hiện luôn trong trang
        $successMessage = "Đăng ký thành công!";
        include __DIR__ . "/../../views/register.php";
    }
}
