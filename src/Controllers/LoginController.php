<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Database;
use PDO;

class LoginController
{
    private $conn;

    public function __construct() {
        $this->conn = Database::class;
    }

    // Hiển thị form đăng nhập
    public function showLoginForm() {
        include __DIR__ . '/../../views/login.php';
        
    }

    // ✅ Xử lý đăng nhập
    public function login() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? ''); // ✅ đúng tên input

        if (empty($email) || empty($password)) {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); history.back();</script>";
            return;
        }

        // ✅ Kiểm tra trong database
        $stmt = $this->conn->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // ✅ Nếu có người dùng và đúng mật khẩu
        if ($user && password_verify($password, $user['mat_khau'])) {
            session_start();
            $_SESSION['user'] = $user;

            // ✅ Chuyển hướng thẳng vào trang chủ
            header("Location: index.php?action=home");
            exit;
        } else {
            echo "<script>alert('❌ Sai email hoặc mật khẩu!'); history.back();</script>";
        }
    }

    // Hiển thị form đăng ký
    public function showRegisterForm() {
        include __DIR__ . '/../../views/register.php';
    }

    // ✅ Xử lý đăng ký
    public function register() {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? '');

        if (empty($email) || empty($password)) {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); history.back();</script>";
            return;
        }

        // ✅ Kiểm tra trùng email
        $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo "<script>alert('❌ Email đã tồn tại!'); history.back();</script>";
            return;
        }

        // ✅ Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // ✅ Lưu vào DB — ĐÃ SỬA DÒNG NÀY
        $stmt = $this->conn->prepare("INSERT INTO nguoi_dung (email, mat_khau) VALUES (?, ?)");
        $stmt->execute([$email, $hashedPassword]);

        echo "<script>alert('✅ Đăng ký thành công!'); window.location='index.php?action=login';</script>";
    }

    // ✅ Đăng xuất
    public function logout() {
        session_start();
        session_destroy();
        header("Location: index.php?action=login");
        exit;
    }
}
