<?php
// Fragment: danh sách quảng cáo
// Biến được cung cấp từ controller: $quangCaos (mảng)

// Nếu yêu cầu create hoặc edit thì include form fragment
$sub = $_GET['sub'] ?? '';
if ($sub === 'create' || $sub === 'edit') {
    // form fragment expects $quangcao variable when editing (controller should set it)
    include __DIR__ . '/QuanLyQuangCao_form.php';
    return;
}
?>
<div class="card">
    <h2 style="margin-top:0">Quản lý Quảng cáo</h2>
    <p><a href="admin.php?action=qc_create" class="btn">Thêm Quảng cáo mới</a></p>

    <?php if (empty($quangCaos)): ?>
        <div class="empty">Chưa có quảng cáo nào.</div>
    <?php else: ?>
        <table style="width:100%;border-collapse:collapse"> 
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">ID</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Tiêu đề</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Hình ảnh</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Vị trí</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Liên kết</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #eee">Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($quangCaos as $qc): ?>
                <tr>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($qc['id']) ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($qc['tieu_de']) ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8">
                        <?php if (!empty($qc['hinh_anh'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($qc['hinh_anh']) ?>" style="max-width:140px;height:auto;display:block" alt="">
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><?= htmlspecialchars($qc['vi_tri']) ?></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8"><a href="<?= htmlspecialchars($qc['lien_ket']) ?>" target="_blank"><?= htmlspecialchars($qc['lien_ket']) ?></a></td>
                    <td style="padding:8px;border-bottom:1px solid #f4f6f8">
                        <a href="admin.php?action=qc_edit&id=<?= $qc['id'] ?>">Sửa</a> |
                        <a href="admin.php?action=qc_delete&id=<?= $qc['id'] ?>" onclick="return confirm('Xóa quảng cáo này?')">Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
