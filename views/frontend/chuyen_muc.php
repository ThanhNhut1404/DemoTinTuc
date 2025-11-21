<?php
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
use Website\TinTuc\Models\QuangcaoModel;
$qcModel = new QuangcaoModel();
$dsQuangCao = $qcModel->getQuangCaoTheoViTri('Sidebar');
$dsQuangCaoTrangChu = $qcModel->getQuangCaoTheoViTri('Trang_chu');

// Prepare unified ads list for rotating slots (ensure 4 items)
$allAds = array_values(array_filter(array_merge($dsQuangCao, $dsQuangCaoTrangChu)));
$ads = [];
if (!empty($allAds)) {
    $take = array_slice($allAds, 0, 4);
    while (count($take) < 4) {
        $take = array_merge($take, $allAds);
        $take = array_slice($take, 0, 4);
    }
    $ads = $take;
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?> - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <style>
    /* Local tweaks for category page layout */
    main { display:flex; gap:18px; align-items:flex-start; }
    main > aside { width:22%; }
    main > section { width:56%; }
    .tin-link { display:block; text-decoration:none; color:inherit; }
    .tin { padding:12px; transition: transform .18s ease, box-shadow .18s ease; }
    .tin:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
    .title { font-size:1.05em; color:#005fa3; margin:0 0 6px 0; }
    /* Ad image sizing (consistent with homepage) */
    .ad-img { width:100%; height:600px; object-fit:cover; border-radius:6px; display:block; }
    .ad-slot { overflow:hidden; }
    </style>
</head>

<body>
    <!-- === HEADER === -->
    <header>
        <nav class="auth-nav" style="justify-content:space-between;align-items:center;">
            <div>
                <a href="index.php" class="auth-link">🏠 Trang chủ</a>
                <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
            </div>

            <!-- search removed as requested -->
        </nav>
            <div style="text-align:center;padding:18px 0 6px 0;">
                <h1 style="margin:6px 0 4px;font-size:34px;letter-spacing:0.6px;color:#fff;text-shadow:0 2px 8px rgba(0,0,0,0.25);">
                    <?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?>
                </h1>
                <p style="color:#e8f0fb;margin:0;font-weight:500;">Danh sách bài viết theo chuyên mục</p>
            </div>
    </header>

    <!-- === MAIN CONTENT === -->
    <main>
        <!-- Cột trái: Danh sách chuyên mục -->
        <aside class="category-list">
            <h2>Chuyên mục</h2>
            <ul class="category-menu">
                <?php

                use Website\TinTuc\Models\ChuyenMucModel;

                $chuyenMucModel = new ChuyenMucModel();
                $dsChuyenMuc = $chuyenMucModel->getAll();
                foreach ($dsChuyenMuc as $cm):
                ?>
                    <li>
                        <a href="index.php?action=chuyenmuc&id=<?= $cm['id'] ?>" <?= ($cm['id'] == $chuyenMuc['id']) ? 'style="font-weight:bold;color:#005fa3;"' : '' ?>>
                            <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <!-- Left ad slots (2) -->
            <div style="margin-top:14px;">
                <div class="ad-slot" data-ad-slot="0" style="margin-bottom:10px;">
                    <a class="ad-link" href="#" target="_blank"><img class="ad-img" src="" alt=""></a>
                </div>
                <div class="ad-slot" data-ad-slot="1">
                    <a class="ad-link" href="#" target="_blank"><img class="ad-img" src="" alt=""></a>
                </div>
            </div>
        </aside>

        <!-- Cột giữa: Danh sách bài viết -->
        <section class="section">
            <!-- Bộ lọc bài viết -->
            <div class="filter-bar" style="margin-bottom:15px;text-align:right;">
                <form method="get" action="index.php">
                    <input type="hidden" name="action" value="chuyenmuc">
                    <input type="hidden" name="id" value="<?= $chuyenMuc['id'] ?>">

                    <label for="filter">Sắp xếp theo:</label>
                    <select name="filter" id="filter" onchange="this.form.submit()" style="padding:6px 10px;border-radius:4px;">
                        <option value="moi_nhat" <?= ($filter == 'moi_nhat') ? 'selected' : '' ?>>🕓 Mới nhất</option>
                        <option value="xem_nhieu" <?= ($filter == 'xem_nhieu') ? 'selected' : '' ?>>👁 Xem nhiều nhất</option>
                        <option value="binh_luan" <?= ($filter == 'binh_luan') ? 'selected' : '' ?>>💬 Bình luận nhiều nhất</option>
                    </select>
                </form>
            </div>

            <h2>📰 <?= htmlspecialchars($tenChuyenMuc ?? '') ?></h2>

            <?php if (empty($baiViet)): ?>
                <p>❌ Chưa có bài viết nào trong chuyên mục này.</p>
            <?php else: ?>
                <?php foreach ($baiViet as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars($tin['anh_dai_dien'] ?? 'uploads/no_image.png') ?>" alt="<?= htmlspecialchars($tin['tieu_de']) ?>">
                            <div>
                                <h3 class="title"><?= htmlspecialchars($tin['tieu_de']) ?></h3>
                                <small>📅 <?= htmlspecialchars($tin['ngay_dang']) ?> | 👁 <?= htmlspecialchars($tin['luot_xem']) ?></small>
                                <p><?= htmlspecialchars(mb_substr(strip_tags($tin['noi_dung']), 0, 140)) ?>...</p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination" style="text-align:center;margin-top:15px;">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="index.php?action=chuyenmuc&id=<?= $chuyenMuc['id'] ?>&page=<?= $i ?>"
                            class="page-link <?= $i == $page ? 'active' : '' ?>"
                            style="display:inline-block;padding:6px 12px;margin:2px;border-radius:6px;
                      border:1px solid #0077cc;text-decoration:none;
                      <?= $i == $page ? 'background:#0077cc;color:white;' : 'color:#0077cc;' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </section>

        <!-- Cột phải: Quảng cáo (2 slot) -->
        <aside class="category-list">
            <div style="margin-top:14px;">
                <div class="ad-slot" data-ad-slot="2" style="margin-bottom:10px;">
                    <a class="ad-link" href="#" target="_blank"><img class="ad-img" src="" alt=""></a>
                </div>
                <div class="ad-slot" data-ad-slot="3">
                    <a class="ad-link" href="#" target="_blank"><img class="ad-img" src="" alt=""></a>
                </div>
            </div>
        </aside>
    </main>

    <!-- === FOOTER === -->
    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>
    

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const ads = <?= json_encode($ads) ?> || [];
            // Normalize image path: prefer absolute or uploads/ prefix
            function normalizeImgPath(p) {
                if (!p) return 'uploads/default_ads.jpg';
                if (p.startsWith('http') || p.startsWith('/')) return p;
                if (p.startsWith('uploads/') || p.startsWith('../uploads/')) return p;
                return 'uploads/' + p;
            }

            function populateSlots(startIndex = 0) {
                const slots = document.querySelectorAll('.ad-slot');
                for (let i = 0; i < slots.length; i++) {
                    const slot = slots[i];
                    const ad = ads[(startIndex + i) % Math.max(ads.length,1)];
                    const link = slot.querySelector('.ad-link');
                    const img = slot.querySelector('.ad-img');
                    if (ad) {
                        link.href = ad.lien_ket && ad.lien_ket.trim() !== '' ? ad.lien_ket : '#';
                        img.src = normalizeImgPath(ad.hinh_anh);
                        img.alt = ad.ten_quang_cao || 'Quảng cáo';
                    } else {
                        link.href = '#';
                        img.src = 'uploads/default_ads.jpg';
                        img.alt = 'Quảng cáo';
                    }
                }
            }

            // rotate by pairs every 5s
            let adIdx = 0;
            if (ads.length > 0) populateSlots(adIdx);
            setInterval(() => {
                adIdx = (adIdx + 2) % Math.max(ads.length,2);
                populateSlots(adIdx);
            }, 5000);
        });
    </script>
</body>

</html>