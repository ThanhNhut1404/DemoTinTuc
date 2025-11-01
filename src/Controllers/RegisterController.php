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
            // 🔹 Đúng tên database của bạn: website_tin_tuc
            $this->conn = new PDO('mysql:host=localhost;dbname=website_tin_tuc;charset=utf8', 'root', '');
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
        // Lấy dữ liệu từ form
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['mat_khau'] ?? '');
        $confirm = trim($_POST['confirm_password'] ?? '');

        // 🔸 1. Kiểm tra dữ liệu
        if (empty($email) || empty($password) || empty($confirm)) {
            echo "<script>alert('⚠️ Vui lòng nhập đầy đủ thông tin!'); window.history.back();</script>";
            exit;
        }

        // 🔸 2. Kiểm tra định dạng email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('❌ Email không hợp lệ!'); window.history.back();</script>";
            exit;
        }

        // 🔸 3. Kiểm tra mật khẩu khớp
        if ($password !== $confirm) {
            echo "<script>alert('❌ Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
            exit;
        }

        try {
            // 🔸 4. Kiểm tra email trùng trong bảng nguoi_dung
            $stmt = $this->conn->prepare("SELECT id FROM nguoi_dung WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<script>alert('❌ Email đã tồn tại, vui lòng dùng email khác!'); window.history.back();</script>";
                return;
            }

            // 🔸 5. Mã hoá mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // 🔸 6. Lưu người dùng mới vào bảng nguoi_dung
            $stmt = $this->conn->prepare("INSERT INTO nguoi_dung (email, mat_khau, ngay_tao) VALUES (?, ?, NOW())");
            $stmt->execute([$email, $hashedPassword]);

            // 🔸 7. Chuyển hướng sau khi đăng ký
            echo "<script>
                    alert('✅ Đăng ký thành công! Hãy đăng nhập.');
                    window.location='index.php?action=login';
                  </script>";
        } catch (Exception $e) {
            echo "⚠️ Lỗi khi đăng ký: " . $e->getMessage();
        }
    }
}
