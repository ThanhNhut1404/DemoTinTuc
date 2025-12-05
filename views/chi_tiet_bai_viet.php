<?php
include __DIR__ . '/../config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper functions
if (!function_exists('img_url')) {
    function img_url($path)
    {
        // Nếu chỉ là tên file (không có /)
        if (strpos($path, '/') === false) {
            // Kiểm tra file có trong public/uploads/bai_viet/ không
            $bai_viet_path = __DIR__ . '/../public/uploads/bai_viet/' . $path;
            if (file_exists($bai_viet_path)) {
                return '/Demotintuc/public/uploads/bai_viet/' . $path;
            }
            // Nếu không, tìm trong public/uploads/
            return '/Demotintuc/public/uploads/' . $path;
        }
        // Nếu đã là đường dẫn đầy đủ, chỉ thêm /Demotintuc/ vào trước
        return '/Demotintuc/' . ltrim($path, '/');
    }
}
if (!function_exists('base_url')) {
    function base_url($path = '')
    {
        return '/Demotintuc/' . ltrim($path, '/');
    }
}

// === LẤY ID BÀI VIẾT ===
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='container mt-5'><div class='alert alert-danger'>ID bài viết không hợp lệ.</div></div>";
    exit;
}
$id = (int)$_GET['id'];
$return_url = "index.php?action=chi_tiet_bai_viet&id=" . $id;
$return_url_encoded = urlencode($return_url);

// === XỬ LÝ BÌNH LUẬN (trước hiển thị trang) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'binh_luan') {
    if (!isset($_SESSION['id_nguoi_dung'])) {
        $_SESSION['flash_message'] = 'Vui lòng đăng nhập để bình luận!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $noi_dung = trim($_POST['noi_dung'] ?? '');
    if (empty($noi_dung)) {
        $_SESSION['flash_message'] = 'Vui lòng nhập nội dung bình luận!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    $uid = (int)$_SESSION['id_nguoi_dung'];
    $id_bai_viet = (int)($_POST['id_bai_viet'] ?? 0);

    if ($id_bai_viet !== $id) {
        $_SESSION['flash_message'] = 'Dữ liệu không hợp lệ!';
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Load active bad words from DB and censor
    try {
        $bwRes = $conn->query("SELECT word FROM bad_words WHERE active = 1");
        $badWords = [];
        if ($bwRes) {
            while ($row = $bwRes->fetch_assoc()) {
                $badWords[] = $row['word'];
            }
        }
    } catch (Exception $e) {
        $badWords = [];
    }

    $noi_dung_censored = $noi_dung;
    if (!empty($badWords)) {
        $sub = [];
        foreach ($badWords as $w) {
            $w = trim($w);
            if ($w === '') continue;
            $chars = preg_split('//u', $w, -1, PREG_SPLIT_NO_EMPTY);
            $parts = array_map(function ($ch) {
                return preg_quote($ch, '/') . '+';
            }, $chars);
            $sub[] = implode('', $parts);
        }
        if (!empty($sub)) {
            $pattern = '/(?<!\\p{L})(?:' . implode('|', $sub) . ')(?!\\p{L})/iu';
            $noi_dung_censored = preg_replace($pattern, '***', $noi_dung_censored);
        }
    }

    // Store as pending ('An') for moderation
    $trang_thai = 'An';
    $stmt = $conn->prepare("INSERT INTO binh_luan (id_bai_viet, id_nguoi_dung, noi_dung, trang_thai, ngay_binh_luan) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $id, $uid, $noi_dung_censored, $trang_thai);
    $stmt->execute();
    $stmt->close();

    $_SESSION['flash_message'] = 'Bình luận đã gửi và đang chờ duyệt bởi quản trị.';
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}

// === XỬ LÝ AJAX LIKE / UNLIKE (theo dõi từng user) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do'], $_POST['id_bai_viet'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_nguoi_dung'])) {
        echo json_encode(['login' => true]);
        exit;
    }

    $post_id = (int)$_POST['id_bai_viet'];
    $user_id = (int)$_SESSION['id_nguoi_dung'];
    $action = $_POST['do'];

    // Chỉ cho phép xử lý đúng bài viết đang xem
    if ($post_id !== $id) {
        echo json_encode(['success' => false]);
        exit;
    }

    if ($action === 'like') {
        // Kiểm tra xem user đã like chưa
        $stmt = $conn->prepare("SELECT id FROM yeu_thich WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Chưa like, thêm vào yeu_thich
            $stmt = $conn->prepare("INSERT INTO yeu_thich (id_bai_viet, id_nguoi_dung) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            $stmt->close();

            // Tăng luot_thich
            $stmt = $conn->prepare("UPDATE bai_viet SET luot_thich = luot_thich + 1 WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        } else {
            // Đã like rồi, không cho like lại
            echo json_encode(['success' => false, 'message' => 'Bạn đã like bài viết này rồi']);
            exit;
        }
    } elseif ($action === 'unlike') {
        // Xóa like
        $stmt = $conn->prepare("DELETE FROM yeu_thich WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();

        // Giảm luot_thich
        $stmt = $conn->prepare("UPDATE bai_viet SET luot_thich = GREATEST(luot_thich - 1, 0) WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    // Trả về số lượt thích mới
    $stmt = $conn->prepare("SELECT luot_thich FROM bai_viet WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['luot_thich'] ?? 0;
    $stmt->close();

    echo json_encode([
        'success' => true,
        'count' => $count
    ]);
    exit;
}

// === XỬ LÝ AJAX LƯU BÀI VIẾT ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_save'], $_POST['id_bai_viet'])) {
    header('Content-Type: application/json');

    if (!isset($_SESSION['id_nguoi_dung'])) {
        echo json_encode(['login' => true]);
        exit;
    }

    $post_id = (int)$_POST['id_bai_viet'];
    $user_id = (int)$_SESSION['id_nguoi_dung'];
    $action = $_POST['action_save'];

    if ($post_id !== $id) {
        echo json_encode(['success' => false]);
        exit;
    }

    if ($action === 'save') {
        // Kiểm tra xem user đã lưu chưa
        $stmt = $conn->prepare("SELECT id FROM luu_bai_viet WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();

        if ($stmt->get_result()->num_rows === 0) {
            // Chưa lưu, thêm vào luu_bai_viet
            $stmt = $conn->prepare("INSERT INTO luu_bai_viet (id_bai_viet, id_nguoi_dung) VALUES (?, ?)");
            $stmt->bind_param("ii", $id, $user_id);
            $stmt->execute();
            $stmt->close();

            echo json_encode(['success' => true, 'saved' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Bạn đã lưu bài viết này rồi']);
        }
        exit;
    } elseif ($action === 'unsave') {
        // Xóa lưu
        $stmt = $conn->prepare("DELETE FROM luu_bai_viet WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
        $stmt->bind_param("ii", $id, $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['success' => true, 'saved' => false]);
        exit;
    }
}

// === TĂNG LƯỢT XEM – CHỐNG SPAM (5 phút cho user, 5 phút cho guest) ===
// We'll use a DB table for logged-in users and a cookie for guests.
if (isset($VIEW_COUNT_ENABLED) && $VIEW_COUNT_ENABLED) {
    $threshold = isset($VIEW_COUNT_THRESHOLD_SECONDS) ? (int)$VIEW_COUNT_THRESHOLD_SECONDS : 300;
    $now = time();
    $didCount = false;

    if (isset($_SESSION['id_nguoi_dung']) && !empty($_SESSION['id_nguoi_dung'])) {
        // Logged-in user: DB-backed tracking so it's resilient across sessions
        $uid = (int)$_SESSION['id_nguoi_dung'];

        // Create table if missing (safe no-op if exists)
        $conn->query("CREATE TABLE IF NOT EXISTS `bai_viet_views_users` (
            `id_bai_viet` INT NOT NULL,
            `id_nguoi_dung` INT NOT NULL,
            `last_view` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_bai_viet`, `id_nguoi_dung`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Get last view timestamp
        $stmt = $conn->prepare("SELECT UNIX_TIMESTAMP(last_view) AS last_ts FROM bai_viet_views_users WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
        $stmt->bind_param('ii', $id, $uid);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $last_ts = isset($row['last_ts']) ? (int)$row['last_ts'] : 0;
        if (($now - $last_ts) >= $threshold) {
            // Upsert last_view and increment
            $stmt = $conn->prepare("INSERT INTO bai_viet_views_users (id_bai_viet, id_nguoi_dung, last_view) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE last_view = NOW()");
            $stmt->bind_param('ii', $id, $uid);
            $stmt->execute();
            $stmt->close();

            $stmt2 = $conn->prepare("UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = ?");
            $stmt2->bind_param('i', $id);
            $stmt2->execute();
            $stmt2->close();
            $didCount = true;
        }
    } else {
        // Guest: use DB-backed fingerprint (hash of IP + User-Agent) to avoid
        // depending on cookies which may be blocked or not set.
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $fingerprint = hash('sha256', $ip . '|' . $ua);

        // Create guest tracking table if missing
        $conn->query("CREATE TABLE IF NOT EXISTS `bai_viet_views_guests` (
            `id_bai_viet` INT NOT NULL,
            `fingerprint` VARCHAR(64) NOT NULL,
            `last_view` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_bai_viet`, `fingerprint`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Check last view for this fingerprint
        $stmt = $conn->prepare("SELECT UNIX_TIMESTAMP(last_view) AS last_ts FROM bai_viet_views_guests WHERE id_bai_viet = ? AND fingerprint = ?");
        $stmt->bind_param('is', $id, $fingerprint);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res ? $res->fetch_assoc() : null;
        $stmt->close();

        $last_ts = isset($row['last_ts']) ? (int)$row['last_ts'] : 0;
        if (($now - $last_ts) >= $threshold) {
            // Upsert last_view and increment
            $stmt = $conn->prepare("INSERT INTO bai_viet_views_guests (id_bai_viet, fingerprint, last_view) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE last_view = NOW()");
            $stmt->bind_param('is', $id, $fingerprint);
            $stmt->execute();
            $stmt->close();

            $stmt2 = $conn->prepare("UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = ?");
            $stmt2->bind_param('i', $id);
            $stmt2->execute();
            $stmt2->close();
            $didCount = true;
        }
    }

    // Debug log if enabled
    if (isset($VIEW_COUNT_DEBUG) && $VIEW_COUNT_DEBUG) {
        $logDir = __DIR__ . '/../storage';
        if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
        $logFile = $logDir . '/view_debug.log';
        $uidLog = isset($uid) ? $uid : 0;
        $cookieFlag = isset($cookieName) ? (isset($_COOKIE[$cookieName]) ? '1' : '0') : '0';
        $line = date('Y-m-d H:i:s') . " | post={$id} | user={$uidLog} | cookieExists={$cookieFlag} | didCount=" . ($didCount ? '1' : '0') . " | now={$now} | threshold={$threshold}\n";
        @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
// === KẾT THÚC ===

$stmt = $conn->prepare("SELECT b.*, n.ho_ten AS tac_gia, n.anh_dai_dien AS tac_gia_avatar, COALESCE(b.luot_thich, 0) AS luot_thich, COALESCE(b.luot_xem, 0) AS luot_xem 
                        FROM bai_viet b 
                        LEFT JOIN nguoi_dung n ON b.id_tac_gia = n.id 
                        WHERE b.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    echo "<div class='container mt-5'><div class='alert alert-warning text-center p-5 fs-3'>Bài viết không tồn tại.</div></div>";
    exit;
}
$bv = $result->fetch_assoc();
$stmt->close();

// === KIỂM TRA USER ĐÃ LIKE BÀI VIẾT NÀY CHƯA ===
$user_liked = false;
$user_saved = false;
if (isset($_SESSION['id_nguoi_dung'])) {
    $user_id = (int)$_SESSION['id_nguoi_dung'];
    $stmt = $conn->prepare("SELECT id FROM yeu_thich WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $user_liked = $stmt->get_result()->num_rows > 0;
    $stmt->close();

    // Kiểm tra user đã lưu chưa
    $stmt = $conn->prepare("SELECT id FROM luu_bai_viet WHERE id_bai_viet = ? AND id_nguoi_dung = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $user_saved = $stmt->get_result()->num_rows > 0;
    $stmt->close();
}

// === LẤY BÌNH LUẬN ===
$stmt = $conn->prepare("SELECT bl.noi_dung, u.ho_ten AS ten_nguoi_dung, bl.ngay_binh_luan 
                        FROM binh_luan bl 
                        JOIN nguoi_dung u ON bl.id_nguoi_dung = u.id 
                        WHERE bl.id_bai_viet = ? AND bl.trang_thai = 'Hien'
                        ORDER BY bl.ngay_binh_luan DESC");
$stmt->bind_param("i", $id);
$stmt->execute();
$binh_luan = $stmt->get_result();
$stmt->close();

// === BÀI VIẾT LIÊN QUAN ===
$stmt = $conn->prepare("SELECT id, tieu_de, anh_dai_dien, mo_ta_ngan 
                        FROM bai_viet 
                        WHERE id != ? 
                        ORDER BY ngay_dang DESC 
                        LIMIT 6");
$stmt->bind_param("i", $id);
$stmt->execute();
$related_posts = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($bv['tieu_de']); ?> - DemoTinTuc</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../views/frontend/frontend.css">
    <style>
        <?php
        // Load active wallpaper so header/background matches homepage
        try {
            $bgModel = new \Website\TinTuc\Models\BgWallpaperModel();
            $activeWallpaper = $bgModel->getActive();
        } catch (Exception $e) {
            $activeWallpaper = null;
        }
        $wallpaperUrl = '';
        if (!empty($activeWallpaper) && !empty($activeWallpaper['duong_dan_file'])) {
            // Use absolute public path so background loads correctly from any route
            $wallpaperUrl = '/Demotintuc/public/uploads/wallpapers/' . htmlspecialchars($activeWallpaper['duong_dan_file']);
        }
        ?>

        <?php if (!empty($wallpaperUrl)): ?>
        body {
            background-image: url('<?= $wallpaperUrl ?>');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            padding-top: 76px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        <?php else: ?>
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding-top: 76px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        <?php endif; ?>

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

        .article-img {
            border-radius: 15px;
            max-height: 500px;
            object-fit: cover;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            animation: fadeInUp 0.6s ease;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            transform: translateY(-5px);
        }

        .bounce-item,
        .related-item,
        .btn-bounce {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            position: relative;
            overflow: hidden;
        }

        .bounce-item::before,
        .related-item::before,
        .btn-bounce::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(0, 198, 255, 0.1));
            opacity: 0;
            transition: opacity 0.4s;
            border-radius: inherit;
            pointer-events: none;
        }

        .bounce-item:hover,
        .related-item:hover,
        .btn-bounce:hover {
            transform: translateY(-8px) scale(1.02) !important;
            box-shadow: 0 20px 50px rgba(13, 110, 253, 0.3) !important;
            z-index: 10;
        }

        .bounce-item:hover::before,
        .related-item:hover::before,
        .btn-bounce:hover::before {
            opacity: 1;
        }

        @keyframes heartbeat {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.4);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sticky-sidebar {
            position: sticky;
            top: 100px;
        }

        .like-btn.active,
        .like-btn:hover {
            background: #e74c3c !important;
            border-color: #e74c3c !important;
            color: white !important;
        }

        .like-btn.active i {
            animation: heartbeat 0.8s ease;
        }

        .save-btn.active {
            background: #0d6efd !important;
            border-color: #0d6efd !important;
            color: white !important;
        }

        .save-btn.active i {
            animation: bounce 0.6s ease;
        }

        @keyframes bounce {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        .sticky-sidebar {
            position: static !important;
            margin-top: 2rem;
        }

        /* Header and category bar styles - align with homepage (layout B)
           Account/avatar stays on the left; search + login/register sit on the right together */
        .auth-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(90deg, #0d6efd 0%, #0dcaf0 100%);
            padding: 8px 12px;
            gap: 10px;
            flex-wrap: wrap;
            position: relative;
            z-index: 1000;
        }

        /* Account area (left) */
        .account-dropdown { position: relative; order: -1; }
        .account-btn { display:inline-flex; align-items:center; gap:8px; cursor:pointer; color:#fff; text-decoration:none; background:transparent; border:none; padding:0; }
        .account-avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; border:2px solid rgba(255,255,255,0.18); }

        /* Search + auth links group on the right */
        .search-container { order: 1; margin-left: auto; display:flex; align-items:center; gap:8px; }
        .search-wrapper { width: 320px; max-width: 46vw; }
        .auth-link, .auth-links { order: 2; margin-left: 8px; color: #fff; }
        .auth-link { margin-left: 6px; margin-right: 6px; color: #fff; background: rgba(255,255,255,0.06); padding:8px 10px; border-radius:8px; text-decoration:none; }

        /* Ensure dropdown menu appears over the header content */
        .dropdown-menu { position:absolute; left:0; top:calc(100% + 8px); background:#fff !important; border-radius:8px; box-shadow:0 4px 12px rgba(0,0,0,0.15); min-width:200px; display:none; z-index:2000; overflow:hidden; }

        header {
            text-align: center;
            padding: 18px 0 6px 0;
        }

        header {
            position: relative;
            padding-top: 8px;
        }

        .header-title {
            margin: 10px 0 2px;
            font-size: 34px;
            letter-spacing: 0.6px;
            color: #fff;
            text-shadow: 0 2px 8px rgba(0,0,0,0.25);
            text-align: center;
        }

        .header-title .site-title-link { color: inherit; text-decoration: none; }

        .header-sub { text-align: center; margin-top: 6px; color: #e8f0fb; font-weight:500; }

        .left-controls { display:flex; align-items:center; gap:8px; }
        .home-link-left { color:#fff; background: rgba(0,0,0,0.06); padding:8px 10px; border-radius:8px; text-decoration:none; }

        header p {
            color: #e8f0fb;
            margin: 0;
            font-weight: 500;
        }

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
            overflow: visible;
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
            z-index: 99999;
            padding: 8px 0;
        }

        .category-bar .cat-item:hover .cat-dropdown { display: block; }

        @media (max-width: 900px) {
            .category-bar .cat-list { overflow: auto; padding:8px; gap:12px; }
        }
    </style>
</head>

<body>

    <?php
    // Load parent and child categories for the header (same as trang_chu)
    if (!isset($chuyenMucCha) || !is_array($chuyenMucCha)) {
        try {
            $cmChaModel = new \Website\TinTuc\Models\ChuyenMucChaModel();
            $chuyenMucCha = $cmChaModel->getAll();
        } catch (Exception $e) {
            $chuyenMucCha = [];
        }
    }
    if (!isset($chuyenMuc) || !is_array($chuyenMuc)) {
        try {
            $cmModel = new \Website\TinTuc\Models\ChuyenMucModel();
            $chuyenMuc = $cmModel->getAll();
        } catch (Exception $e) {
            $chuyenMuc = [];
        }
    }
    $childrenMap = [];
    foreach ($chuyenMuc as $c) {
        if (!empty($c['id_cha'])) {
            $childrenMap[$c['id_cha']][] = $c;
        }
    }
    ?>

    <!-- BEGIN: Header copied from trang_chu.php -->
    <header>
        <nav class="auth-nav">
            <!-- left: home button + account (account may be moved by order) -->
            <div class="left-controls">
                <a class="home-link-left" href="/Demotintuc/public/">🏠 Trang chủ</a>
            </div>
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
            // account area (login/register) or avatar greeting
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
                <?php
            } else {
                ?>
                <div class="auth-links">
                    <a href="index.php?action=login" class="auth-link">Đăng nhập</a>
                    <a href="index.php?action=register" class="auth-link">Đăng ký</a>
                </div>
                <?php
            }
            ?>
        </nav>

        <h1 class="header-title"><a href="/Demotintuc/public/" class="site-title-link">Website Tin Tức</a></h1>
        <p class="header-sub">Cập nhật tin tức mới nhất, nhanh chóng & chính xác</p>
    </header>

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
    
    <!-- END: Header copied from trang_chu.php -->

    <div class="container my-5">
        <div class="row g-5">
            <!-- CỘT TRÁI: BÀI VIẾT CHÍNH -->
            <div class="col-lg-8">

                <?php if (isset($_SESSION['flash_message'])): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4">
                        <strong><?= htmlspecialchars($_SESSION['flash_message']); ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['flash_message']); ?>
                <?php endif; ?>

                <article class="card shadow-lg mb-5">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="display-5 fw-bold mb-4"><?= htmlspecialchars($bv['tieu_de']); ?></h1>

                        <div class="text-muted small mb-4 d-flex flex-wrap align-items-center gap-3 border-bottom pb-3">
                            <span><i class="fas fa-calendar-alt me-2"></i><?= date('d/m/Y', strtotime($bv['ngay_dang'])); ?></span>
                            <!-- Author avatar and name removed as requested -->
                            <span><i class="fas fa-eye me-2"></i><?= number_format($bv['luot_xem']); ?> lượt xem</span>

                            <div class="ms-auto d-flex gap-3">
                                <!-- NÚT THÍCH -->
                                <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill like-btn <?= $user_liked ? 'active' : '' ?>"
                                        data-id="<?= $id ?>" data-action="<?= $user_liked ? 'unlike' : 'like' ?>" title="Thích bài viết">
                                        <i class="<?= $user_liked ? 'fas' : 'far' ?> fa-heart"></i>
                                        <span class="like-count ms-1"><?= number_format($bv['luot_thich']); ?></span>
                                    </button>
                                <?php else: ?>
                                    <a href="index.php?action=login&return_url=<?= $return_url_encoded ?>" class="text-danger text-decoration-none" title="Thích bài viết">
                                        <i class="far fa-heart"></i> <?= number_format($bv['luot_thich']); ?>
                                    </a>
                                <?php endif; ?>

                                <!-- NÚT LƯU BÀI VIẾT -->
                                <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                                    <button class="btn btn-outline-primary btn-sm rounded-pill save-btn <?= $user_saved ? 'active' : '' ?>"
                                        data-id="<?= $id ?>" data-action="<?= $user_saved ? 'unsave' : 'save' ?>" title="Lưu bài viết">
                                        <i class="<?= $user_saved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                        <span class="ms-1">Lưu</span>
                                    </button>
                                <?php else: ?>
                                    <a href="index.php?action=login&return_url=<?= $return_url_encoded ?>" class="btn btn-outline-primary btn-sm rounded-pill" title="Lưu bài viết">
                                        <i class="far fa-bookmark"></i>
                                        <span class="ms-1">Lưu</span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (!empty($bv['mo_ta_ngan'])): ?>
                            <p class="lead fs-5 text-muted mb-4"><?= htmlspecialchars($bv['mo_ta_ngan']); ?></p>
                        <?php endif; ?>

                        <?php if (!empty($bv['anh_dai_dien'])): ?>
                            <div class="text-center mb-5">
                                <img src="<?= img_url($bv['anh_dai_dien']) ?>" alt="<?= htmlspecialchars($bv['tieu_de']) ?>"
                                    class="img-fluid rounded shadow-lg article-img">
                            </div>
                        <?php endif; ?>

                        <div class="article-content fs-5 lh-lg text-justify">
                            <?= $bv['noi_dung'] ?? '' ?>
                        </div>

                        <!-- SHARE BUTTONS -->
                        <div class="mt-4 mb-3 share-row text-center">
                            <div class="d-inline-flex gap-2 align-items-center">
                                <button class="btn btn-outline-secondary btn-sm" id="copyLinkBtn" title="Sao chép liên kết">
                                    <i class="far fa-copy"></i> Sao chép liên kết
                                </button>
                                <a href="#" class="btn btn-primary btn-sm" id="facebookShare" title="Chia sẻ lên Facebook">
                                    <i class="fab fa-facebook-f"></i> Facebook
                                </a>
                                <a href="#" class="btn btn-info btn-sm text-white" id="twitterShare" title="Chia sẻ lên Twitter">
                                    <i class="fab fa-twitter"></i> Twitter
                                </a>
                                <a href="#" class="btn btn-success btn-sm" id="whatsappShare" title="Chia sẻ qua WhatsApp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </a>
                                <button class="btn btn-outline-danger btn-sm" id="instagramShare" title="Chia sẻ lên Instagram">
                                    <i class="fab fa-instagram"></i> Instagram
                                </button>
                            </div>
                            <div id="shareFeedback" class="small text-success mt-2" style="display:none;"></div>
                        </div>

                        <div class="mt-5 pt-4 border-top text-center">
                            <a href="/Demotintuc/public/" class="btn btn-outline-secondary btn-sm back-home">
                                <i class="fas fa-arrow-left me-1"></i> Quay lại trang chủ
                            </a>
                        </div>
                    </div>
                </article>

                <!-- BÌNH LUẬN -->
                <div class="card shadow-lg">
                    <div class="card-body p-4 p-lg-5">
                        <h4 class="mb-4 text-primary"><i class="fas fa-comments me-2"></i>Bình luận (<?= $binh_luan->num_rows; ?>)</h4>

                        <?php if ($binh_luan->num_rows > 0): ?>
                            <div class="comments-list">
                                <?php while ($c = $binh_luan->fetch_assoc()): ?>
                                    <div class="border-bottom pb-4 mb-4">
                                        <strong class="text-primary"><?= htmlspecialchars($c['ten_nguoi_dung']); ?></strong>
                                        <small class="text-muted ms-2"><i class="fas fa-clock"></i> <?= date('d/m/Y H:i', strtotime($c['ngay_binh_luan'])); ?></small>
                                        <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($c['noi_dung'])); ?></p>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted text-center py-5 fst-italic">
                                <i class="fas fa-comments fa-3x opacity-25 mb-3 d-block"></i>
                                Chưa có bình luận nào. Hãy là người đầu tiên!
                            </p>
                        <?php endif; ?>

                        <hr class="my-4">

                        <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                            <form action="" method="post">
                                <input type="hidden" name="id_bai_viet" value="<?= $id ?>">
                                <input type="hidden" name="action" value="binh_luan">
                                <div class="mb-3">
                                    <label class="form-label">Viết bình luận của bạn</label>
                                    <textarea name="noi_dung" class="form-control" rows="4" placeholder="Chia sẻ suy nghĩ của bạn..." required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg px-5 btn-bounce">
                                    <i class="fas fa-paper-plane me-2"></i>Gửi bình luận
                                </button>
                            </form>
                        <?php else: ?>
                            <div class="text-center py-5 bg-light rounded">
                                <i class="fas fa-lock fa-3x text-muted mb-3"></i>
                                <a href="index.php?action=login&return_url=<?= $return_url_encoded ?>" class="btn btn-primary">Đăng nhập để bình luận</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- CỘT PHẢI: BÀI LIÊN QUAN -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <?php if ($related_posts->num_rows > 0): ?>
                        <div class="card shadow-lg">
                            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);">
                                <h5 class="mb-0 fw-bold"><i class="fas fa-lightbulb me-2"></i>Bài viết gợi ý</h5>
                            </div>
                            <div class="card-body p-0">
                                <?php while ($r = $related_posts->fetch_assoc()): ?>
                                    <a href="/Demotintuc/public/index.php?action=chi_tiet_bai_viet&id=<?= $r['id'] ?>"
                                        class="text-decoration-none text-dark d-block border-bottom related-item bounce-item p-3">
                                        <div class="row g-2 align-items-center">
                                            <?php if (!empty($r['anh_dai_dien'])): ?>
                                                <div class="col-4">
                                                    <img src="<?= img_url($r['anh_dai_dien']); ?>" class="img-fluid rounded shadow-sm" style="height:70px; object-fit:cover;">
                                                </div>
                                                <div class="col-8">
                                                    <h6 class="fw-bold mb-1 line-clamp-2 text-primary" style="font-size:0.95rem;">
                                                        <?= htmlspecialchars($r['tieu_de']); ?>
                                                    </h6>
                                                    <?php if (!empty($r['mo_ta_ngan'])): ?>
                                                        <small class="text-muted line-clamp-1 d-block" style="font-size:0.8rem;">
                                                            <?= htmlspecialchars(substr($r['mo_ta_ngan'], 0, 60)); ?>...
                                                        </small>
                                                    <?php endif; ?>
                                                </div>
                                            <?php else: ?>
                                                <div class="col-12">
                                                    <h6 class="fw-bold line-clamp-2 text-primary" style="font-size:0.95rem;">
                                                        <?= htmlspecialchars($r['tieu_de']); ?>
                                                    </h6>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </a>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // === XỬ LÝ NÚT LIKE ===
            document.querySelectorAll('.like-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const currentAction = this.dataset.action;

                    fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                'do': currentAction,
                                'id_bai_viet': id
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.login) {
                                window.location.href = 'index.php?action=login';
                                return;
                            }

                            if (!data.success) {
                                alert(data.message || 'Bạn đã like bài viết này rồi!');
                                return;
                            }

                            // Cập nhật giao diện
                            const nextAction = currentAction === 'like' ? 'unlike' : 'like';
                            this.dataset.action = nextAction;
                            this.classList.toggle('active');

                            // Cập nhật icon
                            const icon = this.querySelector('i');
                            if (currentAction === 'like') {
                                icon.className = 'fas fa-heart';
                            } else {
                                icon.className = 'far fa-heart';
                            }

                            // Cập nhật số lượng like
                            if (data.count !== undefined) {
                                this.querySelector('.like-count').textContent = data.count.toLocaleString();
                            }
                        })
                        .catch(err => console.error('Like error:', err));
                });
            });

            // === XỬ LÝ NÚT LƯU ===
            document.querySelectorAll('.save-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.id;
                    const currentAction = this.dataset.action;

                    fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: new URLSearchParams({
                                'action_save': currentAction,
                                'id_bai_viet': id
                            })
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.login) {
                                window.location.href = 'index.php?action=login';
                                return;
                            }

                            if (!data.success) {
                                alert(data.message || 'Lỗi khi lưu bài viết!');
                                return;
                            }

                            // Cập nhật giao diện
                            const nextAction = data.saved ? 'unsave' : 'save';
                            this.dataset.action = nextAction;
                            this.classList.toggle('active');

                            // Cập nhật icon
                            const icon = this.querySelector('i');
                            if (data.saved) {
                                icon.className = 'fas fa-bookmark';
                            } else {
                                icon.className = 'far fa-bookmark';
                            }
                        })
                        .catch(err => console.error('Save error:', err));
                });
            });

            // === SHARE BUTTONS ===
            const copyBtn = document.getElementById('copyLinkBtn');
            const fbBtn = document.getElementById('facebookShare');
            const twBtn = document.getElementById('twitterShare');
            const waBtn = document.getElementById('whatsappShare');
            const igBtn = document.getElementById('instagramShare');
            const feedback = document.getElementById('shareFeedback');

            const shareUrl = window.location.href;
            const shareTitle = document.title || document.querySelector('h1')?.innerText || '';

            function showFeedback(msg) {
                if (!feedback) return;
                feedback.textContent = msg;
                feedback.style.display = 'block';
                setTimeout(() => feedback.style.display = 'none', 2500);
            }

            if (copyBtn) {
                copyBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    try {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(shareUrl);
                        } else {
                            const ta = document.createElement('textarea');
                            ta.value = shareUrl;
                            document.body.appendChild(ta);
                            ta.select();
                            document.execCommand('copy');
                            ta.remove();
                        }
                        showFeedback('Đã sao chép liên kết vào bộ nhớ tạm.');
                    } catch (err) {
                        console.error('Copy failed', err);
                        showFeedback('Không thể sao chép liên kết.');
                    }
                });
            }

            if (fbBtn) {
                fbBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(shareUrl);
                    window.open(url, 'fbshare', 'width=640,height=480');
                });
            }

            if (twBtn) {
                twBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const url = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(shareUrl) + '&text=' + encodeURIComponent(shareTitle);
                    window.open(url, 'twshare', 'width=640,height=480');
                });
            }

            if (waBtn) {
                waBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const text = encodeURIComponent(shareTitle + '\n' + shareUrl);
                    const url = 'https://api.whatsapp.com/send?text=' + text;
                    window.open(url, '_blank');
                });
            }

            if (igBtn) {
                igBtn.addEventListener('click', async (e) => {
                    e.preventDefault();
                    // Instagram doesn't offer a direct web share URL; copy link and open Instagram
                    try {
                        if (navigator.clipboard && navigator.clipboard.writeText) {
                            await navigator.clipboard.writeText(shareUrl);
                        }
                        showFeedback('Đã sao chép liên kết. Mở Instagram và dán để chia sẻ.');
                        window.open('https://www.instagram.com/', '_blank');
                    } catch (err) {
                        console.error(err);
                        showFeedback('Không thể mở Instagram.');
                    }
                });
            }
        });
    </script>
</body>

</html>