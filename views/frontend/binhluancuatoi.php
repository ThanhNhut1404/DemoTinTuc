<?php
// Khởi động session nếu chưa bật
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user']['id'])) {
    echo "<p style='text-align:center;color:red;margin-top:50px;'>Bạn cần đăng nhập để xem bình luận của mình.</p>";
    return;
}

// Include DB
require_once __DIR__ . '/../../src/Database.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();

// Lấy ID người dùng
$id = $_SESSION['user']['id'];

// Lấy danh sách bình luận của user (kèm thông tin bài viết: ảnh, mô tả ngắn, ngày đăng)
$sql = "SELECT bl.noi_dung AS comment, bl.ngay_binh_luan, b.tieu_de, b.id AS id_bai, b.anh_dai_dien, b.mo_ta_ngan, b.ngay_dang
    FROM binh_luan bl
    JOIN bai_viet b ON bl.id_bai_viet = b.id
    WHERE bl.id_nguoi_dung = ?
    ORDER BY bl.ngay_binh_luan DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    body { margin:0; font-family:'Segoe UI', Tahoma, sans-serif; background:#f1f5f9; }

    .header-bar { width:100%; background:#005fa3; padding:12px 18px; display:flex; align-items:center; justify-content:space-between; }
    .header-bar h1 { color:white; margin:0; font-size:22px; text-align:center; flex:1; }
    .header-bar .back-home { color:white; text-decoration:none; font-weight:600; background:transparent; padding:6px 10px; border-radius:6px; }

    .my-comments { max-width:1100px; margin:30px auto; padding:18px; }
    .page-title { font-size:26px; color:#0b5fa5; margin-bottom:20px; text-align:center; }

    /* Article item (match search.php) */
    .article-item { display:flex; gap:18px; align-items:flex-start; background:#fff; border-radius:12px; padding:14px; border:1px solid #e6eef9; margin-bottom:16px; transition:0.18s; }
    .article-item:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(10,50,100,0.06); }
    .article-img { width:220px; height:130px; object-fit:cover; border-radius:10px; flex-shrink:0; background:#f0f2f5; }

    .article-body { flex:1; }
    .article-title { margin:0 0 8px 0; font-size:18px; }
    .article-title a { color:#0b66c2; text-decoration:none; font-weight:700; }
    .article-title a:hover { text-decoration:underline; }

    .article-desc { color:#4b5563; margin-bottom:10px; }
    .comment-snippet { color:#374151; font-size:15px; margin-top:6px; background:#f8fafc; padding:10px; border-radius:8px; border:1px solid #eef2f7; }

    .meta-row { display:flex; gap:12px; align-items:center; color:#6b7280; font-size:13px; margin-top:10px; }
    .no-comment { text-align:center; color:#6b7280; margin-top:40px; font-style:italic; }
</style>

<!-- HEADER -->
<div class="header-bar">
    <a href="index.php" class="back-home">Trang chủ</a>
    <h1>Danh sách các bài viết đã bình luận</h1>
    <div style="width:86px;"></div>
</div>

<!-- PAGE TITLE -->
<div class="my-comments">

    <?php if (!empty($data)): ?>
        <?php foreach ($data as $c): ?>
            <article class="article-item">
                <?php $img = !empty($c['anh_dai_dien']) ? $c['anh_dai_dien'] : 'public/uploads/wallpapers/default.jpg'; ?>
                <a href="index.php?action=chi_tiet_bai_viet&id=<?= htmlspecialchars($c['id_bai']) ?>">
                    <img class="article-img" src="<?= htmlspecialchars($img) ?>" alt="">
                </a>

                <div class="article-body">
                    <h3 class="article-title">
                        <a href="index.php?action=chi_tiet_bai_viet&id=<?= htmlspecialchars($c['id_bai']) ?>"><?= htmlspecialchars($c['tieu_de']) ?></a>
                    </h3>

                    <div class="article-desc"><?= htmlspecialchars($c['mo_ta_ngan'] ?? '') ?></div>

                    <div class="comment-snippet">
                        <strong>Bình luận:</strong>
                        <div style="margin-top:6px;"><?= nl2br(htmlspecialchars($c['comment'])) ?></div>
                    </div>

                    <div class="meta-row">
                        <div>Ngày bình luận: <?= htmlspecialchars($c['ngay_binh_luan']) ?></div>
                        <div>·</div>
                        <div>Ngày bài: <?= htmlspecialchars($c['ngay_dang'] ?? '') ?></div>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>

    <?php else: ?>
        <p class="no-comment">Bạn chưa bình luận bài viết nào.</p>
    <?php endif; ?>

    