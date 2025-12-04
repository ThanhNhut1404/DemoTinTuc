<?php
// Admin layout (Adminator-like): collapsible sidebar, topbar, and content area.
// Map actions to fragment files and include the selected fragment inside the content area.

$action = $_GET['action'] ?? 'dashboard';
$fragments = [
    'dashboard' => __DIR__ . '/Dashboard.php',
    'bai_viet' => __DIR__ . '/QuanLyBaiViet.php',
    'danh_muc' => __DIR__ . '/QuanLyDanhMuc.php',
    'tag' => __DIR__ . '/QuanLyTag.php',
        'bad_words' => __DIR__ . '/QuanLyTuKhoaXau.php',
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
            <a href="admin.php?action=dashboard" class="brand" style="text-decoration:none;display:flex;align-items:center;gap:10px" aria-label="Trang tổng quan" title="Trang tổng quan">
                <div class="brand-icon" style="display:flex;align-items:center;gap:10px">
                    <!-- simple shield icon -->
                    <svg width="28" height="28" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <path d="M12 2L4 5v6c0 5 3.58 9.74 8 12 4.42-2.26 8-7 8-12V5l-8-3z" fill="#ffffff"/>
                        <path d="M8 9c0-2.21 1.79-4 4-4s4 1.79 4 4v2c0 2.21-1.79 4-4 4s-4-1.79-4-4V9z" fill="#ffffff" opacity="0"/>
                    </svg>
                </div>
                <div class="brand-text">ADMIN</div>
            </a>
                <nav class="nav">
                <a href="admin.php?action=dashboard" class="<?= ($action === 'dashboard') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5L12 3l9 6.5"/><path d="M9 22V12h6v10"/></svg>
                    </span>
                    <span class="label">Tổng quan</span>
                </a>
                <a href="admin.php?action=bai_viet" class="<?= ($action === 'bai_viet') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h8"/></svg>
                    </span>
                    <span class="label">Quản lý Bài viết</span>
                </a>
                <a href="admin.php?action=danh_muc" class="<?= ($action === 'danh_muc') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 7v13a2 2 0 0 0 2 2h14V7"/><path d="M3 7a2 2 0 0 1 2-2h3l2 2h9"/></svg>
                    </span>
                    <span class="label">Quản lý Danh mục</span>
                </a>
                <a href="admin.php?action=tag" class="<?= ($action === 'tag') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41L11 3 3 11l8.59 8.59a2 2 0 0 0 2.83 0L20.59 16.24a2 2 0 0 0 0-2.83z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>
                    </span>
                    <span class="label">Quản lý thẻ Tag</span>
                </a>
                <a href="admin.php?action=bad_words" class="<?= ($action === 'bad_words') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                    </span>
                    <span class="label">Quản lý Từ khoá xấu</span>
                </a>
                <a href="admin.php?action=binh_luan" class="<?= ($action === 'binh_luan') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </span>
                    <span class="label">Quản lý Bình luận</span>
                </a>
                <a href="admin.php?action=quang_cao" class="<?= ($action === 'quang_cao') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11v2a2 2 0 0 0 2 2h3l7 4V7L8 11H5a2 2 0 0 0-2 2z"/></svg>
                    </span>
                    <span class="label">Quản lý Quảng cáo</span>
                </a>
                <a href="admin.php?action=banner" class="<?= ($action === 'banner') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="14" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 21l-5-5-2 2-3-3-5 5"/></svg>
                    </span>
                    <span class="label">Quản lý Banner</span>
                </a>
                <a href="admin.php?action=bg_wallpaper" class="<?= ($action === 'bg_wallpaper') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 3H3v14h18V3z"/><path d="M3 17l5-5 4 4 5-7 5 7"/></svg>
                    </span>
                    <span class="label bg-label">Quản lý Background</span>
                </a>
                <a href="admin.php?action=thanh_vien_roles" class="member-link <?= ($action === 'thanh_vien_roles' || $action === 'index') ? 'active' : '' ?>">
                    <span class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </span>
                    <span class="label">Quản lý Thành viên</span>
                </a>
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
                    // Prefer admin-specific session keys but fall back to legacy keys if present
                    if (!empty($_SESSION['admin_user']['ho_ten'] ?? null)) {
                        $displayName = $_SESSION['admin_user']['ho_ten'];
                    } elseif (!empty($_SESSION['admin_user']['email'] ?? null)) {
                        $displayName = $_SESSION['admin_user']['email'];
                    } elseif (!empty($_SESSION['admin_user_id'])) {
                        try {
                            $tv = new \Website\TinTuc\Models\ThanhVienModel();
                            $u = $tv->findById($_SESSION['admin_user_id']);
                            if ($u) {
                                $displayName = $u['ho_ten'] ?? $u['email'] ?? $displayName;
                            }
                        } catch (Exception $e) {
                            // ignore and fallback
                        }
                    } else {
                        // legacy fallback (in case some code still sets frontend session keys)
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
                                // ignore
                            }
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
                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="admin-flash admin-flash-error"><?= htmlspecialchars($_SESSION['flash_error']) ?></div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>
                <?php
                // If the logged-in user is an Editor and the action is restricted, show an inline 'no permission' message
                $currentRole = strtolower(trim((string)($_SESSION['user_role'] ?? '')));
                $restrictedForEditor = ['dashboard', 'index', 'thanh_vien_roles'];
                if ($currentRole === 'editor' && in_array($action, $restrictedForEditor, true)) {
                    // show an inline, two-line permission notice (no background)
                    echo '<div class="admin-flash admin-flash-error">'
                        . '<div class="admin-flash-title">Truy cập bị từ chối</div>'
                        . '<div class="admin-flash-message">Bạn không có quyền truy cập vào chức năng này. Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là nhầm lẫn!</div>'
                        . '</div>';
                } else {
                    if (isset($fragments[$action]) && file_exists($fragments[$action])) {
                        include $fragments[$action];
                    } else {
                        echo '<div class="card">Trang không tìm thấy</div>';
                    }
                }
                ?>
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
