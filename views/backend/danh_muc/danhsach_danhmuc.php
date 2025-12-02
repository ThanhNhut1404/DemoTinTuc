<?php
use Website\TinTuc\Models\ChuyenMucModel;

$chuyenMucModel = new ChuyenMucModel();
$danhMucList = $chuyenMucModel->getAll();

// Xử lý xóa danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $id = $_POST['id'] ?? null;
    if ($id) {
        try {
            $stmt = $chuyenMucModel->db->prepare("DELETE FROM chuyen_muc WHERE id = ?");
            $stmt->execute([$id]);
            $_SESSION['flash'] = "✅ Xóa danh mục thành công!";
        } catch (\Exception $e) {
            $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
        }
        header('Location: admin.php?action=danh_muc&sub=danhsach');
        exit;
    }
}

// Xử lý sắp xếp thứ tự
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $items = $_POST['items'] ?? [];
    try {
        foreach ($items as $index => $id) {
            $stmt = $chuyenMucModel->db->prepare("UPDATE chuyen_muc SET thu_tu = ? WHERE id = ?");
            $stmt->execute([$index + 1, $id]);
        }
        $_SESSION['flash'] = "✅ Cập nhật thứ tự thành công!";
    } catch (\Exception $e) {
        $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
    }
    header('Location: admin.php?action=danh_muc&sub=danhsach');
    exit;
}

// Reload danh sách
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

<h3>Danh sách danh mục</h3>
<?php if (count($danhMucList) > 0): ?>
    <form method="POST">
        <input type="hidden" name="action" value="update_order">
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background: #f0f0f0; border-bottom: 2px solid #ddd;">
                    <th style="padding: 12px; text-align: left; width: 5%;">#</th>
                    <th style="padding: 12px; text-align: left; width: 25%;">Tên danh mục</th>
                    <th style="padding: 12px; text-align: left; width: 35%;">Mô tả</th>
                    <th style="padding: 12px; text-align: left; width: 15%;">Danh mục cha</th>
                    <th style="padding: 12px; text-align: center; width: 20%;">Thao tác</th>
                </tr>
            </thead>
            <tbody id="sortable" style="display: contents;">
                <?php foreach ($danhMucList as $dm): ?>
                    <tr style="border-bottom: 1px solid #eee; cursor: move;" draggable="true">
                        <td style="padding: 12px;">
                            <input type="hidden" name="items[]" value="<?= $dm['id'] ?>">
                            <span>☰</span>
                        </td>
                        <td style="padding: 12px;"><?= htmlspecialchars($dm['ten_chuyen_muc']) ?></td>
                        <td style="padding: 12px;"><?= htmlspecialchars($dm['mo_ta'] ?? '') ?></td>
                        <td style="padding: 12px;">
                            <?php 
                            if ($dm['id_cha']) {
                                $parent = $chuyenMucModel->getById($dm['id_cha']);
                                echo htmlspecialchars($parent['ten_chuyen_muc'] ?? 'N/A');
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="admin.php?action=danh_muc&sub=sua&id=<?= $dm['id'] ?>" class="btn-icon" title="Sửa">✏️</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $dm['id'] ?>">
                                <button type="submit" class="btn-icon" title="Xóa">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">💾 Lưu thứ tự</button>
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
