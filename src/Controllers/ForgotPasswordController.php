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

        // Load mail config if available
        $mailConfig = [];
        if (file_exists(__DIR__ . '/../../config.php')) {
            include __DIR__ . '/../../config.php';
            if (!empty($mailConfig) && is_array($mailConfig)) {
                // ok
            }
        }

        // Fallback defaults
        $host = $mailConfig['host'] ?? 'smtp.gmail.com';
        $username = $mailConfig['username'] ?? 'YOUR_EMAIL@gmail.com';
        $password = $mailConfig['password'] ?? 'APP_PASSWORD';
        $port = $mailConfig['port'] ?? 587;
        $from = $mailConfig['from'] ?? $username;
        $fromName = $mailConfig['from_name'] ?? 'Website Tin Tuc';
        $secure = ($mailConfig['secure'] ?? 'tls');

        try {
            $mail = new PHPMailer(true);

            $mail->isSMTP();
            $mail->Host       = $host;
            $mail->SMTPAuth   = true;
            // Force LOGIN auth to avoid CRAM-MD5 issues with some servers
            $mail->AuthType   = 'LOGIN';
            $mail->Username   = $username;
            $mail->Password   = $password;
            if (strtolower($secure) === 'ssl') {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            } else {
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            }
            $mail->Port       = $port;

            // Ensure UTF-8 encoding for subject/body to avoid garbled characters
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';

            // Allow automatic STARTTLS negotiation if server supports it
            $mail->SMTPAutoTLS = true;

            $mail->setFrom($from, $fromName);
            $mail->addAddress($email);

            // Disable verbose SMTP debug for production / user view
            $mail->SMTPDebug = 0;

            $mail->isHTML(true);
            $mail->Subject = "Reset mật khẩu";
            $mail->Body    = "Click vào link để đặt lại mật khẩu: $resetLink";

            $mail->send();
            echo 'Gửi email thành công.';
        } catch (Exception $e) {
            // Log details for admin/developer, but show a friendly message to user
            error_log('Mail Error: ' . ($mail->ErrorInfo ?? 'N/A'));
            error_log('Exception: ' . $e->getMessage());
            echo 'Lỗi gửi email. Vui lòng thử lại sau.';
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
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$token || !$password) {
            echo "Dữ liệu không hợp lệ.";
            return;
        }

        $user = $this->model->validateResetToken($token);
        if (!$user) {
            echo "Token không hợp lệ hoặc đã hết hạn.";
            return;
        }

        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $this->model->resetPasswordByToken($token, $hashed);

        // Log the user in automatically using the normalized user data
        $email = $user['email'] ?? null;
        if ($email) {
            $normalized = $this->model->findByEmailNormalized($email);
            if ($normalized) {
                $_SESSION['user'] = $normalized;
            }
        }

        // Redirect to profile (or home) after successful reset+login
        header('Location: index.php?action=profile');
        exit;
    }
}
