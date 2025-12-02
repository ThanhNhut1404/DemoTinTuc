<?php
use Website\TinTuc\Models\ChuyenMucModel;

$chuyenMucModel = new ChuyenMucModel();

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $ten = trim($_POST['ten_chuyen_muc'] ?? '');
    $mo_ta = trim($_POST['mo_ta'] ?? '');
    $id_cha = $_POST['id_cha'] ?? null;
    
    if ($ten) {
        try {
            $stmt = $chuyenMucModel->db->prepare("
                INSERT INTO chuyen_muc (ten_chuyen_muc, mo_ta, id_cha, thu_tu) 
                VALUES (?, ?, ?, (SELECT COALESCE(MAX(thu_tu), 0) + 1 FROM chuyen_muc))
            ");
            $stmt->execute([$ten, $mo_ta, $id_cha ?: null]);
            $_SESSION['flash'] = "✅ Thêm danh mục thành công!";
        } catch (\Exception $e) {
            $_SESSION['flash'] = "❌ Lỗi: " . $e->getMessage();
        }
        header('Location: admin.php?action=danh_muc&sub=danhsach');
        exit;
    } else {
        $error_msg = "❌ Tên danh mục không được trống!";
    }
}

$danhMucList = $chuyenMucModel->getAll();
?>

<h3>Thêm Danh mục mới</h3>

<?php if (isset($error_msg)): ?>
    <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 15px;">
        <?= $error_msg ?>
    </div>
<?php endif; ?>

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

<style>
.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: #333;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
}

.btn-success {
    background: #10b981;
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}
</style>
