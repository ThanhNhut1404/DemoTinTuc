<?php
$page = $page ?? 1;
$totalPages = $totalPages ?? 1;
use Website\TinTuc\Models\QuangcaoModel;
$qcModel = new QuangcaoModel();
// Load only Sidebar ads (active status only) for category page
$dsQuangCao = $qcModel->getQuangCaoTheoViTri('Sidebar');

// Prepare unified ads list for rotating slots (ensure 4 items)
$allAds = array_values(array_filter($dsQuangCao));
$ads = [];
if (!empty($allAds)) {
    $take = array_slice($allAds, 0, 4);
    while (count($take) < 4 && !empty($allAds)) {
        $take = array_merge($take, $allAds);
        $take = array_slice($take, 0, 4);
    }
    $ads = $take;
}

use Website\TinTuc\Models\BgWallpaperModel;
$bgModel = new BgWallpaperModel();
$activeWallpaper = $bgModel->getActive();
$wallpaperUrl = '';
if (!empty($activeWallpaper) && !empty($activeWallpaper['duong_dan_file'])) {
    $wallpaperUrl = 'uploads/wallpapers/' . htmlspecialchars($activeWallpaper['duong_dan_file']);
}
$cmModel = new \Website\TinTuc\Models\ChuyenMucModel();
$dsChuyenMuc = $cmModel->getAll();
$cmChaModel = new \Website\TinTuc\Models\ChuyenMucChaModel();
$chuyenMucCha = $cmChaModel->getAll();
$childrenMap = [];
foreach ($dsChuyenMuc as $c) {
    if (!empty($c['id_cha'])) $childrenMap[$c['id_cha']][] = $c;
}
// Helper to normalize image URLs (matches homepage behavior)
function img_url($src)
{
    $src = trim((string)$src);
    if ($src === '') return 'uploads/no_image.png';
    if (preg_match('#^(https?:)?//#i', $src)) return $src;
    if (strpos($src, '/') === 0) return $src;
    if (stripos($src, 'uploads/') !== false) {
        $pos = stripos($src, 'uploads/');
        return substr($src, $pos);
    }
    return 'uploads/' . ltrim($src, '/');
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tenChuyenMuc ?? 'Chuyên mục') ?> - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <style>
    body {
        background-image: <?= !empty($wallpaperUrl) ? "url('" . $wallpaperUrl . "')" : "''" ?>;
        background-size: cover;
        background-attachment: fixed;
        background-position: center;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.20);
        pointer-events: none;
        z-index: -1;
    }

    /* Local tweaks for category page layout */
    main { display:flex; gap:12px; align-items:stretch; max-width:1200px; margin:12px auto; padding:0 10px; }
    /* single left removed: section takes majority and right aside occupies ~28% */
    main > section { width:72%; }
    main > aside { width:26%; display:flex; flex-direction:column; box-sizing:border-box; }
    .tin-link { display:block; text-decoration:none; color:inherit; }
    .tin { padding:12px; transition: transform .18s ease, box-shadow .18s ease; }
    .tin:hover { transform: translateY(-4px); box-shadow: 0 6px 18px rgba(0,0,0,0.06); }
    .title { font-size:1.05em; color:#005fa3; margin:0 0 6px 0; }
    /* Ad image sizing (consistent with homepage) */
    /* Show sidebar images flush to container edge and make ad slots fill sidebar height */
    .ad-img { width:100%; height:100%; object-fit:cover; border-radius:0; display:block; }
    .ad-slot { overflow:hidden; background:transparent; padding:0; border-radius:0; box-shadow: none; margin:0; flex:1 1 0; }
    /* Make the wrapper inside aside fill available height and stack ad-slots vertically */
    main > aside > div { display:flex; flex-direction:column; gap:10px; height:100%; margin-top:0; padding:0; }
    
        /* Category bar (reuse homepage styles) */
        .category-bar {
            background: white;
            border-bottom: 1px solid #e6e6e6;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            margin-bottom: 12px;
        }

        .category-bar .cat-list {
            list-style: none;
            display: flex;
            gap: 18px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 10px 12px;
            align-items: center;
        }

        .category-bar .cat-item { position: relative; }

        .category-bar .cat-link {
            color: #333;
            text-decoration: none;
            padding: 8px 6px;
            font-weight: 600;
            display: inline-block;
        }

        .category-bar .cat-link:hover { color: #005fa3; }

        .category-bar .cat-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #eee;
            border-radius: 6px;
            box-shadow: 0 6px 18px rgba(10,20,30,0.08);
            min-width: 220px;
            z-index: 1500;
            padding: 8px 0;
        }

        .category-bar .cat-dropdown ul { list-style:none; margin:0; padding:0; }
        .category-bar .cat-dropdown li a { display:block; padding:8px 14px; color:#333; text-decoration:none; }
        .category-bar .cat-dropdown li a:hover { background:#f4f8ff; color:#005fa3; }

        .category-bar .cat-item:hover .cat-dropdown { display: block; }

        @media (max-width: 900px) {
            .category-bar .cat-list { overflow:auto; padding:8px; gap:12px; }
        }
    </style>
</head>

<body>
    <!-- === HEADER === -->
    <header>
        <nav class="auth-nav" style="justify-content:space-between;align-items:center;">
            <div>
                <a href="index.php" class="auth-link">🏠 Trang chủ</a>
                
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

    <!-- Category navigation bar (parents horizontal, children dropdown on hover) -->
    <nav class="category-bar">
        <ul class="cat-list">
            <?php foreach ($chuyenMucCha as $parent): ?>
                <li class="cat-item">
                    <a href="index.php?action=chuyenmuccha&id=<?= $parent['id'] ?>" class="cat-link"><?= htmlspecialchars($parent['ten_chuyen_muc']) ?></a>
                    <div class="cat-dropdown">
                        <ul>
                            <?php if (!empty($childrenMap[$parent['id']])): ?>
                                <?php foreach ($childrenMap[$parent['id']] as $child): ?>
                                    <li><a href="index.php?action=chuyenmuc&id=<?= $child['id'] ?>"><?= htmlspecialchars($child['ten_chuyen_muc']) ?></a></li>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <li class="no-child">(Chưa có chuyên mục con)</li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <!-- === MAIN CONTENT === -->
    <main>

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
                            <img src="<?= htmlspecialchars(img_url($tin['anh_dai_dien'] ?? '')) ?>" alt="<?= htmlspecialchars($tin['tieu_de']) ?>">
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
                        img.src = 'uploads/default_ads.jpg';img.alt = 'Quảng cáo';
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