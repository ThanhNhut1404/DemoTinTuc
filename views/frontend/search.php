<?php
// Bắt session
if (session_status() === PHP_SESSION_NONE) session_start();

// Include config để kết nối DB
require_once __DIR__ . '/../../src/Database.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();
// Helper để chuẩn hoá đường dẫn ảnh giống trang chủ
require_once __DIR__ . '/../../src/helpers.php';

// Khởi tạo biến an toàn
$results = isset($results) && is_array($results) ? $results : [];
$totalResults = $totalResults ?? 0;
$currentPage = $currentPage ?? 1;
$query = htmlspecialchars($query ?? '');

// Debug session user when requested
if (!empty($_GET['debug'])) {
    echo "<pre style='background:#fff8e1;border:1px solid #ffd54f;padding:10px;max-width:1000px;margin:10px auto;'>SESSION USER:\n" . htmlspecialchars(print_r($_SESSION['user'] ?? '(not set)', true)) . "</pre>";
}

// Lấy chuyên mục từ DB
$stmt = $conn->query("SELECT id, ten_chuyen_muc FROM chuyen_muc ORDER BY id ASC");
$chuyenMuc = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Kết quả tìm kiếm: "<?= $query ?>"</title>
<style>
body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #eef2f7; margin:0; }

/* HEADER */
.main-header { width:100%; background:#005fa3; padding:12px 0; }
.main-header-wrapper { max-width:2000px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; gap:15px; padding:0 18px; }
    .header-right { display:flex; align-items:center; gap:8px; }

/* Search box */
.header-search-form { display:flex; width:380px; background:white; border-radius:25px; overflow:hidden; border:1px solid #ddd; }
.header-search-form input { flex:1; padding:10px 15px; border:none; font-size:15px; }
.header-search-form button { width:45px; border:none; background:none; cursor:pointer; font-size:18px; color:#005fa3; }

/* Dropdown */
.dropdown { position:relative; }
.dropdown-toggle { color:white; text-decoration:none; padding:8px 10px; border-radius:8px; background:#007bff; font-size:14px; cursor:pointer; user-select:none; display:flex; align-items:center; gap:8px; border:none; }
.dropdown-toggle:hover, .dropdown-toggle:focus { background:#0069d9; }
.dropdown-menu { position:absolute; top:44px; right:0; background:white; min-width:240px; border-radius:10px; border:1px solid #eee; padding:12px; display:none; z-index:200; box-shadow:0 10px 30px rgba(0,0,0,0.12); }
.dropdown-menu a { display:block; padding:8px 12px; color:#333; text-decoration:none; font-size:14px; border-radius:6px; }
.dropdown-menu a:hover { background:#f7f9fb; }

/* Account avatar + card styles */
.account-avatar { width:28px; height:28px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.15); }
.account-name { color:white; font-weight:600; }
.account-card { display:flex; flex-direction:column; gap:10px; }
.account-card-header { display:flex; gap:12px; align-items:center; }
.account-avatar-lg { width:54px; height:54px; border-radius:50%; object-fit:cover; }
.account-info { display:flex; flex-direction:column; }
.account-name-lg { font-weight:700; color:#222; }
.small-link { color:#007bff; text-decoration:none; font-size:13px; }
.small-link:hover { text-decoration:underline; }
.account-actions { display:flex; flex-direction:column; gap:6px; }
.account-actions a { color:#333; padding:8px 10px; border-radius:6px; }
.account-footer { text-align:center; }
.logout-btn { display:inline-block; padding:8px 12px; background:#0066cc; color:white; border-radius:8px; text-decoration:none; }
.logout-btn:hover { background:#c93b3b; }

/* CONTENT */
.search-container { max-width:1150px; margin:40px auto; background:#fff; padding:30px; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.08); }
.layout-wrapper { display:flex; gap:25px; }
.left-content { flex:1; }

/* Article item */
.article-item { display:flex; gap:20px; padding:18px; margin-bottom:18px; border-radius:12px; background:#fafafa; border:1px solid #eee; transition:0.25s; }
.article-item:hover { background:#fff; border-color:#ccc; transform:translateY(-3px); }
.article-img { width:200px; height:130px; border-radius:10px; object-fit:cover; }

/* Pagination */
.pagination { margin-top:25px; text-align:center; }
.pagination a, .pagination strong { margin:0 5px; padding:9px 15px; border-radius:6px; font-size:15px; text-decoration:none; color:#0077cc; border:1px solid #0077cc; }
.pagination strong { background:#0077cc; color:white; }
.pagination a:hover { background:#005fa3; color:white; }

/* Quay về trang chủ */
.back-home { color:white; text-decoration:none; font-weight:600; background:transparent; padding:6px 10px; border-radius:6px; display:inline-block; transition:0.25s; }
.back-home:hover { opacity:0.92; }

/* Dropdown click */
.dropdown.open .dropdown-menu { display:block; }
</style>
</head>
<body>

<div class="main-header">
    <div class="main-header-wrapper">
        <a href="index.php" class="back-home">Trang chủ</a>

        <div class="header-right">
            <!-- Search -->
            <form action="index.php" method="GET" class="header-search-form">
                <input type="hidden" name="action" value="search">
                <input type="text" name="q" value="<?= $query ?>" placeholder="Bạn muốn tìm gì hôm nay?">
                <button type="submit">🔍</button>
            </form>

            <!-- Dropdown Tài khoản (modern compact card) -->
            <div class="dropdown account-dropdown" tabindex="0">
            <?php if(!empty($_SESSION['user'])): ?>
                <?php
                    $user = $_SESSION['user'];
                    // Support various possible fields used across the app
                    $displayName = $user['name'] ?? $user['ten'] ?? $user['ho_ten'] ?? $user['email'] ?? 'Tài khoản';
                    $avatarVal = $user['avatar'] ?? $user['anh_dai_dien'] ?? $user['avatar_url'] ?? $user['anh'] ?? '';
                    // Normalize via helper (may return '../uploads/...' or absolute URL)
                    $avatarUrl = trim((string)$avatarVal) === '' ? '../uploads/no_avatar.png' : img_url($avatarVal);
                    // If helper returned a ../ prefix but page expects public path, remove it (fix relative mismatch)
                    if (strncmp($avatarUrl, '../', 3) === 0) {
                        $avatarUrl = substr($avatarUrl, 3);
                    }
                ?>
                <button class="dropdown-toggle" aria-expanded="false">
                    <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" class="account-avatar">
                    <span class="account-name"><?= htmlspecialchars($displayName) ?></span>
                    <span class="caret">▾</span>
                </button>

                <div class="dropdown-menu">
                    <div class="account-card">
                        <div class="account-card-header">
                            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" class="account-avatar-lg">
                            <div class="account-info">
                                <div class="account-name-lg"><?= htmlspecialchars($displayName) ?></div>
                                <a href="index.php?action=userPage" class="small-link">Cập nhật thông tin</a>
                            </div>
                        </div>
                        <div class="account-actions">
                            <a href="index.php?action=dathich">Đã thích</a>
                            <a href="index.php?action=daluu">Đã lưu</a>
                            <a href="index.php?action=binhluancuatoi">Bình luận của tôi</a>
                        </div>
                        <div class="account-footer">
                            <a href="index.php?action=logout" class="logout-btn">Đăng xuất</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <button class="dropdown-toggle" aria-expanded="false">Đăng nhập / Đăng ký ▾</button>
                <div class="dropdown-menu">
                    <div class="account-card">
                        <div class="account-actions">
                            <a href="index.php?action=login">Đăng nhập</a>
                            <a href="index.php?action=register">Đăng ký</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<div class="search-container">

    <h1>Kết quả tìm kiếm cho: "<?= $query ?>"</h1>

    <div class="layout-wrapper">

        <!-- LEFT CONTENT -->
        <div class="left-content">
            <?php
            // Tag-based suggestions (controller sets $tagSuggestions if any)
            $tagSuggestions = $tagSuggestions ?? [];
            if (!empty($tagSuggestions)): ?>
                <h2>Bài viết theo thẻ liên quan</h2>
                <?php foreach ($tagSuggestions as $r): ?>
                    <div class="article-item">
                        <img src="<?= htmlspecialchars($r['hinh_anh'] ?? 'uploads/default.jpg') ?>" class="article-img">
                        <div>
                            <h3><a href="index.php?action=chi_tiet_bai_viet&id=<?= $r['id'] ?>"><?= htmlspecialchars($r['tieu_de']) ?></a></h3>
                            <div><?= htmlspecialchars(mb_substr(strip_tags($r['mo_ta_ngan'] ?? ''), 0, 140)) ?>...</div>
                            <div>📅 <?= htmlspecialchars($r['ngay_dang'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <hr style="margin:20px 0;border:none;border-top:1px solid #eee">
            <?php endif; ?>

            <?php if (empty($results)): ?>
                <p>Không tìm thấy bài viết nào.</p>
            <?php else: ?>
                <?php foreach ($results as $r): ?>
                    <?php
                    // Build image src consistently with other views
                    $imgSrc = '';
                    if (!empty($r['anh_dai_dien'])) {
                        $imgSrc = 'uploads/' . htmlspecialchars($r['anh_dai_dien']);
                    } elseif (!empty($r['hinh_anh'])) {
                        $imgSrc = htmlspecialchars($r['hinh_anh']);
                    } else {
                        $imgSrc = 'https://via.placeholder.com/200x130?text=No+Image';
                    }
                    ?>
                    <div class="article-item">
                        <img src="<?= $imgSrc ?>" class="article-img" alt="<?= htmlspecialchars($r['tieu_de'] ?? '') ?>">
                        <div>
                            <h3><a href="index.php?action=chi_tiet_bai_viet&id=<?= $r['id'] ?>"><?= htmlspecialchars($r['tieu_de']) ?></a></h3>
                            <div><?= htmlspecialchars($r['mo_ta_ngan']) ?></div>
                            <div>📅 <?= htmlspecialchars($r['ngay_dang']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Pagination -->
                <?php
                $totalPages = ceil($totalResults / $perPage);
                if($totalPages > 1):
                ?>
                    <div class="pagination">
                        <?php if($currentPage > 1): ?>
                            <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=1">Trang đầu</a>
                            <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $currentPage-1 ?>">&laquo; Trước</a>
                        <?php endif; ?>

                        <?php
                        $range = 2;
                        $start = max(1, $currentPage - $range);
                        $end = min($totalPages, $currentPage + $range);
                        for($i=$start; $i<=$end; $i++):
                        ?>
                            <?php if($i == $currentPage): ?>
                                <strong><?= $i ?></strong>
                            <?php else: ?>
                                <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $i ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if($currentPage < $totalPages): ?>
                            <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $currentPage+1 ?>">Sau &raquo;</a>
                            <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $totalPages ?>">Trang cuối</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

             

            <?php endif; ?>
        </div>

    </div>
</div>

<script>
// Dropdown behavior: toggle, click-outside close, Esc to close
document.querySelectorAll('.dropdown').forEach(drop => {
    const toggle = drop.querySelector('.dropdown-toggle');
    const menu = drop.querySelector('.dropdown-menu');
    if (!toggle) return;

    const open = () => {
        drop.classList.add('open');
        toggle.setAttribute('aria-expanded', 'true');
    };
    const close = () => {
        drop.classList.remove('open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    toggle.addEventListener('click', (ev) => {
        ev.stopPropagation();
        drop.classList.contains('open') ? close() : open();
    });

    // Close when clicking outside
    document.addEventListener('click', (ev) => {
        if (!drop.contains(ev.target)) close();
    });

    // Close on Escape
    document.addEventListener('keydown', (ev) => {
        if (ev.key === 'Escape' || ev.key === 'Esc') close();
    });
});
</script>

</body>
</html>
