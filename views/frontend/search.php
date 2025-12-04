<?php
// Bắt session
if (session_status() === PHP_SESSION_NONE) session_start();

// Include config để kết nối DB
require_once __DIR__ . '/../../src/Database.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();

// Khởi tạo biến an toàn
$results = isset($results) && is_array($results) ? $results : [];
$totalResults = $totalResults ?? 0;
$currentPage = $currentPage ?? 1;
$query = htmlspecialchars($query ?? '');

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
.main-header-wrapper { max-width:2000px; margin:0 auto; display:flex; justify-content:flex-start; /* vẫn căn trái */ align-items:center; gap:15px; padding-left:800px; /* nhích sang phải 20px, nghĩa là nhìn tổng thể nhích sang trái so với container */ }

/* Search box */
.header-search-form { display:flex; width:380px; background:white; border-radius:25px; overflow:hidden; border:1px solid #ddd; }
.header-search-form input { flex:1; padding:10px 15px; border:none; font-size:15px; }
.header-search-form button { width:45px; border:none; background:none; cursor:pointer; font-size:18px; color:#005fa3; }

/* Dropdown */
.dropdown { position:relative; }
.dropdown-toggle { color:white; text-decoration:none; padding:8px 12px; border-radius:6px; background:#007bff; font-size:14px; cursor:pointer; user-select:none; }
.dropdown-toggle:hover, .dropdown-toggle:focus { background:#0069d9; }
.dropdown-menu { position:absolute; top:38px; left:0; background:white; min-width:200px; border-radius:8px; border:1px solid #ddd; padding:8px 0; display:none; z-index:200; box-shadow:0 4px 12px rgba(0,0,0,0.15); }
.dropdown-menu a { display:block; padding:10px 15px; color:#333; text-decoration:none; font-size:14px; }
.dropdown-menu a:hover { background:#f2f2f2; }

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
.back-home { display:inline-block; margin-top:25px; padding:10px 18px; background:#007bff; color:white; border-radius:8px; text-decoration:none; transition:0.25s; }
.back-home:hover { background:#005fa3; }

/* Dropdown click */
.dropdown.open .dropdown-menu { display:block; }
</style>
</head>
<body>

<div class="main-header">
    <div class="main-header-wrapper">

        <!-- Search -->
        <form action="index.php" method="GET" class="header-search-form">
            <input type="hidden" name="action" value="search">
            <input type="text" name="q" value="<?= $query ?>" placeholder="Bạn muốn tìm gì hôm nay?">
            <button type="submit">🔍</button>
        </form>

        <!-- Dropdown Chuyên mục -->
        <div class="dropdown" tabindex="0">
            <span class="dropdown-toggle">Chuyên mục ▾</span>
            <div class="dropdown-menu">
                <?php if(!empty($chuyenMuc)): ?>
                    <?php foreach($chuyenMuc as $cm): ?>
                        <a href="index.php?action=chuyenmuc&id=<?= $cm['id'] ?>">
                            <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span style="display:block; padding:10px 15px; color:#999;">Chưa có chuyên mục</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Dropdown Tài khoản -->
        <div class="dropdown" tabindex="0">
            <?php if(isset($_SESSION['user'])): ?>
                <span class="dropdown-toggle">Tài khoản ▾</span>
                <div class="dropdown-menu">
                    <a href="http://localhost/DemoTinTuc/public/index.php?action=userPage">Cập nhật thông tin cá nhân</a>
                     <a href="index.php?action=dathich">Đã thích</a>
                     <a href="index.php?action=daluu">Đã lưu</a>
                     <a href="index.php?action=binhluancuatoi">Bình luận của tôi</a>
                    <a href="index.php?action=logout">Đăng xuất</a>
                </div>
            <?php else: ?>
                <span class="dropdown-toggle">Tài khoản ▾</span>
                <div class="dropdown-menu">
                    <a href="index.php?action=login">Đăng nhập</a>
                    <a href="index.php?action=register">Đăng ký</a>
                </div>
            <?php endif; ?>
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
                    <div class="article-item">
                        <img src="<?= htmlspecialchars($r['hinh_anh'] ?? 'uploads/default.jpg') ?>" class="article-img">
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

                <!-- Nút quay về trang chủ -->
                <a href="index.php" class="back-home">Trang chủ</a>

            <?php endif; ?>
        </div>

    </div>
</div>

<script>
// Mở dropdown khi bấm vào chữ
document.querySelectorAll('.dropdown').forEach(drop => {
    const toggle = drop.querySelector('.dropdown-toggle');
    toggle.addEventListener('click', () => {
        drop.classList.toggle('open');
    });
});
</script>

</body>
</html>
