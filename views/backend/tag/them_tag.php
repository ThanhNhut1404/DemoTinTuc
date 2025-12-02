<?php
use Website\TinTuc\Models\TagModel;

$tagModel = new TagModel();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten_tag = trim($_POST['ten_tag'] ?? '');
    
    if (empty($ten_tag)) {
        $errorMessage = "❌ Vui lòng nhập tên thẻ tag!";
    } else {
        if ($tagModel->create($ten_tag)) {
            $successMessage = "✅ Thêm thẻ tag thành công!";
            $_POST = []; // Clear form
        } else {
            $errorMessage = "❌ Lỗi khi thêm thẻ tag!";
        }
    }
}
?>

<form method="POST" style="max-width: 500px;">
    <div style="margin-bottom: 15px;">
        <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Tên thẻ tag:</label>
        <input type="text" name="ten_tag" value="<?= htmlspecialchars($_POST['ten_tag'] ?? '') ?>" required 
            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px;">
    </div>
    
    <?php if (isset($errorMessage)): ?>
        <div style="background: #ffe3e3; color: #d20000; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: 500;">
            <?= $errorMessage ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($successMessage)): ?>
        <div style="background: #d3f9d8; color: #2b8a3e; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: 500;">
            <?= $successMessage ?>
        </div>
    <?php endif; ?>
    
    <button type="submit" style="background: #0d6efd; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;">
        ➕ Thêm thẻ tag
    </button>
</form>
