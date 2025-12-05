<?php
use Website\TinTuc\Models\ChuyenMucModel;

$chuyenMucModel = new ChuyenMucModel();

// Get all children (danh mục con)
$danhMucList = $chuyenMucModel->getAll();

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    if (strpos($flash, '✅') === 0) {
        echo "<div style=\"padding: 10px; background: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 15px;\">{$flash}</div>";
    } else {
        echo "<div style=\"padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 15px;\">{$flash}</div>";
    }
}
?>

<h3>Danh sách danh mục con</h3>
<?php if (count($danhMucList) > 0): ?>

    <form method="POST">
        <input type="hidden" name="action" value="update_order">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #f0f0f0; border-bottom: 2px solid #ddd;">
                    <th style="padding: 12px; text-align: center; width: 5%;">#</th>
                    <th style="padding: 12px; text-align: center; width: 30%;">Tên danh mục con</th>
                    <th style="padding: 12px; text-align: center; width: 35%;">Mô tả</th>
                    <th style="padding: 12px; text-align: center; width: 15%;">Danh mục cha</th>
                    <th style="padding: 12px; text-align: center; width: 15%;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="sortable" style="display: contents;">
                <?php foreach ($danhMucList as $child): ?>
                    <tr style="border-bottom: 1px solid #eee; cursor: move;" draggable="true">
                        <td style="padding: 12px;">
                            <input type="hidden" name="items[]" value="<?= $child['id'] ?>">
                            <span>☰</span>
                        </td>
                        <td style="padding: 12px;"><?= htmlspecialchars($child['ten_chuyen_muc']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($child['mo_ta'] ?? '') ?></td>
                        <td style="padding: 12px;">
                            <?php 
                                if ($child['id_cha']) {
                                    // Fetch parent name from chuyen_muc_cha table
                                    $parentStmt = $chuyenMucModel->db->prepare("SELECT ten_chuyen_muc FROM chuyen_muc_cha WHERE id = ?");
                                    $parentStmt->execute([$child['id_cha']]);
                                    $parent = $parentStmt->fetch(PDO::FETCH_ASSOC);
                                    echo htmlspecialchars($parent['ten_chuyen_muc'] ?? '');
                                } else {
                                    echo '-';
                                }
                            ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="admin.php?action=danh_muc&sub=sua&id=<?= $child['id'] ?>" class="btn btn-primary" style="margin-right:6px; font-size:13px; padding:6px 10px;">Sửa</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $child['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="font-size:13px; padding:6px 10px">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            </tbody>
        </table>
        <button type="submit" class="btn btn-success">Lưu thứ tự</button>
    </form>
<?php else: ?>
    <p style="text-align: center; padding: 20px; color: #999;">Chưa có danh mục nào</p>
<?php endif; ?>

<style>
.btn-icon {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 16px;
    padding: 4px 8px;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-icon:hover {
    transform: scale(1.2);
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn-primary {
    background: #0ea5e9;
    color: white;
}

.btn-primary:hover {
    background: #0284c7;
}

/* small adjustments so .btn-primary matches other fragments */
.btn-primary { border-radius: 6px; }

/* Success (green) button */
.btn-success {
    background: #16a34a; /* green-600 */
    color: white;
}
.btn-success:hover {
    background: #15803d; /* green-700 */
}

/* Danger (red) button for delete actions */
.btn-danger {
    background: #ef4444;
    color: #fff;
    border: none;
    border-radius: 6px;
}
.btn-danger:hover {
    background: #dc2626;
}

tr[draggable="true"]:hover {
    background: #f0f8ff;
}

tr[draggable="true"].drag-over {
    background: #e0e7ff;
}
</style>

<script>
let draggedElement = null;

document.querySelectorAll('tr[draggable="true"]').forEach(row => {
    row.addEventListener('dragstart', function() {
        draggedElement = this;
        this.style.opacity = '0.5';
    });
    
    row.addEventListener('dragend', function() {
        this.style.opacity = '1';
        draggedElement = null;
    });
    
    row.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('drag-over');
    });
    
    row.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });
    
    row.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('drag-over');
        if (draggedElement && draggedElement !== this) {
            this.parentNode.insertBefore(draggedElement, this);
        }
    });
});
</script>
