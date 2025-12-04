<?php
// Simple admin page to manage bad keywords
// Expects Composer autoload and admin layout to include this fragment.

try {
    $bwModel = new \Website\TinTuc\Models\BadWordsModel();
    $badWords = $bwModel->getAll();
} catch (Exception $e) {
    $badWords = [];
}

?>
<div class="card">
    <h2>Quản lý Từ khoá Xấu</h2>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="admin-flash admin-flash-success"><?php echo htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="admin-flash admin-flash-error"><?php echo htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>

    <?php
    $editId = (int)($_GET['edit_id'] ?? 0);
    $editing = null;
    if ($editId > 0) {
        $editing = $bwModel->find($editId);
    }
    ?>

    <?php if ($editing): ?>
        <form method="POST" action="admin.php?action=bad_words_update" style="display:flex;gap:8px;margin-bottom:12px">
            <input type="hidden" name="id" value="<?= $editing['id'] ?>" />
            <input name="word" value="<?= htmlspecialchars($editing['word']) ?>" placeholder="Nhập từ khóa xấu" style="flex:1;padding:8px;border:1px solid #e5e7eb;border-radius:6px" />
            <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="active" value="1" <?= $editing['active'] ? 'checked' : '' ?> /> Active</label>
            <button type="submit" class="btn">Cập nhật</button>
            <a href="admin.php?action=bad_words" class="btn" style="background:#ef4444;color:#fff;text-decoration:none;padding:8px 12px;border-radius:6px">Hủy</a>
        </form>
    <?php else: ?>
        <form method="POST" action="admin.php?action=bad_words_add" style="display:flex;gap:8px;margin-bottom:12px">
            <input name="word" placeholder="Nhập từ khóa xấu" style="flex:1;padding:8px;border:1px solid #e5e7eb;border-radius:6px" />
            <button type="submit" class="btn">Thêm</button>
            <a href="admin.php?action=bad_words_apply" class="btn" style="background:#10b981;color:#fff;text-decoration:none;padding:8px 12px;border-radius:6px">Áp dụng (censor)</a>
        </form>
    <?php endif; ?>

    <div style="max-height:320px;overflow:auto;border:1px solid #f1f5f9;padding:8px;border-radius:6px">
        <?php if (empty($badWords)): ?>
            <div style="color:#6b7280">Chưa có từ khoá xấu.</div>
        <?php else: ?>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px">
                <?php foreach ($badWords as $bw): ?>
                    <li style="display:flex;justify-content:space-between;align-items:center;padding:6px;border-radius:4px;background:#fff;border:1px solid #f8fafc">
                        <div style="display:flex;gap:12px;align-items:center">
                            <div style="width:36px;text-align:center;color:#6b7280">#<?php echo $bw['id']; ?></div>
                            <div style="font-weight:600"><?php echo htmlspecialchars($bw['word']); ?></div>
                            <div style="color:#6b7280;font-size:0.85rem"><?= htmlspecialchars($bw['created_at'] ?? '') ?></div>
                            <div style="margin-left:8px">
                                <?php if ($bw['active']): ?>
                                    <span style="background:#d1fae5;color:#065f46;padding:4px 8px;border-radius:4px;font-size:0.85rem">Active</span>
                                <?php else: ?>
                                    <span style="background:#fee2e2;color:#b91c1c;padding:4px 8px;border-radius:4px;font-size:0.85rem">Inactive</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px">
                            <a href="admin.php?action=bad_words&edit_id=<?php echo $bw['id']; ?>" style="text-decoration:none">Sửa</a>
                            <a href="admin.php?action=bad_words_copy&id=<?php echo $bw['id']; ?>" style="text-decoration:none">Chép</a>
                            <a href="admin.php?action=bad_words_toggle&id=<?php echo $bw['id']; ?>" style="text-decoration:none"><?= $bw['active'] ? 'Tắt' : 'Bật' ?></a>
                            <a href="admin.php?action=bad_words_delete&id=<?php echo $bw['id']; ?>" onclick="return confirm('Xóa từ khoá này?')" style="color:#ef4444;text-decoration:none">Xóa bỏ</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
