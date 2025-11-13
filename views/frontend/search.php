<?php
$results = isset($results) && is_array($results) ? $results : [];
$totalResults = $totalResults ?? 0;
$currentPage = $currentPage ?? 1;
$perPage = $perPage ?? 5;
$query = htmlspecialchars($query ?? '');
?>



<main style="max-width:900px;margin:20px auto;padding:0 15px;">
    <h1>Kết quả tìm kiếm cho: "<?= $query ?>"</h1>

    <?php if (empty($results)): ?>
        <p>Không tìm thấy kết quả nào.</p>
    <?php else: ?>
        <?php foreach ($results as $r): ?>
            <article style="border-bottom:1px solid #eee;padding:12px 0;">
                <h3>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= urlencode($r['id']) ?>">
                        <?= htmlspecialchars($r['tieu_de']) ?>
                    </a>
                </h3>
                <p><?= htmlspecialchars($r['mo_ta_ngan'] ?? '') ?></p>
                <small>Ngày đăng: <?= htmlspecialchars($r['ngay_dang'] ?? '') ?></small>
            </article>
        <?php endforeach; ?>

        <!-- Phân trang -->
        <?php
        $totalPages = max(1, (int)ceil($totalResults / $perPage));
        if ($totalPages > 1):
        ?>
            <nav class="pagination" style="margin-top:16px;">
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
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</main>
