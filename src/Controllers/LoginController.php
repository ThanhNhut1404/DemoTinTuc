<?php

namespace Website\TinTuc\Controllers;

use Website\TinTuc\Database;
use PDO;
use DateTime;

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
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash'] = "❌ Vui lòng nhập đầy đủ thông tin!";
            header("Location: index.php?action=login");
            return;
        }

        $stmt = $this->conn->prepare("SELECT * FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['mat_khau'])) {
            // Đăng nhập thành công
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['user'] = $user;
            $_SESSION['id_nguoi_dung'] = $user['id']; // ← QUAN TRỌNG: trang chi tiết đang dùng $_SESSION['id_nguoi_dung']
            $_SESSION['ho_ten'] = $user['ho_ten'] ?? $user['email'];

            // Hiển thị thông báo đăng nhập thành công với delay
            ?>
            <!DOCTYPE html>
            <html lang="vi">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Đăng nhập thành công</title>
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { 
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        background: linear-gradient(135deg, #007bff 0%, #00c6ff 100%);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        min-height: 100vh;
                        padding: 20px;
                    }
                    .success-box {
                        background: rgba(255, 255, 255, 0.95);
                        width: 100%;
                        max-width: 420px;
                        padding: 45px 40px;
                        border-radius: 26px;
                        border: 1px solid rgba(255, 255, 255, 0.4);
                        backdrop-filter: blur(10px);
                        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12), 0 8px 16px rgba(0, 0, 0, 0.05);
                        text-align: center;
                        animation: slideIn 0.4s ease;
                    }
                    @keyframes slideIn {
                        from { opacity: 0; transform: translateY(20px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    .success-icon { 
                        font-size: 80px;
                        margin-bottom: 20px;
                        animation: bounce 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
                    }
                    @keyframes bounce {
                        0% { transform: scale(0); }
                        50% { transform: scale(1.1); }
                        100% { transform: scale(1); }
                    }
                    h2 {
                        font-size: 24px;
                        color: #333;
                        margin-bottom: 10px;
                        font-weight: 700;
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    }
                    .success-message {
                        font-size: 18px;
                        color: #155724;
                        background: #d4edda;
                        padding: 15px;
                        border-radius: 12px;
                        margin: 15px 0;
                        border: 1px solid #c3e6cb;
                        font-weight: 500;
                    }
                    .email-info {
                        font-size: 15px;
                        color: #666;
                        margin: 15px 0 25px 0;
                        line-height: 1.6;
                    }
                    .email-info strong {
                        color: #333;
                        display: block;
                        font-size: 16px;
                        margin-top: 8px;
                    }
                    .countdown {
                        font-size: 14px;
                        color: #999;
                        margin-top: 20px;
                        padding-top: 20px;
                        border-top: 1px solid #eee;
                    }
                    .countdown-number {
                        font-size: 24px;
                        color: #007bff;
                        font-weight: bold;
                        margin: 0 5px;
                    }
                </style>
            </head>
            <body>
                <div class="success-box">
                    <div class="success-icon">✔️</div>
                    <h2>Đăng nhập</h2>
                    <div class="success-message">Đăng nhập thành công</div>
                    <div class="email-info">
                        Chào mừng
                        <strong><?= htmlspecialchars($user['email']) ?></strong>
                    </div>
                </div>
                <script>
                    let count = 3;
                    setInterval(() => {
                        count--;
                        document.getElementById('countdown').textContent = count;
                    }, 1000);
                    
                    setTimeout(() => {
                        window.location.href = 'index.php?action=home';
                    }, 3000);
                </script>
            </body>
            </html>
            <?php
            exit;
        } else {
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['flash'] = "❌ Sai email hoặc mật khẩu!";
            header("Location: index.php?action=login");
            exit;
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

        // Lấy thêm trường từ form
        $ho_ten = trim($_POST['ho_ten'] ?? '');
        $ngay_sinh = trim($_POST['ngay_sinh'] ?? '');
        $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        // Kiểm tra bắt buộc
        if (empty($email) || empty($password) || empty($ho_ten) || empty($ngay_sinh) || empty($gioi_tinh) || empty($confirm)) {
            $errorMessage = '⚠️ Vui lòng nhập đầy đủ thông tin!';
            include __DIR__ . '/../../views/register.php';
            return;
        }

        // Kiểm tra mật khẩu xác nhận
        if ($password !== $confirm) {
            $errorMessage = '❌ Mật khẩu xác nhận không khớp!';
            include __DIR__ . '/../../views/register.php';
            return;
        }

        // Kiểm tra ngày sinh hợp lệ và trước ngày hôm nay
        $dob = DateTime::createFromFormat('Y-m-d', $ngay_sinh);
        $today = new DateTime('today');
        if (!$dob) {
            $errorMessage = '❌ Ngày sinh không hợp lệ!';
            include __DIR__ . '/../../views/register.php';
            return;
        }
        if ($dob >= $today) {
            $errorMessage = '❌ Ngày sinh phải trước ngày hiện tại!';
            include __DIR__ . '/../../views/register.php';
            return;
        }

        // Kiểm tra trùng email
        $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errorMessage = '❌ Email đã tồn tại!';
            include __DIR__ . '/../../views/register.php';
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Lưu vào DB (bao gồm họ tên, ngày sinh, giới tính nếu có cột)
        $stmt = $this->conn->prepare("INSERT INTO nguoi_dung (ho_ten, ngay_sinh, gioi_tinh, email, mat_khau) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$ho_ten, $ngay_sinh, $gioi_tinh, $email, $hashedPassword]);

        // Chuyển về trang đăng nhập với thông báo thành công
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'] = '✅ Đăng ký thành công! Hãy đăng nhập.';
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
