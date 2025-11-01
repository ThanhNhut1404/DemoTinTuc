<?php
namespace Website\TinTuc\Controllers;

class LoginController
{
    // Hiển thị form đăng nhập
    public function showLoginForm()
    {
        include __DIR__ . '/../../views/login.php';
    }

    // Xử lý đăng nhập
    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if ($email === 'admin@gmail.com' && $password === '123456') {
            session_start();
            $_SESSION['user'] = 'admin';
            echo "<script>alert('Đăng nhập thành công!'); window.location='index.php?action=home';</script>";
        } else {
            echo "<script>alert('Sai email hoặc mật khẩu!'); window.location='index.php?action=login';</script>";
        }
    }

    // Hiển thị form đăng ký
    public function showRegisterForm()
    {
        include __DIR__ . '/../../views/register.php';
    }

    // Xử lý đăng ký (giả lập)
    public function register()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!empty($email) && !empty($password)) {
            echo "<script>alert('Đăng ký thành công cho $email!'); window.location='index.php?action=login';</script>";
        } else {
            echo "<script>alert('Vui lòng nhập đầy đủ thông tin!'); window.location='index.php?action=register';</script>";
        }
    }

    // Đăng xuất
    public function logout()
    {
        session_start();
        session_destroy();
        echo "<script>alert('Đã đăng xuất!'); window.location='index.php?action=login';</script>";
    }
}
