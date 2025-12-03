<?php
// Simple profile view
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
if (!$user) {
    header('Location: index.php?action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Hồ sơ của bạn</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;padding:40px}
        .card{max-width:600px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.06)}
        .row{display:flex;justify-content:space-between;align-items:center}
        a.btn{display:inline-block;padding:8px 12px;border-radius:6px;background:#007bff;color:#fff;text-decoration:none}
    </style>
</head>
<body>
<div class="card">
    <h2>Hồ sơ người dùng</h2>
    <p><strong>Tên:</strong> <?= htmlspecialchars($user['ho_ten'] ?? ($user['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></p>
    <div class="row" style="margin-top:16px">
        <div>
            <a class="btn" href="index.php?action=profile_edit">Sửa hồ sơ</a>
            <a class="btn" href="index.php?action=logout" style="background:#6c757d;margin-left:8px">Đăng xuất</a>
        </div>
    </div>
</div>
</body>
</html>