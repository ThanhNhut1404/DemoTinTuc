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

// Lấy danh sách bình luận của user
$sql = "SELECT bl.noi_dung AS comment, bl.ngay_binh_luan, b.tieu_de, b.id AS id_bai
        FROM binh_luan bl
        JOIN bai_viet b ON bl.id_bai_viet = b.id
        WHERE bl.id_nguoi_dung = ?
        ORDER BY bl.ngay_binh_luan DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    body {
        margin: 0;
        background: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
    }

    /* HEADER */
    .header-bar {
        width: 100%;
        background: #004a99;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 6px rgba(0,0,0,0.2);
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .header-title {
        color: white;
        font-size: 22px;
        margin: 0;
        font-weight: bold;
    }

    .header-btn {
        background: white;
        color: #0056c7;
        padding: 8px 14px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: bold;
        transition: 0.25s;
    }

    .header-btn:hover {
        background: #e9f1ff;
    }

    /* CONTENT CONTAINER */
    .my-comments {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 15px;
    }

    .page-title {
        font-size: 28px;
        font-weight: bold;
        color: #0056c7;
        text-align: center;
        margin-bottom: 25px;
    }

    .comment-card {
        background: #fff;
        border: 1px solid #d7e3f8;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        transition: 0.25s;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .comment-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .comment-title a {
        color: #0066cc;
        font-size: 20px;
        font-weight: bold;
        text-decoration: none;
    }

    .comment-title a:hover {
        text-decoration: underline;
    }

    .comment-text {
        margin: 12px 0;
        font-size: 16px;
        color: #444;
        line-height: 1.5;
    }

    .comment-date {
        font-size: 13px;
        color: #777;
    }

    .no-comment {
        text-align: center;
        color: #666;
        margin-top: 40px;
        font-style: italic;
        font-size: 18px;
    }
</style>

<!-- HEADER -->
<div class="header-bar">
  
    <a href="index.php" class="header-btn">Trang chủ</a>
</div>

<!-- PAGE TITLE -->
<div class="my-comments">
    <h2 class="page-title">Danh sách các bài viết đã bình luận</h2>

    <?php if (!empty($data)): ?>
        <?php foreach ($data as $c): ?>
            <div class="comment-card">
                <div class="comment-title">
                    <b>Bài viết:</b>
                    <a href="../index.php?action=xem_bai&id=<?= htmlspecialchars($c['id_bai']) ?>">
                        <?= htmlspecialchars($c['tieu_de']) ?>
                    </a>
                </div>

                <div class="comment-text">
                    <b>Bình luận:</b><br>
                    <?= nl2br(htmlspecialchars($c['comment'])) ?>
                </div>

                <div class="comment-date">
                    <?= htmlspecialchars($c['ngay_binh_luan']) ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <p class="no-comment">Bạn chưa bình luận bài viết nào.</p>
    <?php endif; ?>
</div>
