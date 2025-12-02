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
    .backend-banner-card {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    .backend-banner-card h2 { margin-top:0; margin-bottom:20px; font-size:1.6rem; color:#1f2937; border-bottom:2px solid #e5e7eb; padding-bottom:10px; }
    .backend-banner-card .btn-add { background: linear-gradient(90deg,#0d6efd,#0b5ed7); color:#fff; border-radius:8px; padding:10px 16px; text-decoration:none; display:inline-block; font-weight:600; box-shadow:0 4px 12px rgba(13,110,253,0.3); margin-bottom:15px; }
    .backend-banner-card table { width:100%; border-collapse: separate; border-spacing: 0 8px; }
    .backend-banner-card thead th { text-align:left; padding:12px 15px; color:#6b7280; font-weight:700; font-size:0.85rem; text-transform:uppercase; border-bottom:1px solid #d1d5db; }
    .backend-banner-card tbody tr { background:#ffffff; box-shadow:0 2px 6px rgba(0,0,0,0.02); transition: transform .2s; }
    .backend-banner-card tbody tr:hover { transform: scale(1.005); box-shadow:0 4px 12px rgba(0,0,0,0.08); }
    .backend-banner-card tbody td { padding:10px 15px; vertical-align:middle; font-size:0.95rem; color:#374151; border-top:1px solid #f3f4f6; border-bottom:1px solid #f3f4f6; }
    .backend-banner-card tbody td:first-child { border-left:1px solid #f3f4f6; border-top-left-radius:8px; border-bottom-left-radius:8px; }
    .backend-banner-card tbody td:last-child { border-right:1px solid #f3f4f6; border-top-right-radius:8px; border-bottom-right-radius:8px; }
    .banner-thumb { width:70px; height:45px; object-fit:cover; border-radius:6px; border:1px solid #eee; display:block; }
    .link-shorten { display:inline-block; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; color:#0d6efd; }
    .action-group { display:inline-flex; background:#f3f4f6; border-radius:6px; overflow:hidden; border:1px solid #e5e7eb; }
    .action-btn { padding:6px 12px; font-size:0.85rem; text-decoration:none; color:#4b5563; font-weight:500; transition:background .2s; }
    .action-btn:hover { background:#e5e7eb; color:#111; }
    .action-btn.delete { color:#ef4444; border-left:1px solid #e5e7eb; }
    .action-btn.delete:hover { background:#fee2e2; color:#b91c1c; }
    .status-badge { display:inline-block; padding:6px 12px; border-radius:6px; font-size:0.85rem; font-weight:600; text-decoration:none; cursor:pointer; transition:all .2s; border:none; }
    .status-active { background:#d1fae5; color:#065f46; }
    .status-active:hover { background:#a7f3d0; }
    .status-inactive { background:#fee2e2; color:#b91c1c; }
    .status-inactive:hover { background:#fecaca; }
    .action-btn.delete:hover { background:#fee2e2; color:#b91c1c; }
</style>

<div class="backend-banner-card">
    <h2>Quản lý Quảng cáo</h2>
    <p><a href="admin.php?action=qc_create" class="btn-add">+ Thêm Quảng cáo mới</a></p>

    <?php if (empty($quangCaos)): ?>
        <div style="text-align:center;padding:40px;background:#fff;border-radius:12px;color:#6b7280;">Chưa có dữ liệu quảng cáo.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width:50px">ID</th>
                    <th style="width:140px">Hình</th>
                    <th>Tiêu đề / Link</th>
                    <th>Vị trí</th>
                    <th style="width:100px">Trạng thái</th>
                    <th style="text-align:right">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($quangCaos as $qc): ?>
                <?php $img = $qc['hinh_anh'] ?? ''; ?>
                <tr>
                    <td><b>#<?= htmlspecialchars($qc['id']) ?></b></td>
                    <td>
                        <?php if (!empty($img)): ?>
                            <img src="uploads/<?= htmlspecialchars($img) ?>" class="banner-thumb" alt="QC">
                        <?php else: ?>
                            <span style="font-size:0.8rem;color:#999;font-style:italic">No img</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600;margin-bottom:4px"><?= htmlspecialchars($qc['tieu_de'] ?? 'Không có tiêu đề') ?></div>
                        <span class="link-shorten" title="<?= htmlspecialchars($qc['lien_ket'] ?? '') ?>"><?= htmlspecialchars($qc['lien_ket'] ?? '#') ?></span>
                    </td>
                    <td style="font-size:0.9rem;color:#666"><?= translateViTri($qc['vi_tri'] ?? '-') ?></td>
                    <td>
                        <a href="admin.php?action=qc_toggle_status&id=<?= $qc['id'] ?>" class="status-badge status-<?= ($qc['trang_thai'] === 'on') ? 'active' : 'inactive' ?>">
                            <?= ($qc['trang_thai'] === 'on') ? '🟢 Bật' : '🔴 Tắt' ?>
                        </a>
                    </td>
                    <td style="text-align:right">
                        <div class="action-group">
                            <a href="admin.php?action=qc_edit&id=<?= $qc['id'] ?>" class="action-btn">Sửa</a>
                            <a href="admin.php?action=qc_delete&id=<?= $qc['id'] ?>" class="action-btn delete" onclick="return confirm('Xóa quảng cáo này?')">Xóa</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
