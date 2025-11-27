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

        /* HEADER WEBSITE */
        .header-title {
            text-align: center;
            padding: 20px 0;
            font-size: 34px;
            font-weight: bold;
            color: #0077cc;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        /* CONTAINER */
        .search-container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }

        /* TOP BAR */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        /* Home button giống hình bạn gửi */
        .home-btn {
            text-decoration: none;
            font-size: 18px;
            font-weight: 500;
            color: #333333;
        }

        .home-btn:hover { color: #000; }

        /* SEARCH BAR */
        .search-form input {
            padding: 12px 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
            width: 260px;
            font-size: 14px;
        }

        .search-form button {
            padding: 12px 18px;
            background: #0077cc;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }

        .search-form button:hover {
            background: #005fa3;
        }

        /* ARTICLE CARD */
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

        .article-content { flex: 1; }

        .article-item h3 a {
            color: #0077cc;
            text-decoration: none;
            font-size: 21px;
            font-weight: bold;
        }

        .article-item h3 a:hover { color: #005fa3; text-decoration: underline; }

        .article-desc { color: #555; font-size: 15px; margin: 6px 0; }

        .article-date { color: #888; font-size: 13px; }

        /* PAGINATION */
        .pagination { margin-top: 25px; text-align: center; }

        .pagination a, .pagination strong {
            margin: 0 5px;
            padding: 9px 15px;
            border-radius: 6px;
            font-size: 15px;
            text-decoration: none;
            color: #0077cc;
            border: 1px solid #0077cc;
            transition: 0.2s;
        }

        .pagination a:hover { background: #0077cc; color: #fff; }
        .pagination strong { background: #0077cc; color: #fff; border-color: #005fa3; }

        @media (max-width: 700px) {
            .article-item { flex-direction: column; align-items: center; }
            .article-img { width: 100%; height: 200px; }
        }
    </style>
</head>
<body>

<!-- HEADER -->
<div class="header-title">WEBSITE TIN TỨC</div>

<div class="search-container">

    <!-- TOP BAR -->
    <div class="top-bar">
        <a href="index.php" class="home-btn">Trang chủ</a>

        <form action="index.php" method="GET" class="search-form" style="display:flex; gap:10px;">
            <input type="hidden" name="action" value="search">
            <input type="text" name="q" value="<?= $query ?>" placeholder="Tìm kiếm bài viết...">
            <button type="submit"> Tìm kiếm</button>
        </form>
    </div>

    <h1>Kết quả tìm kiếm cho: "<?= $query ?>"</h1>

    <?php if (empty($results)): ?>
        <p>Không tìm thấy bài viết nào.</p>
    <?php else: ?>

        <?php foreach ($results as $r): ?>
            <div class="article-item">
                <img src="<?= htmlspecialchars($r['hinh_anh'] ?? 'uploads/default.jpg') ?>" class="article-img" alt="Ảnh minh họa">
                <div class="article-content">
                    <h3>
                        <a href="index.php?action=chi_tiet_bai_viet&id=<?= urlencode($r['id']) ?>">
                            <?= htmlspecialchars($r['tieu_de']) ?>
                        </a>
                    </h3>
                    <div class="article-desc"><?= htmlspecialchars($r['mo_ta_ngan'] ?? '') ?></div>
                    <div class="article-date">📅 Ngày đăng: <?= htmlspecialchars($r['ngay_dang'] ?? '') ?></div>
                </div>
            </div>
        <?php endforeach; ?>

        <?php
        $totalPages = max(1, (int)ceil($totalResults / $perPage));
        if ($totalPages > 1):
        ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=1">« Trang đầu</a>
                    <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $currentPage - 1 ?>">« Trước</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p == $currentPage): ?>
                        <strong><?= $p ?></strong>
                    <?php else: ?>
                        <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $currentPage + 1 ?>">Tiếp »</a>
                    <a href="index.php?action=search&q=<?= urlencode($query) ?>&page=<?= $totalPages ?>">Trang cuối »</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
