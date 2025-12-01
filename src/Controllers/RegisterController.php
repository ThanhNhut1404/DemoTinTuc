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

    public function handleRegister()
    {
        // Lấy dữ liệu từ form
        $ho_ten  = trim($_POST['ho_ten'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? '');
        $confirm  = trim($_POST['confirm_password'] ?? '');

        // Kiểm tra dữ liệu
        if (empty($ho_ten) || empty($email) || empty($password) || empty($confirm)) {
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

        try {

            // Kiểm tra email tồn tại
            $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<script>alert('❌ Email đã tồn tại!'); window.history.back();</script>";
                return;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // INSERT chuẩn
            $stmt = $this->conn->prepare("
                INSERT INTO nguoi_dung (ho_ten, email, mat_khau, ngay_tao) 
                VALUES (?, ?, ?, NOW())
            ");

            $stmt->execute([$ho_ten, $email, $hashedPassword]);

            echo "<script>
                    alert('✅ Đăng ký thành công! Hãy đăng nhập.');
                    window.location='index.php?action=login';
                </script>";

        } catch (Exception $e) {
            echo '⚠️ Lỗi khi đăng ký: ' . $e->getMessage();
        }
    }
}
