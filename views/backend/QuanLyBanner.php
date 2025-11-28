<?php
// Fragment: danh sách banner
// Controller sẽ cung cấp $banners

$sub = $_GET['sub'] ?? '';
if ($sub === 'create' || $sub === 'edit') {
    include __DIR__ . '/QuanLyBanner_form.php';
    return;
}
?>
<div class="card">
    <h2 style="margin-top:0">Quản lý Banner</h2>
    <?php if (isset($_GET['updated'])): ?>
        <div style="padding:8px;background:#e6ffee;border:1px solid #ccffdd;margin-bottom:10px;border-radius:6px">Thao tác cập nhật thành công.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div style="padding:8px;background:#fff1f0;border:1px solid #ffd6d6;margin-bottom:10px;border-radius:6px">Có lỗi xảy ra trong quá trình cập nhật.</div>
    <?php endif; ?>
    <p><a href="admin.php?action=banner_create" class="btn">Thêm Banner mới</a></p>

    <?php if (empty($banners)): ?>
        <div class="empty">Chưa có banner nào.</div>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">ID</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Hình ảnh</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Mô tả</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Liên kết</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Ngày tạo</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Trạng thái</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($banners as $b): ?>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($b['id']) ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8">
                        <?php if (!empty($b['hinh_banner'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($b['hinh_banner']) ?>" style="max-width:140px;height:auto;display:block" alt="">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($b['mo_ta'] ?? '') ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($b['lien_ket'] ?? '#') ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($b['ngay_tao'] ?? '') ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8">
                        <?= isset($b['trang_thai']) && $b['trang_thai'] === 'on' ? '<strong style="color:green">Bật</strong>' : '<span style="color:#666">Tắt</span>' ?>
                        &nbsp;|&nbsp;
                        <a href="admin.php?action=banner_toggle&id=<?= $b['id'] ?>" style="font-size:13px"><?= (isset($b['trang_thai']) && $b['trang_thai'] === 'on') ? 'Tắt' : 'Bật' ?></a>
                    </td>
                    <!--<td style="padding:8px;border-bottom:1px solid #f4f6f8"><a href="<?= htmlspecialchars($b['lien_ket'] ?? '#') ?>" target="_blank"><?= htmlspecialchars($b['lien_ket'] ?? '') ?></a></td>-->
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8">
                        <a href="admin.php?action=banner_edit&id=<?= $b['id'] ?>">Sửa</a> |
                        <a href="admin.php?action=banner_delete&id=<?= $b['id'] ?>" onclick="return confirm('Xóa banner này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>




























