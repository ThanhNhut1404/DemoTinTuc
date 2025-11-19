<?php
// Admin layout (Adminator-like): collapsible sidebar, topbar, and content area.
// Map actions to fragment files and include the selected fragment inside the content area.

$action = $_GET['action'] ?? 'dashboard';
$fragments = [
    'dashboard' => __DIR__ . '/Dashboard.php',
    'bai_viet' => __DIR__ . '/QuanLyBaiViet.php',
    'danh_muc' => __DIR__ . '/QuanLyDanhMuc.php',
    'tag' => __DIR__ . '/QuanLyTag.php',
    'binh_luan' => __DIR__ . '/QuanLyBinhLuan.php',
    'quang_cao' => __DIR__ . '/QuanLyQuangCao.php',
    'thanh_vien_roles' => __DIR__ . '/Thanh_Vien.php',
    'index' => __DIR__ . '/Thanh_Vien.php',
    'search' => __DIR__ . '/Thanh_Vien.php',
];
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin</title>
    <link rel="stylesheet" href="/DemoTinTuc/public/assets/admin.css">
</head>
<body>
    <div class="admin-wrap">
        <aside id="sidebar" class="sidebar">
            <div class="brand">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="24" height="24" rx="4" fill="#0ea5e9"/></svg>
                <div class="brand-text">Admin</div>
            </div>
            <nav class="nav">
                <a href="admin.php?action=dashboard" class="<?= ($action === 'dashboard') ? 'active' : '' ?>"><span class="icon">🏠</span><span class="label">Tổng quan</span></a>
                <a href="admin.php?action=bai_viet" class="<?= ($action === 'bai_viet') ? 'active' : '' ?>"><span class="icon">📝</span><span class="label">Quản lý Bài viết</span></a>
                <a href="admin.php?action=danh_muc" class="<?= ($action === 'danh_muc') ? 'active' : '' ?>"><span class="icon">📂</span><span class="label">Quản lý Danh mục</span></a>
                <a href="admin.php?action=tag" class="<?= ($action === 'tag') ? 'active' : '' ?>"><span class="icon">🏷️</span><span class="label">Quản lý thẻ Tag</span></a>
                <a href="admin.php?action=binh_luan" class="<?= ($action === 'binh_luan') ? 'active' : '' ?>"><span class="icon">💬</span><span class="label">Quản lý Bình luận</span></a>
                <a href="admin.php?action=quang_cao" class="<?= ($action === 'quang_cao') ? 'active' : '' ?>"><span class="icon">📣</span><span class="label">Quản lý Quảng cáo</span></a>
                <a href="admin.php?action=thanh_vien_roles" class="<?= ($action === 'thanh_vien_roles' || $action === 'index') ? 'active' : '' ?>"><span class="icon">👥</span><span class="label">Quản lý Thành viên</span></a>
            </nav>
        </aside>

        <div class="main">
            <header class="topbar">
                <div class="left">
                    <button id="sidebarToggle" class="btn" aria-label="Toggle sidebar">☰</button>
                    <input class="search-input" type="text" placeholder="Tìm kiếm..." onkeydown="if(event.key==='Enter'){this.form?.submit && this.form.submit()}" />
                </div>
                <div class="actions">
                    <div style="font-size:14px;color:var(--muted)">Xin chào, Admin</div>
                </div>
            </header>

            <main class="content">
                <?php if (isset($fragments[$action]) && file_exists($fragments[$action])): ?>
                    <?php include $fragments[$action]; ?>
                <?php else: ?>
                    <div class="card">Trang không tìm thấy</div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="/DemoTinTuc/public/assets/admin.js"></script>
</body>
</html>
