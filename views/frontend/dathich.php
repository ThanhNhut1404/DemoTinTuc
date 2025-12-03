<?php
// Khởi động session nếu chưa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra người dùng đã đăng nhập
if (!isset($_SESSION['user']['id'])) {
    echo "<p style='text-align:center;color:red;margin-top:50px;'>Bạn cần đăng nhập để xem bài viết đã thích.</p>";
    return;
}

// Include class Database
require_once __DIR__ . '/../../src/Database.php';
use Website\TinTuc\Database;

// Tạo kết nối PDO
$db = new Database();
$conn = $db->connect();

// Lấy ID người dùng
$id = $_SESSION['user']['id'];

// Truy vấn bài viết đã thích
$sql = "SELECT b.*
        FROM yeu_thich y
        JOIN bai_viet b ON y.id_bai_viet = b.id
        WHERE y.id_nguoi_dung = ?
        ORDER BY y.id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    /* CONTENT */
    .liked-posts {
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

    .post-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid #d7e3f8;
        margin-bottom: 18px;
        transition: 0.25s;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
    }

    .post-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.12);
    }

    .post-card a {
        text-decoration: none;
        color: #0066cc;
        font-size: 20px;
        font-weight: bold;
    }

    .post-card a:hover {
        text-decoration: underline;
    }

    .no-post {
        text-align: center;
        color: #777;
        margin-top: 40px;
        font-size: 18px;
        font-style: italic;
    }
</style>

<!-- HEADER -->
<div class="header-bar">
    
    <a href="index.php" class="header-btn">Trang chủ</a>
</div>

<!-- CONTENT -->
<div class="liked-posts">

    <h2 class="page-title">Danh sách bài viết đã thích</h2>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $row): ?>
            <div class="post-card">
                <a href="index.php?action=xem_bai&id=<?= htmlspecialchars($row['id']) ?>">
                    <?= htmlspecialchars($row['Tieu_de'] ?? $row['tieu_de']) ?>
                </a>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <p class="no-post">Bạn chưa thích bài viết nào.</p>
    <?php endif; ?>
</div>
