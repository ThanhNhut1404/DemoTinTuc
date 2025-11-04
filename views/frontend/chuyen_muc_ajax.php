<?php
// Đảm bảo biến có tồn tại để tránh warning
$cmInfo = isset($cmInfo) ? $cmInfo : [];
$tinTheoChuyenMuc = isset($tinTheoChuyenMuc) ? $tinTheoChuyenMuc : [];
$totalPages = isset($totalPages) ? $totalPages : 1;
$page = isset($page) ? $page : 1;
?>

<div class="section">
    <h2>📰 Bài viết thuộc chuyên mục: 
        <?= htmlspecialchars($cmInfo['ten_chuyen_muc'] ?? 'Không xác định') ?>
    </h2>

    <?php if (empty($tinTheoChuyenMuc)): ?>
        <p>Chưa có bài viết nào trong chuyên mục này.</p>
    <?php else: ?>
        <?php foreach ($tinTheoChuyenMuc as $tin): ?>
            <div class="tin">
                <img src="<?= htmlspecialchars($tin['anh_dai_dien']) ?>" alt="">
                <div>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" 
                        style="text-decoration:none;color:#005fa3;font-weight:bold;">
                        <?= htmlspecialchars($tin['tieu_de']) ?>
                    </a>
                    <small>🗓 <?= htmlspecialchars($tin['ngay_dang']) ?> | 👁 <?= htmlspecialchars($tin['luot_xem']) ?></small>
                    <p><?= htmlspecialchars(mb_strimwidth($tin['mo_ta_ngan'] ?? '', 0, 150, '...')) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- PHÂN TRANG -->
<?php if ($totalPages > 1): ?>
    <div class="pagination" style="text-align:center;margin-top:15px;">
        <?php if ($page > 1): ?>
            <a href="#" class="page-link" data-page="<?= $page - 1 ?>" 
               style="padding:6px 10px;border:1px solid #ccc;border-radius:4px;margin:2px;">« Trước</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="#" class="page-link <?= $i == $page ? 'active' : '' ?>" 
               data-page="<?= $i ?>"
               style="padding:6px 10px;border:1px solid #ccc;border-radius:4px;margin:2px;
               <?= $i == $page ? 'background:#0077cc;color:white;' : '' ?>">
               <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="#" class="page-link" data-page="<?= $page + 1 ?>" 
               style="padding:6px 10px;border:1px solid #ccc;border-radius:4px;margin:2px;">Sau »</a>
        <?php endif; ?>
    </div>
<?php endif; ?>
