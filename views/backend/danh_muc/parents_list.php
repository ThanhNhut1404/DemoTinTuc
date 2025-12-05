<?php
use Website\TinTuc\Models\ChuyenMucChaModel;

$parentModel = new ChuyenMucChaModel();
$parents = $parentModel->getAll();
?>

<h3>Danh mục cha</h3>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
    <div>Quản lý danh mục cha (dùng cho danh mục con chọn)</div>
    <div>
        <a href="admin.php?action=danh_muc&sub=parents_add" class="btn btn-success">Thêm danh mục cha</a>
    </div>
</div>

<?php if (count($parents) > 0): ?>
    <table style="width:100%; border-collapse: collapse;">
        <thead>
            <tr style="background:#f7f7f7;">
                <th style="padding:10px; text-align:left; width:8%;">#</th>
                <th style="padding:10px; text-align:left;">Tên danh mục cha</th>
                <th style="padding:10px; text-align:left;">Mô tả</th>
                <th style="padding:10px; text-align:center; width:16%;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($parents as $p): ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:10px;">☰</td>
                    <td style="padding:10px; font-weight:600;"><?= htmlspecialchars($p['ten_chuyen_muc']) ?></td>
                    <td style="padding:10px;"><?= htmlspecialchars($p['mo_ta'] ?? '') ?></td>
                    <td style="padding:10px; text-align:center;">
                        <a href="admin.php?action=danh_muc&sub=parents_edit&id=<?= $p['id'] ?>" class="btn btn-primary">Sửa</a>
                        <form method="POST" style="display:inline-block; margin:0 6px;" onsubmit="return confirm('Xóa danh mục cha?');">
                            <input type="hidden" name="parent_action" value="delete">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p style="color:#666; padding:12px;">Chưa có danh mục cha nào.</p>
<?php endif; ?>

<style>
/* button base - ensure anchors and buttons match size */
.btn {
    padding:6px 12px;
    border: none;
    border-radius:4px;
    cursor:pointer;
    font-weight:700;
    color:#fff;
    display:inline-block;
    text-decoration:none;
    font-size:14px;
    line-height:20px;
    vertical-align:middle;
    box-sizing:border-box;
}

/* primary (blue) */
.btn-primary { background:#0ea5e9; }
.btn-primary:hover { background:#0284c7; }

/* success (green) - restore Add Parent button */
.btn-success { background:#16a34a; }
.btn-success:hover { background:#15803d; }

/* danger (red) */
.btn-danger { background:#ef4444; }
.btn-danger:hover { background:#dc2626; }

/* keep old icon helper for any remaining uses */
.btn-icon { background:none; border:none; cursor:pointer; padding:6px; }
</style>
