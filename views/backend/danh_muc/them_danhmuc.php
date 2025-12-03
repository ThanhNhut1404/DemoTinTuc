<?php
use Website\TinTuc\Models\ChuyenMucModel;

$chuyenMucModel = new ChuyenMucModel();
$danhMucList = $chuyenMucModel->getAll();
?>

<h3>Thêm Danh mục mới</h3>

<form method="POST" style="max-width: 600px;">
    <input type="hidden" name="action" value="add">
    
    <div class="form-group">
        <label>Tên danh mục:</label>
        <input type="text" name="ten_chuyen_muc" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>
    
    <div class="form-group">
        <label>Mô tả:</label>
        <textarea name="mo_ta" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; height: 100px; box-sizing: border-box;"></textarea>
    </div>
    
    <div class="form-group">
        <label>Danh mục cha:</label>
        <select name="id_cha" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
            <option value="">-- Không có danh mục cha --</option>
            <?php foreach ($danhMucList as $dm): ?>
                <option value="<?= $dm['id'] ?>"><?= htmlspecialchars($dm['ten_chuyen_muc']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div style="display: flex; gap: 10px;">
        <button type="submit" class="btn btn-success">💾 Lưu</button>
        <a href="admin.php?action=danh_muc&sub=danhsach" class="btn btn-secondary">❌ Hủy</a>
    </div>
</form>
