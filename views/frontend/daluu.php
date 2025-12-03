<?php
// Khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) session_start();

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user']['id'])) {
    echo "<p style='text-align:center;color:red;margin-top:50px;'>Bạn cần đăng nhập để xem bài viết đã lưu.</p>";
    return;
}

// Include class Database
require_once __DIR__ . '/../../src/Database.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();

$id = $_SESSION['user']['id'];

$sql = "SELECT b.*, l.id AS id_luu
        FROM luu_bai_viet l
        JOIN bai_viet b ON l.id_bai_viet = b.id
        WHERE l.id_nguoi_dung = ?
        ORDER BY l.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    body { margin:0; font-family:'Segoe UI', Tahoma, sans-serif; background:#f1f5f9; }

    /* HEADER */
    .header-bar { width:100%; background:#005fa3; padding:12px 18px; display:flex; align-items:center; justify-content:space-between; }
    .header-bar h1 { color:white; margin:0; font-size:22px; text-align:center; flex:1; }
    .header-bar .back-home { color:white; text-decoration:none; font-weight:600; background:transparent; padding:6px 10px; border-radius:6px; }

    /* Container */
    .container-saved, .search-container { max-width:1150px; margin:40px auto; background:#fff; padding:30px; border-radius:15px; box-shadow:0 6px 20px rgba(0,0,0,0.08); }
    .layout-wrapper { display:flex; gap:25px; }
    .left-content { flex:1; }

    /* Article item */
    .article-item { display:flex; gap:20px; padding:18px; margin-bottom:18px; border-radius:12px; background:#fff; border:1px solid #e6eef9; transition:0.25s; align-items:flex-start; }
    .article-item:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(10,50,100,0.06); }
    .article-img { width:220px; height:130px; border-radius:10px; object-fit:cover; }

    /* back-home fallback */
    .back-home { display:inline-block; margin-top:0; padding:6px 10px; background:transparent; color:white; border-radius:6px; text-decoration:none; transition:0.25s; }
    .back-home:hover { opacity:0.92; }

    .empty-text, .no-post { text-align:center; color:#777; margin-top:40px; font-size:18px; font-style:italic; }
</style>

<!-- HEADER -->
<div class="header-bar">
    <a href="index.php" class="back-home">Trang chủ</a>
    <h1>Danh sách bài viết đã lưu</h1>
    <div style="width:86px;"></div>
</div>

<!-- CONTENT -->
<div class="container-saved">

    <?php if (!empty($posts)): ?>
        <div class="layout-wrapper">
            <div class="left-content">
                <?php foreach ($posts as $row): ?>
                    <div class="article-item">
                        <img src="<?= !empty($row['anh_dai_dien']) ? 'uploads/' . htmlspecialchars($row['anh_dai_dien']) : htmlspecialchars($row['hinh_anh'] ?? 'https://via.placeholder.com/200x130?text=No+Image') ?>" class="article-img" alt="Ảnh bài">
                        <div>
                            <h3><a href="index.php?action=chi_tiet_bai_viet&id=<?= urlencode($row['id']) ?>"><?= htmlspecialchars($row['tieu_de'] ?? $row['Tieu_de'] ?? '') ?></a></h3>
                            <div><?= htmlspecialchars($row['mo_ta_ngan'] ?? $row['Mo_ta'] ?? '') ?></div>
                            <div>📅 <?= htmlspecialchars($row['ngay_dang'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php else: ?>
        <p class="empty-text">Bạn chưa lưu bài viết nào.</p>
    <?php endif; ?>

    