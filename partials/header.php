<!-- views/frontend/_header.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Tin tức hôm nay'; ?></title>
    <link rel="stylesheet" href="/public/assets/css/frontend.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body style="background-color:#f8f9fa;">
<header class="bg-white border-bottom shadow-sm py-3 mb-2">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="/public/index.php" class="fs-4 fw-bold text-danger text-decoration-none">WEBSITE TIN TỨC</a>
        <div>
            <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                <a href="/views/logout.php" class="btn btn-outline-secondary btn-sm">Đăng xuất</a>
            <?php else: ?>
                <a href="/views/login.php" class="btn btn-outline-primary btn-sm">Đăng nhập</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php include __DIR__ . '/../views/frontend/chu_de.php'; ?>

