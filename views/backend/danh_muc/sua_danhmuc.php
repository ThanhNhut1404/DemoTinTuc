<?php
use Website\TinTuc\Models\ChuyenMucModel;

$chuyenMucModel = new ChuyenMucModel();
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<p style=\"color: #d32f2f;\">❌ ID danh mục không hợp lệ</p>";
    exit;
}

$danhMuc = $chuyenMucModel->getById($id);
if (!$danhMuc) {
    echo "<p style=\"color: #d32f2f;\">❌ Danh mục không tìm thấy</p>";
    exit;
}

// Xử lý cập nhật danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $ten = trim($_POST['ten_chuyen_muc'] ?? '');
    $mo_ta = trim($_POST['mo_ta'] ?? '');
    $id_cha = $_POST['id_cha'] ?? null;
    
    if ($ten) {
        try {
            $stmt = $chuyenMucModel->db->prepare("
                UPDATE chuyen_muc 
                SET ten_chuyen_muc = ?, mo_ta = ?, id_cha = ? 
                WHERE id = ?
            ");
            $stmt->execute([$ten, $mo_ta, $id_cha ?: null, $id]);
            $_SESSION['flash'] = "✅ Cập nhật danh mục thành công!";
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

<h3>Sửa Danh mục</h3>

<?php if (isset($error_msg)): ?>
    <div style="padding: 10px; background: #f8d7da; color: #721c24; border-radius: 4px; margin-bottom: 15px;">
        <?= $error_msg ?>
    </div>
<?php endif; ?>

<form method="POST" style="max-width: 600px;">
    <input type="hidden" name="action" value="update">
    
    <div class="form-group">
        <label>Tên danh mục:</label>
        <input type="text" name="ten_chuyen_muc" value="<?= htmlspecialchars($danhMuc['ten_chuyen_muc']) ?>" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
    </div>
    
    <div class="form-group">
        <label>Mô tả:</label>
        <textarea name="mo_ta" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; height: 100px; box-sizing: border-box;"><?= htmlspecialchars($danhMuc['mo_ta'] ?? '') ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Danh mục cha:</label>
        <select name="id_cha" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box;">
            <option value="">-- Không có danh mục cha --</option>
            <?php foreach ($danhMucList as $dm): 
                if ($dm['id'] == $id) continue; // Không cho chọn chính nó làm cha
            ?>
                <option value="<?= $dm['id'] ?>" <?= $danhMuc['id_cha'] == $dm['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($dm['ten_chuyen_muc']) ?>
                </option>
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
