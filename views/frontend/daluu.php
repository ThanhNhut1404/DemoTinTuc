<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user']['id'])) {
    echo "<p style='text-align:center;color:red;margin-top:50px;'>Bạn cần đăng nhập để xem bài viết đã lưu.</p>";
    return;
}

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
    body {
        background: #f4f6f9;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
    }

    /* HEADER */
    .header-bar {
        width: 100%;
        background: #004a99;
        padding: 15px 20px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .header-title {
        font-size: 22px;
        font-weight: bold;
        color: white;
        margin: 0;
    }

    .header-btn {
        background: white;
        color: #0056c7;
        padding: 8px 14px;
        text-decoration: none;
        border-radius: 6px;
        transition: 0.25s;
        font-weight: bold;
        border: 2px solid transparent;
    }

    .header-btn:hover {
        background: #e6efff;
        border-color: white;
    }

    /* CONTENT */
    .container-saved {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
        margin-top: 30px;
    }

    .page-title {
        font-size: 28px;
        font-weight: bold;
        color: #0056c7;
        text-align: center;
        margin-bottom: 20px;
        letter-spacing: 0.5px;
    }

    /* CARD */
    .post-card {
        background: white;
        border-radius: 14px;
        padding: 20px 25px;
        margin-bottom: 18px;
        border: 1px solid #e0e0e0;
        box-shadow: 0px 4px 18px rgba(0,0,0,0.06);
        transition: 0.25s;
    }
    .post-card:hover {
        transform: translateY(-3px);
        box-shadow: 0px 6px 25px rgba(0,0,0,0.1);
    }

    .post-title {
        font-size: 20px;
        font-weight: bold;
        color: #0055cc;
        text-decoration: none;
    }
    .post-title:hover {
        text-decoration: underline;
        color: #003e96;
    }

    .post-actions {
        margin-top: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-remove {
        padding: 7px 14px;
        background: #0066cc;
        color: #fff !important;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        transition: 0.25s;
    }
    .btn-remove:hover {
        background: #0066cc;
    }

    .empty-text {
        text-align: center;
        color: #777;
        font-size: 18px;
        margin-top: 40px;
        font-style: italic;
    }
</style>


<!-- HEADER -->
<div class="header-bar">
    

    <a href="index.php" class="header-btn">Trang chủ</a>
</div>

<div class="container-saved">

    <h2 class="page-title">Danh sách bài viết đã lưu</h2>

    <?php if (!empty($posts)): ?>
        <?php foreach ($posts as $row): ?>
            <div class="post-card">

                <!-- Bấm vào xem bài viết -->
                <a class="post-title" href="../index.php?action=xem_bai&id=<?= $row['id'] ?>">
                    <?= htmlspecialchars($row['tieu_de']) ?>
                </a>

                <div class="post-actions">
                    <span style="color:#555;">ID bài viết: <?= $row['id'] ?></span>

                    <!-- Bỏ lưu -->
                    <a class="btn-remove" 
                       href="../index.php?action=bo_luu&id_luu=<?= $row['id_luu'] ?>"
                       onclick="return confirm('Bạn có chắc muốn bỏ lưu bài viết này không?');">
                        Bỏ lưu
                    </a>
                </div>

            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <p class="empty-text">Bạn chưa lưu bài viết nào.</p>
    <?php endif; ?>

</div>
