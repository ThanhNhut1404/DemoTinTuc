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

    public function index()
    {
        include __DIR__ . '/../../views/forgot_password.php';
    }

    public function submit()
    {
        $email = $_POST['email'] ?? '';

        if (!$email) {
            die("Email không được để trống");
        }

        $token = $this->model->createResetToken($email);
        if (!$token) {
            die("Email không tồn tại trong hệ thống!");
        }

        $resetLink = "http://localhost/DemoTinTuc/public/index.php?action=reset&token=$token";

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'YOUR_EMAIL@gmail.com';
            $mail->Password   = 'APP_PASSWORD';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Quan trọng
            $mail->setFrom('YOUR_EMAIL@gmail.com', 'Website Tin Tuc');

            // Sửa đúng biến nhận email
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = "Reset mật khẩu";
            $mail->Body    = "Click vào link để đặt lại mật khẩu: $resetLink";

            $mail->send();
            echo 'Sent!';
        } catch (Exception $e) {
            echo "Error: {$mail->ErrorInfo}";
        }
    }

    public function reset()
    {
        $token = $_GET['token'] ?? '';
        $user = $this->model->validateResetToken($token);

        if (!$user) {
            die("Token không hợp lệ hoặc đã hết hạn.");
        }

        include __DIR__ . '/../../views/reset_password.php';
    }

    public function submitReset()
    {
        $token = $_POST['token'];
        $password = $_POST['password'];

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $this->model->resetPasswordByToken($token, $hashed);

        echo "Đổi mật khẩu thành công! <a href='index.php?action=login'>Đăng nhập</a>";
    }
}
