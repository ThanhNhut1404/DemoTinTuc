<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ThanhVienModel;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ForgotPasswordController
{
    private $model;

    public function __construct()
    {
        $this->model = new ThanhVienModel();
    }

    // Hiển thị form quên mật khẩu
    public function index()
    {
        include __DIR__ . '/../../views/forgot_password.php';
    }

    // Xử lý gửi link reset
    public function submit()
    {
        $email = $_POST['email'] ?? '';
        if (!$email) die("Email không được để trống");

        try {
            $token = $this->model->createResetToken($email);

            // Link reset chứa token
            $resetLink = "https://yourdomain.com/index.php?controller=forgot_password&action=reset&token=$token";

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'youremail@gmail.com';
            $mail->Password = 'your_app_password'; // App password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('no-reply@yourdomain.com', 'Website Tin Tuc');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Link đặt lại mật khẩu';
            $mail->Body = "Nhấn vào link sau để đặt lại mật khẩu (hết hạn sau 1 giờ):<br>
                           <a href='$resetLink'>$resetLink</a>";

            $mail->send();
            echo "Link reset đã được gửi vào email của bạn";
        } catch (\Exception $e) {
            echo "Gửi mail thất bại: " . $e->getMessage();
        }
    }

    // Hiển thị form reset password
    public function reset()
    {
        $token = $_GET['token'] ?? '';
        $user = $this->model->validateResetToken($token);
        if (!$user) die("Token không hợp lệ hoặc đã hết hạn");

        include __DIR__ . '/../../views/reset_password.php';
    }

    // Xử lý submit mật khẩu mới
    public function submitReset()
    {
        $token = $_POST['token'] ?? '';
        $newPassword = $_POST['password'] ?? '';

        if (!$token || !$newPassword) die("Dữ liệu không hợp lệ");

        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $this->model->resetPasswordByToken($token, $hashed);

        echo "✅ Mật khẩu đã được thay đổi thành công! Bạn có thể <a href='index.php?controller=login&action=showLoginForm'>đăng nhập</a> ngay.";
    }
}
