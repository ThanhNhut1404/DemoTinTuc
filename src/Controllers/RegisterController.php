<?php
namespace Website\TinTuc\Controllers;

use PDO;
use Exception;

class RegisterController
{
    private $conn;

    // ✅ Kết nối cơ sở dữ liệu
    public function __construct()
    {
        try {
            $this->conn = new PDO('mysql:host=localhost;dbname=tintuc;charset=utf8', 'root', '');
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e) {
            die("Lỗi kết nối CSDL: " . $e->getMessage());
        }
    }

    // ✅ Hiển thị form đăng ký
    public function showForm()
    {
        include __DIR__ . '/../../views/register.php';
    }

    // ✅ Xử lý đăng ký
    public function handleRegister()
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        // Kiểm tra dữ liệu
        if (empty($email) || empty($password) || empty($confirm)) {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('❌ Email không hợp lệ!'); window.history.back();</script>";
            exit;
        }

        if ($password !== $confirm) {
            echo "<script>alert('❌ Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
            exit;
        }

        // Kiểm tra trùng email
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo "<script>alert('⚠️ Email đã tồn tại!'); window.history.back();</script>";
            exit;
        }

        // Mã hoá mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Lưu vào database
        $stmt = $this->conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->execute([$email, $hashedPassword]);

        echo "<script>
                alert('✅ Đăng ký thành công! Hãy đăng nhập.');
                window.location='index.php?action=login';
              </script>";
    }
}
