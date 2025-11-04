<?php
namespace Website\TinTuc\Controllers;

use Website\TinTuc\Models\ThanhVienModel;

class ForgotPasswordController
{
    public function index()
    {
        include __DIR__ . '/../../views/forgot_password.php';

    }

    public function submit()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email']);

        // ✅ Sửa đúng tên class model
        $userModel = new ThanhVienModel();
        $user = $userModel->findByEmail($email);

        if ($user) {
            // Tạo mật khẩu tạm
            $newPassword = substr(md5(time()), 0, 8);
            $userModel->updatePassword($email, $newPassword);

            echo "<script>alert('Mật khẩu tạm thời của bạn là: $newPassword'); window.location='index.php?action=login';</script>";
        } else {
            echo "<script>alert('Email không tồn tại trong hệ thống!'); window.history.back();</script>";
        }
    }
}
}
