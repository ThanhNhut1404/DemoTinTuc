<?php
include __DIR__ . '/../config.php';

// --- KHÔNG lỗi session trùng ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Kiểm tra ID bài viết ---
if (!isset($_GET['id'])) {
    echo "Không có ID bài viết.";
    exit;
}

$id = (int)$_GET['id'];

// --- Giới hạn lượt xem mỗi 30 giây ---
if (!isset($_SESSION['views'])) {
    $_SESSION['views'] = [];
}

$now = time();
$last_view = $_SESSION['views'][$id] ?? 0;

if ($now - $last_view >= 30) {
    $conn->query("UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id = $id");
    $_SESSION['views'][$id] = $now;
}

// --- Lấy bài viết ---
$sql = "SELECT b.*, n.ho_ten AS tac_gia
        FROM bai_viet b
        LEFT JOIN nguoi_dung n ON b.id_tac_gia = n.id
        WHERE b.id = $id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Bài viết không tồn tại.";
    exit;
}

$bv = $result->fetch_assoc();

// --- Lấy bình luận ---
$bl_sql = "SELECT b.noi_dung, u.ho_ten AS ten_nguoi_dung, b.ngay_binh_luan 
           FROM binh_luan b
           JOIN nguoi_dung u ON b.id_nguoi_dung = u.id
           WHERE b.id_bai_viet = $id
           ORDER BY b.ngay_binh_luan DESC";

$binh_luan = $conn->query($bl_sql);

// --- Lấy 3 bài viết khác ---
$related_sql = "SELECT id, tieu_de, anh_dai_dien
                FROM bai_viet
                WHERE id != $id
                ORDER BY ngay_dang DESC
                LIMIT 3";
$related = $conn->query($related_sql);

// --- Kiểm tra đã thích / đã lưu ---
$yeu_thich = false;
$da_luu = false;

if (isset($_SESSION['id_nguoi_dung'])) {
    $uid = (int)$_SESSION['id_nguoi_dung'];

    $like = $conn->query("SELECT id FROM yeu_thich 
                          WHERE id_bai_viet = $id AND id_nguoi_dung = $uid");
    if ($like->num_rows > 0) $yeu_thich = true;

    $saved = $conn->query("SELECT id FROM luu_bai
                           WHERE id_bai_viet = $id AND id_nguoi_dung = $uid");
    if ($saved->num_rows > 0) $da_luu = true;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($bv['tieu_de']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; margin-top: 40px; }
        .card { border-radius: 16px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        img { border-radius: 12px; }
        .meta { color: gray; font-size: 0.9rem; margin-bottom: 15px; }
        .related img { width: 100%; height: 140px; object-fit: cover; }
        .btn-group-custom form { display: inline-block; }
    </style>
</head>

<body>

<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container">

    <!-- BÀI VIẾT -->
    <div class="card p-4 mb-4">

        <h2 class="mb-3"><?= htmlspecialchars($bv['tieu_de']); ?></h2>

        <div class="meta">
            🗓 <?= $bv['ngay_dang']; ?> • 
            👤 <?= $bv['tac_gia'] ?? 'Ẩn danh'; ?> • 
            👁 <?= $bv['luot_xem']; ?> lượt xem
        </div>

        <?php if (!empty($bv['anh_dai_dien'])): ?>
            <img src="../<?= htmlspecialchars($bv['anh_dai_dien']); ?>" class="img-fluid mb-3">
        <?php endif; ?>

        <p><em><?= htmlspecialchars($bv['mo_ta_ngan']); ?></em></p>

        <div><?= $bv['noi_dung']; ?></div>

        <div class="d-flex justify-content-between align-items-center mt-4">

            <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                <div class="btn-group-custom">

                    <!-- Nút Thích -->
                    <form method="post" action="../controllers/chi_tiet_bai_viet.php">
                        <input type="hidden" name="id_bai_viet" value="<?= $id; ?>">
                        <input type="hidden" name="action" value="yeu_thich">
                        <button class="btn <?= $yeu_thich ? 'btn-danger' : 'btn-outline-danger'; ?> btn-sm">
                            ❤️ <?= $yeu_thich ? 'Đã thích' : 'Thích'; ?>
                        </button>
                    </form>

                    <!-- Nút Lưu -->
                    <form method="post" action="../controllers/chi_tiet_bai_viet.php">
                        <input type="hidden" name="id_bai_viet" value="<?= $id; ?>">
                        <input type="hidden" name="action" value="luu_bai">
                        <button class="btn <?= $da_luu ? 'btn-primary' : 'btn-outline-primary'; ?> btn-sm">
                            💾 <?= $da_luu ? 'Đã lưu' : 'Lưu bài'; ?>
                        </button>
                    </form>

                </div>

            <?php else: ?>
                <a href="../login.php" class="btn btn-outline-danger btn-sm">❤️ Thích</a>
                <a href="../login.php" class="btn btn-outline-primary btn-sm">💾 Lưu bài</a>
                <small class="text-muted">(Vui lòng đăng nhập)</small>
            <?php endif; ?>

            <!-- QUAY LẠI TRANG CHỦ -->
            <a href="/Demotintuc/public/" class="text-decoration-none">← Quay lại trang chủ</a>

        </div>
    </div>




    <!-- BÌNH LUẬN -->
    <div class="card p-4 mb-4">

        <h5>Bình luận</h5>

        <?php if ($binh_luan->num_rows > 0): ?>
            <?php while ($c = $binh_luan->fetch_assoc()): ?>
                <div class="border-bottom pb-2 mb-2">
                    <strong><?= htmlspecialchars($c['ten_nguoi_dung']); ?></strong>
                    <span class="text-muted small">(<?= $c['ngay_binh_luan']; ?>)</span><br>
                    <?= nl2br(htmlspecialchars($c['noi_dung'])); ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Chưa có bình luận nào.</p>
        <?php endif; ?>

        <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
            <form action="../controllers/chi_tiet_bai_viet.php" method="post">
                <input type="hidden" name="id_bai_viet" value="<?= $id; ?>">
                <input type="hidden" name="action" value="binh_luan">
                <textarea name="noi_dung" class="form-control mb-2" rows="3" required></textarea>
                <button class="btn btn-primary btn-sm">Gửi bình luận</button>
            </form>
        <?php else: ?>
            <p>
    <a href="login.php" class="btn btn-primary">Đăng nhập</a>
</p>
        <?php endif; ?>

    </div>




    <!-- BÀI LIÊN QUAN -->
    <div class="card p-4 related">

        <h5>Bài viết khác</h5>

        <div class="row">

            <?php while ($r = $related->fetch_assoc()): ?>
                <div class="col-md-4">
                    <a href="/Demotintuc/views/frontend/chi_tiet_bai_viet.php?id=<?= $r['id']; ?>" class="text-decoration-none text-dark">

                        <div class="card mb-3">
                            <?php if (!empty($r['anh_dai_dien'])): ?>
                                <img src="../<?= $r['anh_dai_dien']; ?>" class="card-img-top">
                            <?php endif; ?>
                            <div class="card-body">
                                <p class="card-text"><?= htmlspecialchars($r['tieu_de']); ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>

        </div>

    </div>

</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>

</body>
</html>
