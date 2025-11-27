<?php
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

    <!-- CSS giao diện đẹp đã gộp -->
    <style>
    body {
        font-family: Arial, sans-serif;
        background: #f5f6fa;
        margin: 0;
        padding: 0;
    }

    .search-container {
        max-width: 900px;
        margin: 30px auto;
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .search-container h1 {
        font-size: 26px;
        color: #333;
        margin-bottom: 20px;
    }

    /* Nút quay lại trang chủ */
    .back-btn {
        display: inline-block;
        margin-bottom: 20px;
        padding: 10px 16px;
        background: #0077cc;
        color: #fff;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
        transition: 0.2s;
    }

    .back-btn:hover {
        background: #005fa3;
    }

    .article-item {
        padding: 18px;
        margin-bottom: 15px;
        border-radius: 10px;
        background: #fafafa;
        border: 1px solid #eee;
        transition: 0.25s;
    }

    .article-item:hover {
        background: #fff;
        border-color: #ccc;
        transform: translateY(-2px);
    }

    .article-item h3 {
        margin: 0 0 8px;
    }

    .article-item h3 a {
        color: #0077cc;
        text-decoration: none;
        font-size: 20px;
        font-weight: bold;
    }

    .article-item h3 a:hover {
        color: #005fa3;
        text-decoration: underline;
    }

    .article-desc {
        color: #555;
        margin-bottom: 6px;
    }

    .article-date {
        color: #888;
        font-size: 13px;
    }

    .pagination {
        margin-top: 20px;
        text-align: center;
    }

    .pagination a, .pagination strong {
        margin: 0 5px;
        padding: 8px 14px;
        border-radius: 6px;
        font-size: 15px;
        text-decoration: none;
        color: #0077cc;
        border: 1px solid #0077cc;
        transition: 0.2s;
    }

    .pagination a:hover {
        background: #0077cc;
        color: #fff;
    }

    .pagination strong {
        background: #0077cc;
        color: #fff;
        border-color: #005fa3;
    }
    </style>
</head>

<body>

<div class="search-container">

    <h1>Kết quả tìm kiếm cho: "<?= $query ?>"</h1>

    <!-- Nút quay lại trang chủ -->
    <a href="index.php" class="back-btn">← Quay lại trang chủ</a>

    <?php if (empty($results)): ?>
        <p>Không tìm thấy bài viết nào.</p>
    <?php else: ?>

        <?php foreach ($results as $r): ?>
            <div class="article-item">
                <h3>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= urlencode($r['id']) ?>">
                        <?= htmlspecialchars($r['tieu_de']) ?>
                    </a>
                </h3>

                <div class="article-desc">
                    <?= htmlspecialchars($r['mo_ta_ngan'] ?? '') ?>
                </div>

                <div class="article-date">
                    Ngày đăng: <?= htmlspecialchars($r['ngay_dang'] ?? '') ?>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Phân trang -->
        <?php
        $totalPages = max(1, (int)ceil($totalResults / $perPage));
        if ($totalPages > 1):
        ?>
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
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
                <?php endif; ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>

</div>


</body>
</html>
