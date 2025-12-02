<?php
use Website\TinTuc\Models\TagModel;

$tagModel = new TagModel();

// Handle delete action
if ($_GET['sub'] === 'xoa' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($tagModel->delete($id)) {
        $successMessage = "✅ Xóa thẻ tag thành công!";
    } else {
        $errorMessage = "❌ Lỗi khi xóa thẻ tag!";
    }
    // Redirect to list
    header("Location: admin.php?action=tag&sub=danhsach");
    exit;
}

$tags = $tagModel->getAll();
?>

<?php if (isset($successMessage)): ?>
    <div style="background: #d3f9d8; color: #2b8a3e; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: 500;">
        <?= $successMessage ?>
    </div>
<?php endif; ?>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">ID</th>
            <th style="padding: 12px; text-align: left; font-weight: 600; color: #333;">Tên thẻ tag</th>
            <th style="padding: 12px; text-align: center; font-weight: 600; color: #333;">Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($tags): ?>
            <?php foreach ($tags as $tag): ?>
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px; color: #555;"><?= htmlspecialchars($tag['id']) ?></td>
                    <td style="padding: 12px; color: #555;"><?= htmlspecialchars($tag['ten_tag'] ?? '') ?></td>
                    <td style="padding: 12px; text-align: center;">
                        <a href="admin.php?action=tag&sub=sua&id=<?= $tag['id'] ?>" style="color: #0d6efd; text-decoration: none; margin-right: 10px;">✏️ Sửa</a>
                        <a href="admin.php?action=tag&sub=xoa&id=<?= $tag['id'] ?>" style="color: #dc3545; text-decoration: none;" onclick="return confirm('Bạn chắc chắn muốn xóa?');">🗑️ Xóa</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="padding: 20px; text-align: center; color: #999;">Chưa có thẻ tag nào</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
