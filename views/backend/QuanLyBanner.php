<?php
// Fragment: danh sách banner
// Controller sẽ cung cấp $banners

$sub = $_GET['sub'] ?? '';
if ($sub === 'create' || $sub === 'edit') {
    include __DIR__ . '/QuanLyBanner_form.php';
    return;
}
?>
<style>
    /* 🎨 Backend banner management styling */
    .backend-banner-card {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .backend-banner-card h2 {
        margin-top: 0;
        font-size: 1.6rem;
        margin-bottom: 20px;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 10px;
    }

    .backend-banner-card .btn-add {
        background: linear-gradient(90deg, #0d6efd, #0b5ed7);
        color: #fff;
        border-radius: 8px;
        padding: 10px 16px;
        text-decoration: none;
        display: inline-block;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        margin-bottom: 15px;
    }

    /* Table Styling */
    .backend-banner-card table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px; /* Khoảng cách giữa các hàng */
    }

    .backend-banner-card thead th {
        text-align: left;
        padding: 12px 15px;
        color: #6b7280;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-bottom: 1px solid #d1d5db;
    }

    .backend-banner-card tbody tr {
        background: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        transition: transform 0.2s;
    }
    
    .backend-banner-card tbody tr:hover {
        transform: scale(1.005);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .backend-banner-card tbody td {
        padding: 10px 15px;
        vertical-align: middle;
        font-size: 0.95rem;
        color: #374151;
        border-top: 1px solid #f3f4f6;
        border-bottom: 1px solid #f3f4f6;
    }
    
    /* Bo tròn đầu và cuối hàng */
    .backend-banner-card tbody td:first-child { border-left: 1px solid #f3f4f6; border-top-left-radius: 8px; border-bottom-left-radius: 8px; }
    .backend-banner-card tbody td:last-child { border-right: 1px solid #f3f4f6; border-top-right-radius: 8px; border-bottom-right-radius: 8px; }

    /* 🖼️ Thumbnail Image */
    .banner-thumb {
        width: 70px;
        height: 45px;
        object-fit: cover;
        border-radius: 6px;
        border: 1px solid #eee;
        display: block;
    }

    /* 🔗 Link Shorten */
    .link-shorten {
        display: inline-block;
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #0d6efd;
    }

    /* 💡 Toggle Switch (Trạng thái) */
    .toggle-box {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 20px;
        font-size: 0.8rem;
        text-decoration: none;
        font-weight: 600;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }
    .toggle-box .dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
    .toggle-box.on { background: #ecfdf5; border-color: #a7f3d0; color: #047857; }
    .toggle-box.on .dot { background: #10b981; }
    .toggle-box.off { background: #f9fafb; border-color: #d1d5db; color: #6b7280; }
    .toggle-box.off .dot { background: #9ca3af; }

    /* 🛠️ Action Group (Nút Sửa/Xóa gộp lại) */
    .action-group {
        display: inline-flex;
        background: #f3f4f6;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    .action-btn {
        padding: 6px 12px;
        font-size: 0.85rem;
        text-decoration: none;
        color: #4b5563;
        font-weight: 500;
        transition: background 0.2s;
    }
    .action-btn:hover { background: #e5e7eb; color: #111; }
    .action-btn.delete { color: #ef4444; border-left: 1px solid #e5e7eb; }
    .action-btn.delete:hover { background: #fee2e2; color: #b91c1c; }
    
</style>

<div class="backend-banner-card">
    <h2>Quản lý Banner</h2>
    
    <?php if (isset($_GET['updated'])): ?>
        <div style="padding:10px;background:#e6ffee;border:1px solid #90ee90;margin-bottom:15px;border-radius:8px; color:#0a7a2a;">Cập nhật thành công.</div>
    <?php endif; ?>
    
    <p><a href="admin.php?action=banner_create" class="btn-add">+ Thêm Banner</a></p>

    <?php if (empty($banners)): ?>
        <div style="text-align:center; padding:40px; background:#fff; border-radius:12px; color:#6b7280;">Chưa có dữ liệu banner.</div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th style="width: 90px;">Hình</th> <th>Mô tả / Link</th>
                    <th>Ngày tạo</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right">Hành động</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($banners as $b): ?>
                <?php $img = $b['hinh_banner'] ?? $b['hinh_anh'] ?? $b['hinh'] ?? ''; ?>
                <tr>
                    <td><b>#<?= htmlspecialchars($b['id']) ?></b></td>

                    <td>
                        <?php if (!empty($img)): ?>
                            <img src="uploads/<?= htmlspecialchars($img) ?>" class="banner-thumb" alt="Banner">
                        <?php else: ?>
                            <span style="font-size:0.8rem;color:#999;font-style:italic">No img</span>
                        <?php endif; ?>
                    </td>

                    <td>
                        <div style="font-weight:600;margin-bottom:4px"><?= htmlspecialchars($b['mo_ta'] ?? 'Không có mô tả') ?></div>
                        <span class="link-shorten" title="<?= htmlspecialchars($b['lien_ket'] ?? '') ?>">
                            <?= htmlspecialchars($b['lien_ket'] ?? '#') ?>
                        </span>
                    </td>

                    <td style="font-size:0.85rem;color:#666"><?= htmlspecialchars($b['ngay_tao'] ?? '-') ?></td>

                    <td>
                        <a href="admin.php?action=banner_toggle&id=<?= $b['id'] ?>" class="toggle-box <?= (isset($b['trang_thai']) && $b['trang_thai'] === 'on') ? 'on' : 'off' ?>">
                            <span class="dot"></span>
                            <span><?= (isset($b['trang_thai']) && $b['trang_thai'] === 'on') ? 'Bật' : 'Tắt' ?></span>
                        </a>
                    </td>

                    <td style="text-align:right">
                        <div class="action-group">
                            <a href="admin.php?action=banner_edit&id=<?= $b['id'] ?>" class="action-btn" title="Sửa">Sửa</a>
                            <a href="admin.php?action=banner_delete&id=<?= $b['id'] ?>" class="action-btn delete" onclick="return confirm('Xóa banner này?')" title="Xóa">Xóa</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>