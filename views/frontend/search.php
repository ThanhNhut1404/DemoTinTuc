<?php
// Khởi tạo biến an toàn
$results = isset($results) && is_array($results) ? $results : [];
$totalResults = $totalResults ?? 0;
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 5;
$query = htmlspecialchars($query ?? '');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm: "<?= $query ?>"</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #eef2f7;
            margin: 0;
        }

        .header-title {
            text-align: center;
            padding: 20px 0;
            font-size: 34px;
            font-weight: bold;
            color: #0077cc;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .search-container {
            max-width: 1150px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        .ad-banner img {
            width: 100%;
            height: 135px;
            object-fit: cover;
            border-radius: 10px;
        }

        /* TOP BAR + SEARCH FORM */
        .top-bar {
            display: flex;
            justify-content: center; /* căn giữa toàn bộ */
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .home-btn {
            font-size: 18px;
            font-weight: 500;
            color: #333333;
            text-decoration: none;
            flex-shrink: 0;
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex: 1; /* chiếm phần còn lại */
            max-width: 600px; /* giới hạn độ rộng */
        }

        .search-form input[type="text"] {
            flex: 1; /* input chiếm hết chiều ngang có thể */
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .search-form button {
            padding: 10px 18px;
            border-radius: 8px;
            border: none;
            background: #0077cc;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.25s;
        }

        .search-form button:hover {
            background: #005fa3;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
        }

        .layout-wrapper {
            display: flex;
            gap: 25px;
        }
        .left-content {
            flex: 3;
        }
        .right-sidebar {
            flex: 1.2;
            display: flex;
            flex-direction: column;
            gap: 18px;
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        /* SIDEBAR QUẢNG CÁO */
        .sidebar-ad {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
        }
        .sidebar-ad img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid #ddd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .sidebar-ad img:hover {
            transform: scale(1.03);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .article-item {
            display: flex;
            gap: 20px;
            padding: 18px;
            margin-bottom: 18px;
            border-radius: 12px;
            background: #fafafa;
            border: 1px solid #eee;
            transition: 0.25s;
        }
        .article-item:hover {
            background: #fff;
            border-color: #ccc;
            transform: translateY(-3px);
        }

        .article-img {
            width: 200px;
            height: 130px;
            border-radius: 10px;
            object-fit: cover;
        }

        .article-content h3 a {
            text-decoration: none;
            color: #0077cc;
        }

        .article-content .article-desc {
            margin: 8px 0;
            color: #555;
        }

        .article-content .article-date {
            font-size: 14px;
            color: #888;
        }

        .pagination {
            margin-top: 25px;
            text-align: center;
        }
        .pagination a, .pagination strong {
            margin: 0 5px;
            padding: 9px 15px;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            color: #0077cc;
            border: 1px solid #0077cc;
        }

        @media (max-width: 700px) {
            .layout-wrapper { flex-direction: column; }
            .right-sidebar { position: static; }
            .article-item { flex-direction: column; align-items: center; }
            .article-img { width: 100%; height: 200px; }
        }
    </style>
</head>
<body>

<div class="header-title">WEBSITE TIN TỨC</div>

<div class="search-container">

    <div class="ad-banner">
        <img src="uploads/đầutrangjpg.jpg" alt="Quảng cáo">
    </div>

    <div class="top-bar">
        <a href="index.php" class="home-btn">Trang chủ</a>

        <!-- Thanh tìm kiếm cân bằng -->
        <form action="index.php" method="GET" class="search-form">
            <input type="hidden" name="action" value="search">
            <input type="text" name="q" value="<?= $query ?>" placeholder="Tìm kiếm bài viết...">
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>

    <h1>Kết quả tìm kiếm cho: "<?= $query ?>"</h1>

    <div class="layout-wrapper">
        
        <!-- LEFT CONTENT -->
        <div class="left-content">
            <?php if (empty($results)): ?>
                <p>Không tìm thấy bài viết nào.</p>
            <?php else: ?>
                <?php foreach ($results as $index => $r): ?>
                    <div class="article-item">
                        <img src="<?= htmlspecialchars($r['hinh_anh'] ?? 'uploads/default.jpg') ?>" 
                             class="article-img" alt="Ảnh minh họa">
                        <div class="article-content">
                            <h3>
                                <a href="index.php?action=chi_tiet_bai_viet&id=<?= urlencode($r['id']) ?>">
                                    <?= htmlspecialchars($r['tieu_de']) ?>
                                </a>
                            </h3>
                            <div class="article-desc"><?= htmlspecialchars($r['mo_ta_ngan'] ?? '') ?></div>
                            <div class="article-date">📅 <?= htmlspecialchars($r['ngay_dang'] ?? '') ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- RIGHT SIDEBAR QUẢNG CÁO -->
        <div class="right-sidebar">
            <div class="sidebar-ad">
                <img src="uploads/haha.jpg" alt="Quảng cáo 1">
                <img src="uploads/quangcao.jpg" alt="Quảng cáo 2">
                <img src="uploads/3.jpg" alt="Quảng cáo 3">
            </div>
        </div>

    </div>
</div>

</body>
</html>
