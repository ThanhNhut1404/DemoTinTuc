<!-- views/frontend/_header.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Tin tức hôm nay'; ?></title>
    <link rel="stylesheet" href="/public/assets/css/frontend.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        .header-top {
            background-color: #0d6efd; /* màu xanh dương chuẩn Bootstrap primary */
            padding: 1rem 0;
        }
        .search-box {
            max-width: 500px;
            width: 100%;
        }
        .search-box input {
            border: none;
            border-radius: 50px 0 0 50px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }
        .search-box button {
            border: none;
            background-color: #0d6efd;
            color: white;
            border-radius: 0 50px 50px 0;
            padding: 0.75rem 1.2rem;
        }
        .btn-custom {
            background-color: #0d6efd;
            color: white;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .btn-custom:hover {
            background-color: #0b5ed7;
            color: white;
        }
        .logo-title {
            font-size: 2.8rem;
            font-weight: 900;
            color: #0d6efd;
            text-align: center;
            margin: 2rem 0 1rem;
            letter-spacing: 1px;
        }
        .tagline {
            background-color: #e7f0ff;
            color: #0d6efd;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            display: inline-block;
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body style="background-color:#f8f9fa;">

<!-- Header xanh phía trên -->
<header class="header-top">
    <div class="container d-flex align-items-center justify-content-end gap-3">
        <!-- Thanh tìm kiếm -->
        <form class="search-box d-flex me-auto" action="/tim-kiem" method="GET">
            <input type="text" name="q" class="form-control" placeholder="Bạn muốn tìm gì hôm nay?" required>
            <button type="submit">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                </svg>
            </button>
        </form>
        <!-- Nút Tài khoản -->
        <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
            <a href="/views/logout.php" class="btn btn-custom">Đăng xuất</a>
        <?php else: ?>
            <a href="index.php?action=login" class="btn btn-custom">Đăng Nhập</a>
        <?php endif; ?>
    </div>
</header>

<!-- Phần tiêu đề lớn ở giữa -->
<div class="container text-center my-4">
    <h1 class="logo-title">Website Tin Tức</h1>
    <div class="tagline">Cập nhật tin tức mới nhất, nhanh chóng & chính xác</div>
</div>

<!-- Menu chuyên đề (giữ nguyên file cũ của bạn) -->
<?php include __DIR__ . '/../views/frontend/chu_de.php'; ?>