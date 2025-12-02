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
    'banner' => __DIR__ . '/QuanLyBanner.php',
    'bg_wallpaper' => __DIR__ . '/QuanLyBgWallpaper.php',
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
    <link rel="stylesheet" href="/DemoTinTuc/public/assets/admin.css?v=<?= time() ?>">
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
                <a href="admin.php?action=banner" class="<?= ($action === 'banner') ? 'active' : '' ?>"><span class="icon">🖼️</span><span class="label">Quản lý Banner</span></a>
                <a href="admin.php?action=bg_wallpaper" class="<?= ($action === 'bg_wallpaper') ? 'active' : '' ?>"><span class="icon">🎨</span><span class="label">Quản lý Nền</span></a>
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
                    <?php
                    $displayName = 'Admin';
                    if (!empty($_SESSION['user']['ho_ten'] ?? null)) {
                        $displayName = $_SESSION['user']['ho_ten'];
                    } elseif (!empty($_SESSION['user']['email'] ?? null)) {
                        $displayName = $_SESSION['user']['email'];
                    } elseif (!empty($_SESSION['user_id'])) {
                        try {
                            $tv = new \Website\TinTuc\Models\ThanhVienModel();
                            $u = $tv->findById($_SESSION['user_id']);
                            if ($u) {
                                $displayName = $u['ho_ten'] ?? $u['email'] ?? $displayName;
                            }
                        } catch (Exception $e) {
                            // ignore and fallback to 'Admin'
                        }
                    }
                    ?>
                    <div class="user-area">
                        <div class="greeting" style="font-size:14px;color:var(--muted);margin-right:8px">Xin chào, <?= htmlspecialchars($displayName) ?></div>
                        <div class="user-menu-wrapper" style="position:relative">
                            <button id="userMenuToggle" class="btn btn-hamburger" aria-expanded="false" aria-label="Mở menu người dùng">
                                <!-- user icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </button>
                            <div id="userMenu" class="user-menu" aria-hidden="true">
                                <a href="admin.php?action=settings" class="user-menu-item">Cài đặt</a>
                                <a href="admin.php?action=userPage" class="user-menu-item">Thông Tin</a>
                                <a href="admin.php?action=logout" class="user-menu-item">Đăng Xuất</a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="content">
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="admin-flash admin-flash-success"><?= htmlspecialchars($_SESSION['flash_success']) ?></div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>
                <?php if (isset($fragments[$action]) && file_exists($fragments[$action])): ?>
                    <?php include $fragments[$action]; ?>
                <?php else: ?>
                    <div class="card">Trang không tìm thấy</div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <script src="/DemoTinTuc/public/assets/admin.js"></script>
    <script>
        (function(){
            const toggle = document.getElementById('userMenuToggle');
            const menu = document.getElementById('userMenu');
            if (!toggle || !menu) return;
            function closeMenu(){ menu.classList.remove('open'); toggle.setAttribute('aria-expanded','false'); menu.setAttribute('aria-hidden','true'); }
            function openMenu(){ menu.classList.add('open'); toggle.setAttribute('aria-expanded','true'); menu.setAttribute('aria-hidden','false'); }
            toggle.addEventListener('click', function(e){ e.stopPropagation(); if (menu.classList.contains('open')) closeMenu(); else openMenu(); });
            document.addEventListener('click', function(){ closeMenu(); });
            menu.addEventListener('click', function(e){ e.stopPropagation(); });
        })();
    </script>
</body>
</html>
