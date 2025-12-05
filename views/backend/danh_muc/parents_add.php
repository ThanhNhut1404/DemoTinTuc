<?php
use Website\TinTuc\Models\ChuyenMucChaModel;

$parentModel = new ChuyenMucChaModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['parent_action']) && $_POST['parent_action'] === 'add') {
    $ten = trim($_POST['ten_chuyen_muc'] ?? '');
    $mo_ta = trim($_POST['mo_ta'] ?? '');
    if ($ten === '') {
        $error = 'Tên danh mục cha không được trống.';
    } else {
        $parentModel->add($ten, $mo_ta);
        header('Location: admin.php?action=danh_muc&sub=parents');
        exit;
    }
}
?>

<h3>Thêm danh mục cha</h3>

<?php if (!empty($error)): ?>
    <div style="padding:10px; background:#fee; color:#900; border-radius:6px; margin-bottom:8px;"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" style="max-width:700px;">
    <input type="hidden" name="parent_action" value="add">
    <div class="form-group">
        <label>Tên danh mục cha</label>
        <input type="text" name="ten_chuyen_muc" required style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px;">
    </div>
    <div class="form-group">
        <label>Mô tả</label>
        <textarea name="mo_ta" style="width:100%; padding:8px; border:1px solid #ddd; border-radius:6px; height:100px;"></textarea>
    </div>
    <div style="display:flex; gap:8px;">
        <button class="btn btn-success" type="submit">Lưu</button>
        <a href="admin.php?action=danh_muc&sub=parents" class="btn btn-secondary">Hủy</a>
    </div>
</form>

<style>
.form-group { margin-bottom:12px; }
/* Button styles local to this fragment */
.btn { padding: 8px 14px; border-radius: 8px; font-weight: 700; color: #fff; border: none; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
.btn-success { background: #16a34a; }
.btn-success:hover { background: #15803d; }
.btn-secondary { background: #ef4444; }
.btn-secondary:hover { background: #dc2626; }
</style>
