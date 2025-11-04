<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
</head>
<body>
    <h1>Chào mừng <?= htmlspecialchars($_SESSION['user']['email']) ?> đã đăng nhập!</h1>
    <a href="index.php?action=logout">Đăng xuất</a>
</body>
</html>
