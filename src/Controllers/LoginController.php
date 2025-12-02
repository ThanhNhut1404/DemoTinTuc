<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Database;
use PDO;

class LoginController
{
    private $conn;

    public function __construct()
    {
        // ✅ Tạo kết nối PDO thực sự từ Database
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        include __DIR__ . '/../../views/login.php';
    }

    // Xử lý đăng nhập
    public function login()
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? '');

        if (empty($email) || empty($password)) {
            echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); history.back();</script>";
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mat_khau'])) {
            // Đăng nhập thành công
            session_start(); // nếu chưa start thì start
            $_SESSION['user'] = $user;
            $_SESSION['id_nguoi_dung'] = $user['id']; // ← QUAN TRỌNG: trang chi tiết đang dùng $_SESSION['id_nguoi_dung']
            $_SESSION['ho_ten'] = $user['ho_ten'] ?? $user['email'];

            // === XỬ LÝ RETURN_URL - ĐÂY LÀ CHỖ BẠN THIẾU ===
            $redirect = 'index.php?action=home'; // mặc định về trang chủ

            if (isset($_GET['return_url']) && !empty($_GET['return_url'])) {
                $return = urldecode($_GET['return_url']);

                // Bảo mật: chỉ cho phép redirect trong cùng website
                if (
                    strpos($return, 'index.php') === 0 ||
                    strpos($return, '/Demotintuc/') !== false ||
                    preg_match('/^index\.php\?action=/', $return)
                ) {
                    $redirect = $return;
                }
            }

            header("Location: $redirect");
            exit;
        } else {
            echo "<script>alert('Sai email hoặc mật khẩu!'); history.back();</script>";
        }
    }

    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        include __DIR__ . '/../../views/register.php';
    }

    // Xử lý đăng ký
    public function register()
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? '');

        if (empty($email) || empty($password)) {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); history.back();</script>";
            return;
        }

        // Kiểm tra trùng email
        $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            echo "<script>alert('❌ Email đã tồn tại!'); history.back();</script>";
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Lưu vào DB
        $stmt = $this->conn->prepare("INSERT INTO nguoi_dung (email, mat_khau) VALUES (?, ?)");
        $stmt->execute([$email, $hashedPassword]);

        header("Location: index.php?action=login");
        exit;

    }

    // Đăng xuất
    public function logout()
    {
        // Đảm bảo session đã được start trước khi destroy
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        header("Location: index.php");
        exit;
    }

    private function checkPasswordStrength($password)
{
    $length = strlen($password);

    // Kiểm tra mạnh
    if (
        $length >= 10 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[\W]/', $password)
    ) {
        return "strong";
    }

    // Kiểm tra trung bình
    if (
        $length >= 8 &&
        preg_match('/[a-zA-Z]/', $password) &&
        preg_match('/[0-9]/', $password)
    ) {
        return "medium";
    }

    // Còn lại là yếu
    return "weak";
}

}
