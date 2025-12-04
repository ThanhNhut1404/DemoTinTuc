<?php
// Tránh warning nếu controller chưa truyền biến
$banners = isset($banners) && is_array($banners) ? $banners : [];
$quangCaoTrai = isset($quangCaoTrai) && is_array($quangCaoTrai) ? $quangCaoTrai : [];
$quangCaoPhai = isset($quangCaoPhai) && is_array($quangCaoPhai) ? $quangCaoPhai : [];
$chuyenMuc = isset($chuyenMuc) && is_array($chuyenMuc) ? $chuyenMuc : [];
$tinNoiBat = isset($tinNoiBat) && is_array($tinNoiBat) ? $tinNoiBat : [];
$tinMoiNhat = isset($tinMoiNhat) && is_array($tinMoiNhat) ? $tinMoiNhat : [];
$tinXemNhieu = isset($tinXemNhieu) && is_array($tinXemNhieu) ? $tinXemNhieu : [];
$baiVietTheoChuyenMuc = isset($baiVietTheoChuyenMuc) && is_array($baiVietTheoChuyenMuc) ? $baiVietTheoChuyenMuc : [];
$activeWallpaper = isset($activeWallpaper) && is_array($activeWallpaper) ? $activeWallpaper : [];

// Prepare unified ads list (take up to 4 ads from available left/right ad arrays)
// Prepare unified ads list (take up to 6 ads: 3 left + 3 right slots)
$allAds = array_values(array_filter(array_merge($quangCaoTrai, $quangCaoPhai)));
$ads = [];
if (!empty($allAds)) {
    // take first 6, or repeat if less than 6
    $take = array_slice($allAds, 0, 6);
    while (count($take) < 6) {
        $take = array_merge($take, $allAds);
        $take = array_slice($take, 0, 6);
    }
    $ads = $take;
}
// Helper: chuẩn hóa đường dẫn ảnh
function img_url($src)
{
    $src = trim((string)$src);
    if ($src === '') return 'uploads/no_image.png';

    // full URL or protocol-less
    if (preg_match('#^(https?:)?//#i', $src)) return $src;

    // absolute path on domain (keep as-is)
    if (strpos($src, '/') === 0) return $src;

    // If value already contains 'uploads/' anywhere, normalize to 'uploads/...'
    if (stripos($src, 'uploads/') !== false) {
        $pos = stripos($src, 'uploads/');
        return substr($src, $pos);
    }

    // otherwise assume filename -> prefix ../uploads/
    // Use same uploads path as backend views
    return 'uploads/' . ltrim($src, '/');
}

// normalize ads hinh_anh for JS usage (so JS can use the URL directly)
foreach ($ads as &$adNorm) {
    if (isset($adNorm['hinh_anh'])) {
        $adNorm['hinh_anh'] = img_url($adNorm['hinh_anh']);
    }
}
unset($adNorm);

// Prepare wallpaper URL from database
$wallpaperUrl = '';
if (!empty($activeWallpaper) && !empty($activeWallpaper['duong_dan_file'])) {
    $wallpaperUrl = 'uploads/wallpapers/' . htmlspecialchars($activeWallpaper['duong_dan_file']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Website Tin Tức</title>
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --primary: #005fa3;
            --primary-hover: #d9534f;
            --bg-light: #fafafa;
            --border: #eee;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
            background-image: <?= !empty($wallpaperUrl) ? "url('" . htmlspecialchars($wallpaperUrl) . "')" : "''" ?>;
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            transition: background-image 0.5s ease;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.20);
            pointer-events: none;
            z-index: -1;
        }

        /* ===== HEADER & NAV ===== */
        .auth-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--primary);
            padding: 8px 12px;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Account avatar + dropdown (left side) */
        .account-dropdown { position: relative; order: -1; }
        .account-btn { display:inline-flex; align-items:center; gap:8px; cursor:pointer; color:#fff; text-decoration:none; background:transparent; border:none; padding:0; }
        .account-avatar { width:48px; height:48px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.18); }
        .greeting { color: #fff; font-weight:600; font-size:0.95em; margin-right:6px; }
        .dropdown-menu { position:absolute; left:0; top:calc(100% + 8px); background:#fff !important; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:200px; display:none; z-index:2000; overflow:hidden; }
        .dropdown-menu a { display:block; padding:10px 12px; color:#333; text-decoration:none; border-bottom:1px solid #f1f1f1; }
        .dropdown-menu a:hover { background:#f5f8ff; color:var(--primary); }
        .dropdown-menu .last { border-bottom:0; }

        /* Reserve left space for the fixed category button */
        .auth-nav.with-left-menu {
            padding-left: 220px;
        }

        .search-box {
            position: relative;
            margin-right: auto;
            max-width: 300px;
        }

        .search-input-wrapper {
            position: relative;
        }

        #search-input {
            width: 100%;
            padding: 8px 35px 8px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.95em;
        }

        .search-btn {
            position: absolute;
            right: 5px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            font-size: 1.1em;
            cursor: pointer;
        }

        .auth-link {
            color: white;
            text-decoration: none;
            font-weight: 600;
            background: #007bff;
            padding: 7px 14px;
            border-radius: 6px;
            font-size: 0.9em;
            transition: 0.2s;
        }

        .auth-link:hover {
            background: #004a99;
        }

        /* Dropdown Chuyên mục */
        /* Place the category dropdown button at the top-left of the header */
        .dropdown {
            position: absolute;
            left: 12px;
            top: 8px;
            z-index: 2500;
        }

        .dropdown-toggle {
            color: white;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            left: 12px; /* anchor to left corner */
            top: 48px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: var(--shadow);
            min-width: 640px; /* wider menu */
            z-index: 1000;
            padding: 12px;
        }

        .dropdown-menu a {
            display: block;
            padding: 10px 16px;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.95em;
            transition: 0.2s;
        }

        .dropdown-menu a:hover {
            background: #f1f9ff;
            color: var(--primary-hover);
        }

        .dropdown.show .dropdown-menu {
            display: grid;
            grid-template-columns: 1fr 2fr; /* parents on left, children pane on right */
            gap: 16px;
            animation: fadeIn 0.2s ease;
        }

        /* Parent -> child submenu */
        .parent-list {
            min-width: 260px;
            max-height: 420px;
            overflow: auto;
        }

        .parent-list .parent-item {
            position: relative;
        }

        .parent-list .parent-item > .parent-link {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* hide nested submenu (we'll render children into the right pane) */
        .parent-list .submenu {
            display: none !important;
        }

        .parent-list .submenu li { list-style: none; }
        .parent-list .submenu li a { padding: 8px 12px; display:block; color:var(--primary); }

        .children-pane {
            min-height: 120px;
            max-height: 520px;
            overflow: auto;
            padding: 6px 12px;
        }

        /* Mobile: fall back to inline submenu under parent (if JS unavailable) */
        @media (max-width: 720px) {
            .dropdown.show .dropdown-menu { grid-template-columns: 1fr; }
            .children-pane { display: block; }
        }

        /* Category bar (new) */
        .category-bar {
            background: white;
            border-bottom: 1px solid #e6e6e6;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
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

        .category-bar .cat-link:hover { color: var(--primary); }

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
        .category-bar .cat-dropdown li a:hover { background:#f4f8ff; color:var(--primary); }

        .category-bar .cat-item:hover .cat-dropdown { display: block; }

        @media (max-width: 900px) {
            .category-bar .cat-list { overflow:auto; padding:8px; gap:12px; }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        header {
            text-align: center;
            padding: 12px 10px;
            background: white;
            border-bottom: 4px solid var(--primary);
            position: relative; /* allow absolutely positioned dropdown to align to left */
        }

        header h1 {
            font-size: 2.4em;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        header p {
            font-style: italic;
            color: #555;
            background: linear-gradient(to right, #e3f2fd, #fff);
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #cfe2ff;
            font-size: 1.02em;
        }

        /* ===== BANNER SLIDE ===== */
        .banner-container {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            margin: 8px auto; /* reduce vertical gap to align with menu */
            border-radius: 8px;
            box-shadow: none; /* remove extra shadow so banner sits flush */
        }

        /* Dots overlaid on the banner (do not take extra vertical space) */
        .banner-dots {
            position: absolute;
            left: 50%;
            bottom: 12px;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
            pointer-events: auto;
            z-index: 40;
            margin: 0; /* remove extra space */
        }

        .dot {
            width: 12px;
            height: 12px;
            background: rgba(255,255,255,0.65);
            border: 2px solid rgba(0,0,0,0.08);
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.18s ease, background 0.18s ease, box-shadow 0.18s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        }

        .dot.active {
            background: rgba(13,110,253,0.95);
            transform: scale(1.2);
            box-shadow: 0 6px 18px rgba(13,110,253,0.18);
            border-color: rgba(13,110,253,0.15);
        }
        

        /* ===== MAIN LAYOUT ===== */
        main {
            display: flex;
            flex-wrap: wrap;
            gap: 8px; /* tighten gaps between main columns */
            max-width: 1400px;
            margin: 12px auto; /* reduce vertical spacing */
            padding: 0 10px;
        }

        .content {
            flex: 1;
            min-width: 300px;
        }

        aside {
            width: 300px;
            min-width: 250px;
        }

        .category-list {
            background: white;
            padding: 10px; /* reduce padding */
            border-radius: 8px;
            box-shadow: var(--shadow);
            height: fit-content;
        }

        .category-list h2 {
            color: var(--primary);
            margin-bottom: 8px;
            font-size: 1.15em;
            border-left: 4px solid #007bff;
            padding-left: 8px;
        }

        .category-menu {
            list-style: none;
        }

        .category-menu a {
            display: block;
            padding: 6px 0;
            color: #333;
            text-decoration: none;
            border-bottom: 1px dashed #eee;
            transition: 0.15s;
        }

        .category-menu a:hover {
            color: var(--primary-hover);
            padding-left: 5px;
        }

        /* ===== TIN NỔI BẬT - SLIDE ===== */
        .slide {
            display: flex;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: var(--shadow);
            transition: transform 0.6s ease;
            will-change: transform;
            touch-action: pan-y; /* allow vertical scroll on touch, handle horizontal via JS */
            cursor: grab;
        }

        .slide-item {
            min-width: 100%;
            position: relative;
            user-select: none;
            -webkit-user-drag: none;
        }

        /* Top5 grid (show 5 images at once) */
        .top5-grid {
            display: flex;
            gap: 12px;
        }

        .top5-item {
            flex: 0 0 calc((100% - 48px) / 5);
            background: linear-gradient(180deg, #fff, #fafafa);
            border-radius: 12px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 6px 18px rgba(10,20,30,0.06);
            transition: transform .35s cubic-bezier(.2,.8,.2,1), box-shadow .35s ease, filter .35s ease;
            will-change: transform;
        }

        .top5-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            transition: transform .45s ease, filter .35s ease;
        }

        /* top badge removed to keep single title only */

        .top5-item:hover img {
            transform: scale(1.06);
            filter: brightness(.95);
        }

        .top5-item:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 18px 40px rgba(10,20,30,0.12);
        }

        .top5-info {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 12px 14px;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.55) 60% , rgba(0,0,0,0.7) 100%);
            color: white;
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .top5-info h4 {
            margin: 0;
            font-size: 0.98em;
            line-height: 1.2;
            font-weight: 700;
            text-shadow: 0 1px 2px rgba(0,0,0,0.4);
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-clamp: 2;
        }

        /* Title is shown below image (visible) */

        /* Featured layout */
        .featured {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 16px;
            align-items: start;
        }

        .featured-main img {
            width: 100%;
            height: 420px;
            object-fit: cover;
            border-radius: 12px;
            display: block;
        }

        .featured-overlay {
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            padding: 18px;
            background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.45) 50%, rgba(0,0,0,0.7) 100%);
            border-radius: 0 0 12px 12px;
        }

        .featured-main { position: relative; }
        .featured-main h3 { margin: 0; color: #fff; font-size: 1.35rem; line-height: 1.2; }

        .featured-side .side-top img { width:100%; height: 420px; object-fit: cover; border-radius: 12px; display:block; }
        .side-top { position: relative; }
        .side-top .side-overlay { position: absolute; left:0; right:0; bottom:0; padding:12px; background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.45) 40%, rgba(0,0,0,0.7) 100%); border-radius: 0 0 12px 12px; }
        .side-top h4 { margin:0; color:#fff; font-size:1rem; }

        .featured-thumbs { margin-top: 14px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        .thumb-item img { width:100%; height:140px; object-fit: cover; border-radius: 8px; display:block; }
        .thumb-title { margin-top:8px; color:var(--muted); font-weight:600; font-size:0.95rem; }

        .top5-meta {
            margin-left: auto;
            font-size: 0.85em;
            opacity: 0.9;
        }

        /* Responsive featured */
        @media (max-width: 992px) {
            .featured { grid-template-columns: 1fr; }
            .featured-side .side-top img, .featured-main img { height: 320px; }
            .featured-thumbs { grid-template-columns: repeat(2,1fr); }
        }

        @media (max-width: 576px) {
            .featured-side .side-top img, .featured-main img { height: 220px; }
            .featured-thumbs { grid-template-columns: 1fr; }
        }

        @media (max-width: 992px) {
            .top5-item { flex: 0 0 calc((100% - 24px) / 3); }
        }

        @media (max-width: 768px) {
            .top5-item { flex: 0 0 calc((100% - 12px) / 2); }
            .top5-item img { height: 160px; }
        }

        /* subtle hover focus for keyboard accessibility */
        .top5-item:focus-within {
            outline: 2px solid rgba(0,95,163,0.15);
            transform: translateY(-6px) scale(1.01);
        }

        .slide-item img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .slide-item .info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.8));
            color: white;
            padding: 20px;
        }

        .slide-item .info a {
            color: white;
            font-weight: bold;
            font-size: 1.2em;
            text-decoration: none;
        }

        .slide-item .info a:hover {
            color: #ffeb3b;
        }

        /* ===== TIN MỚI & XEM NHIỀU ===== */
        .section {
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: var(--shadow);
        }

        .section h2 {
            color: var(--primary);
            margin-bottom: 15px;
            font-size: 1.4em;
            border-left: 5px solid #007bff;
            padding-left: 10px;
        }

        .tin-link {
            display: block;
            text-decoration: none;
            color: inherit;
            margin-bottom: 12px;
        }

        .tin {
            display: flex;
            gap: 12px;
            padding: 12px;
            background: var(--bg-light);
            border-radius: 10px;
            border: 1px solid var(--border);
            transition: 0.3s;
        }

        .tin:hover {
            background: #f1f9ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .tin img {
            width: 140px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            flex-shrink: 0;
        }

        .tin .title {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 6px;
            font-size: 1.05em;
            line-height: 1.4;
        }

        .tin .title:hover {
            color: var(--primary-hover);
        }

        .tin small {
            color: #666;
            font-size: 0.9em;
        }

        /* Scroll area for remaining items - collapsed by default so only head items show */
        .tin-scroll {
            max-height: 320px; /* always expanded for Tin xem nhiều (show rest in scroll)"); */
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-right: 8px; /* room for scrollbar when expanded */
            margin-top: 12px;
        }

        .tin-scroll .tin {
            padding: 8px;
            background: #fff;
            border: 1px solid #f0f0f0;
        }

        /* ===== QUẢNG CÁO PHẢI ===== */
        .qc-right {
            margin-bottom: 15px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: none;
        }

        .qc-right.active {
            display: block;
        }

        .qc-right img {
            width: 100%;
            height: auto;
            transition: 0.3s;
        }

        .qc-right img:hover {
            transform: scale(1.03);
        }

        /* ===== AD COLUMNS (Left & Right slots) ===== */
        .ad-columns, .ad-columns-right {
            background: white;
            padding: 8px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            gap: 8px;
            height: fit-content;
        }

        .ad-column {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .ad-slot {
            overflow: hidden;
            border-radius: 10px;
            border: none; /* removed border for cleaner ad look */
            background: transparent; /* let ad images define their own background */
        }

        .ad-slot .ad-link { display:block; }
        .ad-slot .ad-img {
            display:block;
            width:100%;
            height:678px; /* increased by 20px */
            object-fit:cover;
            transition: transform .25s ease;
        }

        .ad-slot {
            margin-bottom: 8px; /* small gap between stacked ads */
        }

        .ad-slot .ad-link:hover .ad-img { transform: scale(1.03); }

        /* ===== CHUYÊN MỤC - SCROLL NGANG ĐẸP ===== */
        .chuyen-muc-wrapper {
            max-width: 1400px;
            margin: 12px auto; /* reduce spacing between sections */
            padding: 0 10px;
        }

        .chuyen-muc-block {
            margin-bottom: 12px;
            background: white;
            padding: 12px;
            border-radius: 8px;
            box-shadow: var(--shadow);
        }

        .chuyen-muc-block h3 {
            color: var(--primary);
            font-size: 1.2em;
            font-weight: 700;
            margin-bottom: 8px;
            border-left: 5px solid #007bff;
            padding-left: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .scroll-controls {
            display: flex;
            gap: 8px;
        }

        .scroll-btn {
            width: 36px;
            height: 36px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 1.2em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.2s;
        }

        .scroll-btn:hover {
            background: #004a99;
            transform: scale(1.1);
        }

        .scroll-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .scroll-container {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding: 6px 0;
            scroll-behavior: smooth;
        }

        .scroll-container::-webkit-scrollbar {
            height: 8px;
        }

        .scroll-container::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
        }

        .scroll-container::-webkit-scrollbar-thumb:hover {
            background: #999;
        }

        .bai-viet-item {
            flex: 0 0 240px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
            transition: 0.25s;
            text-align: center;
        }

        .bai-viet-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .bai-viet-item img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .bai-viet-item h4 {
            font-size: 1.02em;
            color: var(--primary);
            margin: 8px 8px 6px;
            line-height: 1.25;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .bai-viet-item p {
            font-size: 0.9em;
            color: #555;
            margin: 0 8px 8px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .bai-viet-item a {
            display: block;
            margin: 0 8px 8px;
            padding: 6px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.88em;
            font-weight: 600;
        }

        .bai-viet-item a:hover {
            background: var(--primary);
        }

        .empty {
            color: #888;
            font-style: italic;
            text-align: center;
            padding: 20px;
        }

        /* ===== FOOTER ===== */
        footer {
            text-align: center;
            padding: 16px;
            background: #222;
            color: #aaa;
            font-size: 0.95em;
            margin-top: 20px;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 992px) {
            main {
                flex-direction: column;
            }
            aside {
                width: 100%;
            }
            .auth-nav {
                justify-content: center;
            }
            .search-box {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.8em;
            }
            .banner-slide img {
                height: 250px;
            }
            .tin img {
                width: 100px;
                height: 80px;
            }
            
        }

    </style>
</head>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const searchBox = document.getElementById("searchBox");
    const suggestionsBox = document.getElementById("suggestions");
    const searchForm = document.getElementById("searchForm"); 

    // Hàm gọi API lấy gợi ý và hiển thị
    searchBox.addEventListener("keyup", async () => {
        let keyword = searchBox.value.trim();

        if (keyword.length < 1) {
            suggestionsBox.style.display = "none";
            return;
        }

        try {
            // Gọi API để lấy dữ liệu gợi ý (encode query)
            const response = await fetch(`index.php?action=suggest&q=${encodeURIComponent(keyword)}`);
            if (!response.ok) throw new Error('Network response not ok');
            const suggestions = await response.json();

            // ensure suggestions container is attached to body so it won't be clipped by parents
            if (!document.body.contains(suggestionsBox)) document.body.appendChild(suggestionsBox);
            suggestionsBox.innerHTML = "";

            if (!Array.isArray(suggestions) || suggestions.length === 0) {
                // show a single "no results" item to avoid a blank overlay
                let li = document.createElement('li');
                li.textContent = 'Không tìm thấy kết quả';
                li.style.cssText = 'padding:10px 14px;font-size:15px;color:#6b7280;cursor:default;opacity:0.85;border-bottom:none;';
                suggestionsBox.appendChild(li);
                positionSuggestions();
                suggestionsBox.style.display = 'block';
                return;
            }

            // Sort suggestions so items that start with the query come first (case-insensitive)
            try {
                const qLower = keyword.toLowerCase();
                suggestions.sort((a, b) => {
                    const ta = (typeof a === 'string' ? a : (a.tieu_de || a.title || '')).toLowerCase();
                    const tb = (typeof b === 'string' ? b : (b.tieu_de || b.title || '')).toLowerCase();
                    const aStarts = ta.startsWith(qLower) ? 0 : 1;
                    const bStarts = tb.startsWith(qLower) ? 0 : 1;
                    if (aStarts !== bStarts) return aStarts - bStarts;
                    return ta.localeCompare(tb);
                });
            } catch (e) {
                console.warn('Suggestion sort failed', e);
            }

            // Tạo và gắn các thẻ LI vào danh sách gợi ý
            suggestions.forEach(item => {
                let li = document.createElement("li");
                // Hỗ trợ cả mảng chuỗi hoặc mảng object {id, tieu_de}
                let text = typeof item === 'string' ? item : (item.tieu_de || item.title || JSON.stringify(item));
                li.textContent = text;
                // inline styles to avoid external CSS override
                li.style.cssText = 'display:flex;align-items:center;padding:10px 14px;font-size:15px;color:#0f1724;cursor:pointer;border-bottom:1px solid rgba(15,23,36,0.06);transition:background .12s ease,transform .12s ease;';
                // hover effects via events (inline so not dependent on stylesheet)
                li.onmouseenter = () => { li.style.background = 'linear-gradient(90deg, rgba(0,95,163,0.06), rgba(0,120,215,0.03))'; li.style.transform = 'translateX(4px)'; li.style.color = '#003f70'; };
                li.onmouseleave = () => { li.style.background = 'transparent'; li.style.transform = 'none'; li.style.color = '#0f1724'; };

                // Xử lý sự kiện click: BẤM LÀ TÌM KIẾM NGAY!
                li.onclick = () => {
                    // 1. Điền từ khóa vào ô tìm kiếm
                    searchBox.value = text;

                    // 2. Ẩn danh sách gợi ý
                    suggestionsBox.style.display = "none";

                    // 3. TỰ ĐỘNG GỬI FORM (Chuyển hướng đến trang tìm kiếm)
                    searchForm.submit();
                };
                suggestionsBox.appendChild(li);
            });

            // position the dropdown under the wrapper to avoid clipping
            positionSuggestions();
            suggestionsBox.style.display = "block";

        } catch (err) {
            console.error('Suggest fetch error', err);
            suggestionsBox.innerHTML = '';
            let li = document.createElement('li');
            li.textContent = 'Lỗi khi tải gợi ý';
            li.style.cssText = 'padding:10px 14px;font-size:15px;color:#b91c1c;cursor:default;opacity:0.9;border-bottom:none;';
            suggestionsBox.appendChild(li);
            positionSuggestions();
            suggestionsBox.style.display = 'block';
        }
    });
    
    // Ẩn gợi ý khi click ra ngoài ô tìm kiếm
    document.addEventListener('click', function(event) {
        if (!searchBox.contains(event.target) && !suggestionsBox.contains(event.target)) {
            suggestionsBox.style.display = 'none';
        }
    });

    // reposition suggestions under the input; use fixed positioning to avoid clipping
    function positionSuggestions() {
        try {
            const wrapper = searchBox.closest('.search-wrapper') || searchBox.parentElement;
            const rect = wrapper.getBoundingClientRect();
            // attach to body to escape parent overflow
            if (!document.body.contains(suggestionsBox)) document.body.appendChild(suggestionsBox);
            suggestionsBox.style.position = 'fixed';
            suggestionsBox.style.left = rect.left + 'px';
            suggestionsBox.style.top = (rect.bottom + 6) + 'px';
            suggestionsBox.style.width = Math.min(rect.width, 600) + 'px';
            suggestionsBox.style.maxWidth = '90%';
        } catch (e) {
            // fallback: let CSS handle positioning
            console.warn('positionSuggestions failed', e);
        }
    }

    // update position on scroll/resize while suggestions are visible
    window.addEventListener('scroll', () => { if (suggestionsBox.style.display === 'block') positionSuggestions(); }, { passive: true });
    window.addEventListener('resize', () => { if (suggestionsBox.style.display === 'block') positionSuggestions(); });
});

</script>


<body>

    <!-- BANNER SLIDE -->
    <div class="banner-container">
        <?php foreach ($banners as $index => $b): ?>
            <div class="banner-slide <?= $index === 0 ? 'active' : '' ?>">
                <a href="<?= htmlspecialchars($b['lien_ket']) ?>" target="_blank">
                    <img src="<?= htmlspecialchars(img_url($b['hinh_banner'])) ?>" alt="<?= htmlspecialchars($b['mo_ta'] ?? '') ?>">
                </a>
            </div>
        <?php endforeach; ?>
        <?php if (!empty($banners)): ?>
            <div class="banner-dots">
                <?php foreach ($banners as $index => $b): ?>
                    <span class="dot <?= $index === 0 ? 'active' : '' ?>" onclick="showBanner(<?= $index ?>)"></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <header>
        <nav class="auth-nav">
<form id="searchForm" action="index.php" method="get" class="search-container">
    <input type="hidden" name="action" value="search">

    <div class="search-wrapper">
        <input type="text" 
                id="searchBox" 
                name="q" 
                placeholder="Bạn muốn tìm gì hôm nay?" 
                autocomplete="off" 
                class="search-input">
        <button type="submit" class="search-button">🔍</button>
           <ul id="suggestions" class="suggestions" style="position:fixed;display:none;z-index:99999;background:rgba(255,255,255,0.98);border-radius:12px;box-shadow:0 10px 30px rgba(8,20,40,0.18);backdrop-filter:blur(6px);max-height:360px;overflow:auto;padding:6px 0;margin:0;list-style:none;"> </ul>
    </div>
</form>
    

            

    <?php
    // Load parent categories (chuyen_muc_cha) if controller didn't provide them
    if (!isset($chuyenMucCha) || !is_array($chuyenMucCha)) {
        try {
            $cmChaModel = new \Website\TinTuc\Models\ChuyenMucChaModel();
            $chuyenMucCha = $cmChaModel->getAll();
        } catch (Exception $e) {
            $chuyenMucCha = [];
        }
    }

    // Load child categories (chuyen_muc) if missing
    if (!isset($chuyenMuc) || !is_array($chuyenMuc)) {
        try {
            $cmModel = new \Website\TinTuc\Models\ChuyenMucModel();
            $chuyenMuc = $cmModel->getAll();
        } catch (Exception $e) {
            $chuyenMuc = [];
        }
    }

    // Build a map of children by parent id for the horizontal category bar below
    $childrenMap = [];
    foreach ($chuyenMuc as $c) {
        if (!empty($c['id_cha'])) {
            $childrenMap[$c['id_cha']][] = $c;
        }
    }
    ?>
<?php
// Render account area: show avatar + greeting if logged in, otherwise show login/register
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $user = $_SESSION['user'];
    $displayName = $user['name'] ?? $user['ten'] ?? $user['ho_ten'] ?? $user['email'] ?? 'Người dùng';
    $avatarVal = $user['avatar'] ?? $user['anh_dai_dien'] ?? $user['avatar_url'] ?? '';
    $avatarUrl = trim((string)$avatarVal) === '' ? 'uploads/no_avatar.png' : img_url($avatarVal);
    ?>
    <div class="account-dropdown">
        <button type="button" class="account-btn" id="accountToggle" aria-expanded="false">
            <img src="<?= htmlspecialchars($avatarUrl) ?>" alt="avatar" class="account-avatar">
            <span class="greeting">Xin chào, <?= htmlspecialchars($displayName) ?></span>
            <span style="color:#fff;font-size:0.9em;">▾</span>
        </button>

        <div class="dropdown-menu" id="accountMenu" role="menu">
            <a href="index.php?action=userPage">Cập nhật thông tin cá nhân</a>
            <a href="index.php?action=dathich">Đã thích</a>
            <a href="index.php?action=daluu">Đã lưu</a>
            <a href="index.php?action=binhluancuatoi">Bình luận của tôi</a>
            <a href="index.php?action=logout" class="last">Đăng xuất</a>
        </div>
    </div>
    <script>
    (function(){
        const toggle = document.getElementById('accountToggle');
        const menu = document.getElementById('accountMenu');
        if (!toggle || !menu) return;

        function closeMenu(){
            menu.style.display = 'none';
            toggle.setAttribute('aria-expanded','false');
        }

        function openMenu(){
            menu.style.display = 'block';
            toggle.setAttribute('aria-expanded','true');
        }

        toggle.addEventListener('click', function(e){
            e.stopPropagation();
            if (menu.style.display === 'block') closeMenu(); else openMenu();
        });

        // close when clicking outside
        document.addEventListener('click', function(){ closeMenu(); });

        // stop propagation when clicking inside the menu
        menu.addEventListener('click', function(e){ e.stopPropagation(); });
    })();
    </script>
<?php } else { ?>
    <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
    <a href="index.php?action=register" class="auth-link">Đăng ký</a>
<?php } ?>


        </nav>
        <h1>Website Tin Tức</h1>
        <p>Cập nhật tin tức mới nhất, nhanh chóng & chính xác</p>
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

    <main>
        <aside class="ad-columns">
            <div class="ad-column left-ads">
                <div class="ad-slot" data-ad-slot="0">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
                <div class="ad-slot" data-ad-slot="1">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
                <div class="ad-slot" data-ad-slot="2">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
            </div>
        </aside>

        <div class="content">
            <!-- Tin nổi bật -->
            <div class="section" id="featured-section">
                <h2>Tin nổi bật</h2>
                <?php
                $featured = is_array($tinNoiBat) ? array_values($tinNoiBat) : [];
                $main = $featured[0] ?? null;
                $side = $featured[1] ?? null;
                $thumbs = array_slice($featured, 2, 3);
                ?>

                <div class="featured">
                    <?php if ($main): ?>
                        <article class="featured-main">
                            <a href="index.php?action=chi_tiet_bai_viet&id=<?= $main['id'] ?>">
                                <img src="<?= htmlspecialchars(img_url($main['anh_dai_dien'])) ?>" alt="<?= htmlspecialchars($main['tieu_de']) ?>">
                                <div class="featured-overlay">
                                    <h3><?= htmlspecialchars($main['tieu_de']) ?></h3>
                                </div>
                            </a>
                        </article>
                    <?php endif; ?>

                    <div class="featured-side">
                        <?php if ($side): ?>
                            <article class="side-top">
                                <a href="index.php?action=chi_tiet_bai_viet&id=<?= $side['id'] ?>">
                                    <img src="<?= htmlspecialchars(img_url($side['anh_dai_dien'])) ?>" alt="<?= htmlspecialchars($side['tieu_de']) ?>">
                                    <div class="side-overlay">
                                        <h4><?= htmlspecialchars($side['tieu_de']) ?></h4>
                                    </div>
                                </a>
                            </article>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="featured-thumbs">
                    <?php foreach ($thumbs as $t): ?>
                        <article class="thumb-item">
                            <a href="index.php?action=chi_tiet_bai_viet&id=<?= $t['id'] ?>">
                                <img src="<?= htmlspecialchars(img_url($t['anh_dai_dien'])) ?>" alt="<?= htmlspecialchars($t['tieu_de']) ?>">
                                <div class="thumb-title"><?= htmlspecialchars($t['tieu_de']) ?></div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Tin mới nhất -->
            <div class="section">
                <h2>Tin mới nhất</h2>
                <?php
                    // Ensure newest-first ordering by date, then reindex and slice to 5
                    $tinMoiNhat = $tinMoiNhat ?: [];
                    usort($tinMoiNhat, function($a, $b){
                        return strtotime($b['ngay_dang'] ?? 0) <=> strtotime($a['ngay_dang'] ?? 0);
                    });
                    $tinMoiNhat = array_values($tinMoiNhat);
                    $moiNhat_head = array_slice($tinMoiNhat, 0, 4);
                    $moiNhat_rest = array_slice($tinMoiNhat, 4);
                ?>
                <?php foreach ($moiNhat_head as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars(img_url($tin['anh_dai_dien'])) ?>" alt="">
                            <div>
                                <p class="title"><?= htmlspecialchars($tin['tieu_de']) ?></p>
                                <small>Ngày đăng: <?= date('d/m/Y H:i', strtotime($tin['ngay_dang'])) ?></small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Tin xem nhiều -->
            <div class="section">
                <h2>Tin xem nhiều</h2>
                <?php
                    // Ensure most-viewed ordering by luot_xem desc, reindex and slice
                    $tinXemNhieu = $tinXemNhieu ?: [];
                    usort($tinXemNhieu, function($a, $b){
                        return ($b['luot_xem'] ?? 0) <=> ($a['luot_xem'] ?? 0);
                    });
                    $tinXemNhieu = array_values($tinXemNhieu);
                    $xemNhieu_head = array_slice($tinXemNhieu, 0, 4);
                    $xemNhieu_rest = array_slice($tinXemNhieu, 4);
                ?>
                <?php foreach ($xemNhieu_head as $tin): ?>
                    <a href="index.php?action=chi_tiet_bai_viet&id=<?= $tin['id'] ?>" class="tin-link">
                        <div class="tin">
                            <img src="<?= htmlspecialchars(img_url($tin['anh_dai_dien'])) ?>" alt="">
                            <div>
                                <p class="title"><?= htmlspecialchars($tin['tieu_de']) ?></p>
                                <small><?= number_format($tin['luot_xem']) ?> lượt xem</small>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>

                <?php // Remaining 'Tin xem nhiều' intentionally not rendered; only the 4 head items are shown ?>
            </div>
        </div>

        <!-- Quảng cáo phải (2 slots) -->
        <aside class="ad-columns-right">
            <div class="ad-column right-ads">
                <div class="ad-slot" data-ad-slot="3">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
                <div class="ad-slot" data-ad-slot="4">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
                <div class="ad-slot" data-ad-slot="5">
                    <a class="ad-link" href="#" target="_blank">
                        <img class="ad-img" src="" alt="">
                    </a>
                </div>
            </div>
        </aside>
    </main>

    <!-- CHUYÊN MỤC CHA - SCROLL NGANG (hiển thị bài theo các chuyên mục con của mỗi chuyên mục cha) -->
    <div class="chuyen-muc-wrapper">
        <?php foreach ($chuyenMucCha as $cm): ?>
            <?php $baiViet = $baiVietTheoChuyenMuc[$cm['id']] ?? []; ?>
            <div class="chuyen-muc-block">
                <h3>
                    <?= htmlspecialchars($cm['ten_chuyen_muc']) ?>
                    <div class="scroll-controls">
                        <button class="scroll-btn" onclick="scrollLeft('cm-<?= $cm['id'] ?>')">&lt;</button>
                        <button class="scroll-btn" onclick="scrollRight('cm-<?= $cm['id'] ?>')">&gt;</button>
                    </div>
                </h3>
                <div class="scroll-container" id="scroll-cm-<?= $cm['id'] ?>">
                    <?php if (!empty($baiViet)): ?>
                        <?php foreach ($baiViet as $bv): ?>
                            <article class="bai-viet-item">
                                <img src="<?= htmlspecialchars(img_url($bv['anh_dai_dien'])) ?>" alt="">
                                <h4><?= htmlspecialchars($bv['tieu_de']) ?></h4>
                                <p><?= htmlspecialchars($bv['mo_ta_ngan']) ?></p>
                                <a href="index.php?action=chi_tiet_bai_viet&id=<?= $bv['id'] ?>">Xem thêm</a>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="empty">Chưa có bài viết nào.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    

    <footer>
        © <?= date('Y') ?> Website Tin Tức. All rights reserved.
    </footer>
    

    <script>
        // Banner Slide
        let currentBanner = 0;
        const banners = document.querySelectorAll('.banner-slide');
        const dots = document.querySelectorAll('.dot');

        function showBanner(n) {
            banners.forEach((s, i) => s.classList.toggle('active', i === n));
            dots.forEach((d, i) => d.classList.toggle('active', i === n));
            currentBanner = n;
        }

        setInterval(() => {
            currentBanner = (currentBanner + 1) % banners.length;
            showBanner(currentBanner);
        }, 5000);

        // Quảng cáo: populate 4 ad slots (2 trái, 2 phải) và xoay theo cặp
        const ads = <?php echo json_encode(isset($ads) ? $ads : []); ?>;
        const adSlots = Array.from(document.querySelectorAll('.ad-slot'));
        let adIdx = 0; // start index in ads

        function populateAdSlots() {
            if (!adSlots.length) return;
            if (!ads || !ads.length) {
                // fallback: hide slots or show placeholder
                adSlots.forEach(s => {
                    const img = s.querySelector('.ad-img');
                    const link = s.querySelector('.ad-link');
                    img.src = '../uploads/default_ads.jpg';
                    link.href = '#';
                });
                return;
            }

            for (let i = 0; i < adSlots.length; i++) {
                const ad = ads[(adIdx + i) % ads.length] || {};
                const link = adSlots[i].querySelector('.ad-link');
                const img = adSlots[i].querySelector('.ad-img');
                link.href = ad['lien_ket'] ? ad['lien_ket'] : '#';
                // hinh_anh đã được chuẩn hóa bởi PHP img_url() function
                img.src = ad['hinh_anh'] ? ad['hinh_anh'] : '../uploads/default_ads.jpg';
                img.alt = ad['tieu_de'] ? ad['tieu_de'] : 'Quảng cáo';
            }
        }

        // populate initially
        populateAdSlots();

        // rotate every 5s, shift by 2 (luân phiên theo cặp)
        setInterval(() => {
            adIdx = (adIdx + 2) % (ads.length || 1);
            populateAdSlots();
        }, 5000);

        // Dropdown (parent -> child menu)
        (function() {
            const dropdown = document.querySelector('.dropdown');
            if (!dropdown) return;
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const parentItems = dropdown.querySelectorAll('.parent-item');

            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                dropdown.classList.toggle('show');
            });

            // Populate children pane when clicking or hovering a parent
            const childrenPane = dropdown.querySelector('#children-pane');
            dropdown.querySelectorAll('.parent-item').forEach(li => {
                const link = li.querySelector('.parent-link');
                const submenu = li.querySelector('.submenu');

                // on hover (desktop) show children
                li.addEventListener('mouseenter', function() {
                    // populate children pane
                    if (submenu) childrenPane.innerHTML = submenu.innerHTML;
                    parentItems.forEach(pi => { if (pi !== li) pi.classList.remove('active'); });
                    li.classList.add('active');
                });

                // on click: open children pane first; second click follows link
                link.addEventListener('click', function(e) {
                    if (li.classList.contains('active')) {
                        // allow navigation on second click
                        return;
                    }
                    e.preventDefault();
                    if (submenu) childrenPane.innerHTML = submenu.innerHTML;
                    parentItems.forEach(pi => { if (pi !== li) pi.classList.remove('active'); });
                    li.classList.add('active');
                });
            });

            // Click outside closes everything
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    dropdown.classList.remove('show');
                    parentItems.forEach(pi => pi.classList.remove('active'));
                }
            });
        })();

        // Scroll ngang
        function scrollLeft(id) {
            const container = document.getElementById('scroll-' + id);
            container.scrollBy({ left: -300, behavior: 'smooth' });
        }

        function scrollRight(id) {
            const container = document.getElementById('scroll-' + id);
            container.scrollBy({ left: 300, behavior: 'smooth' });
        }

        // Removed toggle button for Tin xem nhiều; rest is shown in scroll by default.

        // Load background on page load
        document.addEventListener('DOMContentLoaded', loadBackground);
    </script>
    
</body>
</html>