<?php
use Website\TinTuc\Models\BaiVietModel;
use Website\TinTuc\Models\ThanhVienModel;
use Website\TinTuc\Models\BinhLuanModel;
use Website\TinTuc\Models\ChuyenMucModel;
use Website\TinTuc\Models\QuangCaoModel;
use Website\TinTuc\Database;

$bv = new BaiVietModel();
$tv = new ThanhVienModel();
$bl = new BinhLuanModel();

$cm = new ChuyenMucModel();
$qc = new QuangCaoModel();

$countPosts = $bv->countAll();
$countUsers = $tv->countAll();
$countComments = $bl->countAll();
$totalViews = $bv->totalViews();

// Additional dashboard stats requested
$countPendingPosts = count($bv->getPending());
$countCategories = count($cm->getAll());

// Comments: pending / hidden
$countCommentsPending = 0;
$countCommentsHidden = 0;
try {
    $pending = $bl->getByStatus('Cho_duyet');
    $countCommentsPending = is_array($pending) ? count($pending) : 0;
} catch (Exception $e) { $countCommentsPending = 0; }
try {
    $hidden = $bl->getByStatus('An');
    $countCommentsHidden = is_array($hidden) ? count($hidden) : 0;
} catch (Exception $e) { $countCommentsHidden = 0; }

// Users locked
$lockedUsers = $tv->getAll(null, 'Khoa');
$countUsersLocked = is_array($lockedUsers) ? count($lockedUsers) : 0;

// Featured posts (la_noi_bat = 1)
$countFeatured = count($bv->getTinNoiBat(10000));

// Active banners
$allBanners = $qc->all();
$countActiveBanners = 0;
if (is_array($allBanners)) {
    foreach ($allBanners as $b) {
        if (isset($b['trang_thai']) && strtolower(trim($b['trang_thai'])) === 'on') $countActiveBanners++;
    }
}

// Scheduled posts
$countScheduled = count($bv->getScheduled());
?>

<style>
    .card h3 { color: #0d6efd; }

    /* Dashboard specific upgrades */
    .dashboard .card {
        background: linear-gradient(180deg, #ffffff, #fbfdff);
        padding: 20px 22px;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(15,23,42,0.04);
    }

    .dashboard h2 { color: #0b5ed7; font-size:22px; margin-bottom:8px; }

    .dashboard .stat-tile {
        background: #fff;
        padding: 14px 16px;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(15,23,42,0.04);
        border-left: 4px solid rgba(13,110,253,0.12);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        display: flex; flex-direction: column; justify-content: center;
    }
    .dashboard .stat-tile:hover { transform: translateY(-4px); box-shadow: 0 10px 28px rgba(15,23,42,0.08); }

    .dashboard .stat-tile h3 { margin:0 0 6px 0; font-size:14px; color:#0d6efd; }
    .dashboard .stat-tile p { margin:0; font-size:20px; font-weight:700; color:#111827; }
    .dashboard .stat-tile small { color:#6b7280; }

    /* Chart containers */
    .dashboard canvas { background: transparent; }
    .dashboard .card .small { color:#6b7280; }
</style>

<div class="dashboard">

<?php
// Prepare grouped data for charts (posts by status, comments by status)
try {
    $db = new Database();
    $pdo = $db->connect();

    // Posts by status
    $postsStatusStmt = $pdo->query("SELECT LOWER(TRIM(COALESCE(trang_thai,'')) ) AS status, COUNT(*) AS cnt FROM bai_viet GROUP BY status");
    $postsByStatus = [];
    foreach ($postsStatusStmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $k = $r['status'] ?: 'unknown';
        $postsByStatus[$k] = (int)$r['cnt'];
    }

    // Comments by status
    $commentsStatusStmt = $pdo->query("SELECT LOWER(TRIM(COALESCE(trang_thai,''))) AS status, COUNT(*) AS cnt FROM binh_luan GROUP BY status");
    $commentsByStatus = [];
    foreach ($commentsStatusStmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $k = $r['status'] ?: 'unknown';
        $commentsByStatus[$k] = (int)$r['cnt'];
    }
} catch (Exception $e) {
    $postsByStatus = [];
    $commentsByStatus = [];
}
?>

<div class="card">
    <h2>Tổng quan</h2>
    <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap">
        <div class="stat-tile" style="flex:1;min-width:180px;">
            <h3>Thống kê bài viết</h3>
            <p><?= $countPosts ?></p>
            <small>Số bài viết hiện có</small>
        </div>

        <div class="stat-tile" style="flex:1;min-width:180px;">
            <h3>Thống kê người dùng</h3>
            <p><?= $countUsers ?></p>
            <small>Tổng người dùng</small>
        </div>

        <div class="stat-tile" style="flex:1;min-width:180px;">
            <h3>Thống kê bình luận</h3>
            <p><?= $countComments ?></p>
            <small>Tổng bình luận</small>
        </div>

        <div class="stat-tile" style="flex:1;min-width:180px;">
            <h3>Lượt xem</h3>
            <p><?= $totalViews ?></p>
            <small>Tổng lượt xem tất cả bài viết</small>
        </div>
            </div>

        </div> <!-- /.dashboard -->

        <div class="card" style="margin-top:12px;">
            <h2>Chi tiết thống kê</h2>
            <div style="display:flex;gap:12px;margin-top:12px;flex-wrap:wrap">
                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Tổng số bài viết</h3>
                    <p><?= $countPosts ?></p>
                    <small>Tổng bài viết trong hệ thống</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Bài viết chờ duyệt</h3>
                    <p><?= $countPendingPosts ?></p>
                    <small>Bài viết đang ở trạng thái chờ duyệt</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Tổng số chuyên mục</h3>
                    <p><?= $countCategories ?></p>
                    <small>Tổng chuyên mục (chưa phân biệt cha/con)</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Tổng số bình luận</h3>
                    <p><?= $countComments ?></p>
                    <small>Tổng bình luận</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Bình luận (Chờ / Ẩn)</h3>
                    <p>Chờ: <?= $countCommentsPending ?> &nbsp; / &nbsp; Ẩn: <?= $countCommentsHidden ?></p>
                    <small>Số bình luận chờ duyệt và bị ẩn</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Tổng tài khoản</h3>
                    <p><?= $countUsers ?></p>
                    <small>Tổng số người dùng</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Tài khoản bị khóa</h3>
                    <p><?= $countUsersLocked ?></p>
                    <small>Số tài khoản ở trạng thái khóa</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Bài viết nổi bật</h3>
                    <p><?= $countFeatured ?></p>
                    <small>Số bài viết được đánh dấu nổi bật</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Banner quảng cáo đang bật</h3>
                    <p><?= $countActiveBanners ?></p>
                    <small>Số banner có trạng thái "on"</small>
                </div>

                <div class="stat-tile" style="flex:1;min-width:180px;">
                    <h3>Bài viết đã đặt lịch</h3>
                    <p><?= $countScheduled ?></p>
                    <small>Số bài viết có ngày đăng trong tương lai</small>
                </div>
            </div>
        </div>

        <!-- Charts row -->
        <div class="card" style="margin-top:12px;">
            <h2>Biểu đồ</h2>
            <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:12px;align-items:flex-start;">
                <div style="flex:1;min-width:280px;max-width:520px;background:#fff;border:1px solid #eef2f6;padding:12px;border-radius:8px;">
                    <h3>Bài viết theo trạng thái</h3>
                    <canvas id="postsStatusChart" style="width:100%;height:220px;max-height:260px;"></canvas>
                    <small>So sánh số lượng bài viết theo trạng thái (Đã đăng / Chờ duyệt / Nháp / Từ chối)</small>
                </div>

                <div style="width:300px;min-width:260px;background:#fff;border:1px solid #eef2f6;padding:12px;border-radius:8px;">
                    <h3>Bình luận (Hiện / Ẩn / Chờ)</h3>
                    <canvas id="commentsStatusChart" style="width:100%;height:220px;max-height:260px;"></canvas>
                    <small>Tỷ lệ bình luận theo trạng thái</small>
                </div>
            </div>
        </div>

        <!-- Chart.js and render script -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
        // Provide server-side data to JS
        window.dashboardStats = {
            postsByStatus: <?= json_encode($postsByStatus ?? []) ?>,
            commentsByStatus: <?= json_encode($commentsByStatus ?? []) ?>
        };

        (function(){
            const s = window.dashboardStats;
            // Posts by status -> bar
            const postLabels = Object.keys(s.postsByStatus).map(l => l || 'unknown');
            const postData = Object.values(s.postsByStatus);

            const ctxPosts = document.getElementById('postsStatusChart');
            if (ctxPosts) {
                new Chart(ctxPosts.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: postLabels,
                        datasets: [{
                            label: 'Số bài',
                            data: postData,
                            backgroundColor: ['#0ea5e9','#f59e0b','#94a3b8','#ef4444','#6b7280'],
                            borderColor: 'rgba(0,0,0,0.05)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false }, tooltip: { mode: 'index' } },
                        scales: { y: { beginAtZero: true } }
                    }
                });
            }

            // Comments by status -> donut
            const commentLabels = Object.keys(s.commentsByStatus).map(l => l || 'unknown');
            const commentData = Object.values(s.commentsByStatus);
            const ctxComments = document.getElementById('commentsStatusChart');
            if (ctxComments) {
                new Chart(ctxComments.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: commentLabels,
                        datasets: [{
                            data: commentData,
                            backgroundColor: ['#10b981','#ef4444','#f59e0b','#9ca3af']
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
                });
            }
        })();
        </script>
</div>
