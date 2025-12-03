<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user = $_SESSION['user'] ?? null;
if (!$user) {
    header('Location: index.php?action=login');
    exit;
}
$name = $user['ho_ten'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Chỉnh sửa hồ sơ</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;background:#f4f6f8;padding:40px}
        .card{max-width:600px;margin:0 auto;background:#fff;padding:20px;border-radius:8px;box-shadow:0 4px 12px rgba(0,0,0,0.06)}
        input{width:100%;padding:10px;margin:8px 0;border-radius:6px;border:1px solid #ccc}
        button{padding:10px 14px;background:#007bff;color:#fff;border:none;border-radius:6px}
    </style>
</head>
<body>
<div class="card">
    <h2>Chỉnh sửa hồ sơ</h2>
    <form action="index.php?action=profile_update" method="post">
        <label>Tên</label>
        <input type="text" name="name" value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>" required>
        <div style="margin-top:12px">
            <button type="submit">Lưu</button>
            <a href="index.php?action=profile" style="margin-left:8px">Hủy</a>
        </div>
    </form>
</div>
</body>
</html>