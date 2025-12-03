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
    font-family: 'Segoe UI', Tahoma, sans-serif; 
    background: #eef2f7; 
    margin:0; 
}

/* HEADER */
.header-bar { 
    width:100%; 
    background:#005fa3; 
    padding:12px 0; 
    text-align:center; 
}
.header-bar h1 { 
    color:white; 
    margin:0; 
    font-size:22px; 
}

/* Container */
.search-container { 
    max-width:1150px; 
    margin:40px auto; 
    background:#fff; 
    padding:30px; 
    border-radius:15px; 
    box-shadow:0 6px 20px rgba(0,0,0,0.08); 
}

.layout-wrapper { 
    display:flex; 
    gap:25px; 
}

.left-content { 
    flex:1; 
}

/* Article item */
.article-item { 
    display:flex; 
    gap:20px; 
    padding:18px; 
    margin-bottom:18px; 
    border-radius:12px; 
    background:#fafafa; 
    border:1px solid #eee; 
    transition:0.25s; 
}

.article-item:hover { 
    background:#fff; 
    border-color:#ccc; 
    transform:translateY(-3px); 
}

.article-img { 
    width:200px; 
    height:130px; 
    border-radius:10px; 
    object-fit:cover; 
}

.back-home { 
    display:inline-block; 
    margin-top:25px; 
    padding:10px 18px; 
    background:#007bff; 
    color:white; 
    border-radius:8px; 
    text-decoration:none; 
    transition:0.25s; 
}

.back-home:hover { 
    background:#005fa3; 
}

.no-post { 
    text-align:center; 
    color:#777; 
    margin-top:40px; 
    font-size:18px; 
    font-style:italic; 
}
</style>

<!-- HEADER -->
<div class="header-bar">
    <h1>Danh sách bài viết đã thích</h1>
</div>

<!-- CONTENT -->
<div class="search-container">
    <div class="layout-wrapper">
        <div class="left-content">
            <?php if (empty($posts)): ?>
                <p class="no-post">Bạn chưa thích bài viết nào.</p>
            <?php else: ?>
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

                <a href="index.php" class="back-home">Trang chủ</a>
            <?php endif; ?>
        </div>
    </div>
</div>
