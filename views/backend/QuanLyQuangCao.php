<?php
// Fragment: danh sách quảng cáo
// Biến được cung cấp từ controller: $quangCaos (mảng)

// Hàm dịch vị trí quảng cáo sang tiếng Việt
function translateViTri($viTri) {
    $translations = [
        'Trang_chu' => ' Trang chủ',
        'Sidebar' => ' Chuyên mục',
        'Giua_noi_dung' => ' Giữa nội dung'
    ];
    return $translations[$viTri] ?? htmlspecialchars($viTri);
}

// Nếu yêu cầu create hoặc edit thì include form fragment
$sub = $_GET['sub'] ?? '';
if ($sub === 'create' || $sub === 'edit') {
    // form fragment expects $quangcao variable when editing (controller should set it)
    include __DIR__ . '/QuanLyQuangCao_form.php';
    return;
}
?>
<style>
    /* Reuse backend banner card styles for ads management for a consistent admin UI */
    .card {
        padding: 28px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        max-width: none;
        width: 100%;
        box-sizing: border-box;
        margin: 2px auto 18px;
    }
    .card h2 { margin-top:0; margin-bottom:12px; font-size:1.6rem; color:#1f2937; padding-bottom:10px; }
    /* Match the member-title style used in QuanLyBanner_form.php */
    .card h2.member-title { font-size:24px; color:#007bff; font-weight:700; margin-bottom:12px; padding-bottom:0; }
    .card .btn-add { background: linear-gradient(90deg,#22c55e,#16a34a); color:#fff; border-radius:8px; padding:8px 14px; text-decoration:none; display:inline-block; font-weight:600; box-shadow:0 6px 18px rgba(34,197,94,0.18); margin-bottom:12px; margin-top:-6px; }
    /* Simplified table: no floating cards per-row, clean separators like Thanh_Vien/QuanLyBanner */
    .table { width:100%; border-collapse: collapse; border-spacing: 0; background: transparent; }
    .table thead th { text-align:center; padding:14px 16px; color:#111827; font-weight:700; font-size:1rem; border-bottom:1px solid #e6e9ef; }
    /* Narrow columns */
    .col-id { width:40px; text-align:center; }
    .col-actions { width:120px; }
    /* Reduce widths for position and status to save horizontal space */
    .col-position { width:120px; text-align:center; }
    .col-status { width:90px; text-align:center; }
    .table tbody tr { background: transparent; box-shadow: none; transition: none; }
    .table tbody td { padding:14px 16px; vertical-align:middle; font-size:1rem; color:#374151; border-top: none; border-bottom: none; }
    .banner-thumb { width:120px; height:72px; object-fit:cover; border-radius:6px; border:1px solid #eee; display:block; }
    .link-shorten { display:inline-block; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#0d6efd; }
    .badge { display:inline-block; padding:6px 12px; border-radius:6px; font-size:0.85rem; font-weight:600; text-decoration:none; cursor:pointer; transition:all .2s; border:none; }
    .badge--active { background:#d1fae5; color:#065f46; }
    .badge--active:hover { background:#a7f3d0; }
    .badge--locked { background:#fee2e2; color:#b91c1c; }
    .badge--locked:hover { background:#fecaca; }
    /* Button variants that mirror Thanh_Vien / QuanLyBanner styling */
    /* Match button height/appearance used elsewhere (e.g. Thanh_Vien) */
    .btn-edit, .btn-delete {
        /* Slightly reduced height/padding and increased corner radius */
        padding:5px 12px;
        height:30px;
        line-height:1;
        display:inline-flex;
        align-items:center;
        justify-content:center;
        gap:6px;
        font-size:14px;
        font-weight:600;
        border-radius:8px; /* round a bit more */
        min-width:0;
        white-space:nowrap;
        box-shadow:none;
        text-decoration:none;
        color:#fff;
    }
    .btn-edit { background:#3b82f6; }
    .btn-delete { background:#ef4444; }
    .btn-lock { background:#fbbf24; color:#92400e; padding:6px 10px; border-radius:6px; text-decoration:none; display:inline-block; margin-right:6px; box-shadow:none; }
    .btn-unlock { background:#16a34a; color:#fff; padding:6px 10px; border-radius:6px; text-decoration:none; display:inline-block; margin-right:6px; box-shadow:none; }
    .btn { display:inline-block; padding:6px 12px; border-radius:4px; font-size:0.85rem; text-decoration:none; font-weight:500; transition:background .2s; border:none; cursor:pointer; }
    .btn-sm { padding:4px 8px; font-size:0.8rem; }
    .btn-warning { background:#fbbf24; color:#92400e; }
    .btn-warning:hover { background:#f59e0b; }
    .btn-primary { background:#3b82f6; color:#fff; }
    .btn-primary:hover { background:#2563eb; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-danger:hover { background:#dc2626; }
</style>

<div class="card">
    <h2 class="member-title">Danh sách Quảng cáo</h2>
    <p><a href="admin.php?action=qc_create" class="btn-add">Thêm quảng cáo</a></p>

    <?php if (empty($quangCaos)): ?>
        <div style="text-align:center;padding:40px;background:#fff;border-radius:12px;color:#6b7280;">Chưa có dữ liệu quảng cáo.</div>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th class="col-id">ID</th>
                    <th class="col-image">Hình</th>
                    <th class="col-title">Tiêu đề / Link</th>
                    <th class="col-position">Vị trí</th>
                    <th class="col-status">Trạng thái</th>
                    <th class="col-actions">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($quangCaos as $qc): ?>
                <?php $img = $qc['hinh_anh'] ?? ''; ?>
                <tr>
                    <td class="col-id"><b>#<?= htmlspecialchars($qc['id']) ?></b></td>
                    <td class="col-image">
                        <?php if (!empty($img)): ?>
                            <img src="uploads/<?= htmlspecialchars($img) ?>" class="banner-thumb" alt="QC">
                        <?php else: ?>
                            <span style="font-size:0.8rem;color:#999;font-style:italic">Không có hình ảnh</span>
                        <?php endif; ?>
                    </td>
                    <td class="col-title">
                        <div style="font-weight:600;margin-bottom:4px"><?= htmlspecialchars($qc['tieu_de'] ?? 'Không có tiêu đề') ?></div>
                        <span class="link-shorten" title="<?= htmlspecialchars($qc['lien_ket'] ?? '') ?>"><?= htmlspecialchars($qc['lien_ket'] ?? '#') ?></span>
                    </td>
                    <td class="col-position" style="font-size:0.9rem;color:#666"><?= translateViTri($qc['vi_tri'] ?? '-') ?></td>
                    <td class="col-status">
                        <span class="badge <?= ($qc['trang_thai'] === 'on') ? 'badge--active' : 'badge--locked' ?>">
                            <?= ($qc['trang_thai'] === 'on') ? 'Đang bật' : 'Đang tắt' ?>
                        </span>
                    </td>
                    <td class="col-actions" style="text-align:right">
                        <a href="admin.php?action=qc_edit&id=<?= $qc['id'] ?>" class="btn-edit">Sửa</a>
                        <a href="admin.php?action=qc_delete&id=<?= $qc['id'] ?>" class="btn-delete" onclick="return confirm('Xóa quảng cáo này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
