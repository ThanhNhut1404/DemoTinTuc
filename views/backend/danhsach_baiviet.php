<div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
        <h2 style="margin:0">Danh sách bài viết</h2>
    </div>

    <div style="overflow:auto">
    <table class="table" style="min-width:900px">
    <tr>
        <th>ID</th>
        <th>Tiêu đề</th>
        <th>Chuyên mục</th>
        <th>Trạng thái</th>
        <th>Ngày đăng</th>
        <th>Tin nổi bật</th>
        <th>Hành động</th>
    </tr>

        <?php if (!empty($baiviets)): ?>
            <?php foreach ($baiviets as $b): ?>
            <tr>
                <td class="col-id"><?= $b['id'] ?></td>
                <td><?= htmlspecialchars($b['tieu_de']) ?></td>
                <td class="col-category"><?= htmlspecialchars($b['id_chuyen_muc']) ?></td>
                <td class="col-status"><?= htmlspecialchars($b['trang_thai']) ?></td>
                <td class="col-date"><?= $b['ngay_dang'] ?></td>
                <td class="col-highlight" style="text-align:center"><?= $b['la_noi_bat'] ? '<span class="badge">✔️</span>' : '' ?></td>
                <td class="col-actions">
                    <a href="admin.php?action=edit&id=<?= $b['id'] ?>" class="btn" style="padding:6px 8px">Sửa</a>
                    <a href="admin.php?action=delete&id=<?= $b['id'] ?>" class="btn btn-lock" style="padding:6px 8px" onclick="return confirm('Xóa bài viết này?')">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="7">Chưa có bài viết nào.</td></tr>
        <?php endif; ?>
    </table>
    </div>
</div>
