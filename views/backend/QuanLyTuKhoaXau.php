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
        <!-- Editing is hidden to simplify UI; use the add form to create new entries. -->
        <div style="margin-bottom:12px;padding:10px;background:#fff7ed;border:1px solid #ffedd5;border-radius:6px;color:#92400e">Đang chỉnh sửa từ khoá: <strong><?= htmlspecialchars($editing['word']) ?></strong> — Sửa qua form thêm hoặc hủy để quay lại.</div>
    <?php else: ?>
        <form method="POST" action="admin.php?action=bad_words_add" style="display:flex;gap:8px;margin-bottom:12px">
            <input name="word" placeholder="Nhập từ khóa xấu" style="flex:1;padding:8px;border:1px solid #e5e7eb;border-radius:6px" />
            <button type="submit" class="btn">Thêm</button>
            <a href="admin.php?action=bad_words_apply" class="btn" style="background:#10b981;color:#fff;text-decoration:none;padding:8px 12px;border-radius:6px">Áp dụng (censor)</a>
        </form>
    <?php endif; ?>

    <div style="max-height:420px;overflow:auto;border:1px solid #f1f5f9;padding:8px;border-radius:6px;background:#ffffff;">
        <?php if (empty($badWords)): ?>
            <div style="color:#6b7280">Chưa có từ khoá xấu.</div>
        <?php else: ?>
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:6px">
                <?php foreach ($badWords as $bw): ?>
                    <li style="display:flex;justify-content:space-between;align-items:center;padding:10px;border-radius:8px;background:#f8fafc;border:1px solid #e6eef8">
                        <div style="display:flex;gap:16px;align-items:center">
                            <div style="width:40px;height:40px;display:flex;align-items:center;justify-content:center;color:#2563eb;font-weight:700;border-radius:6px;background:#e8f0ff">#<?php echo $bw['id']; ?></div>
                            <div>
                                <div style="font-weight:700;font-size:1rem;color:#0f172a"><?php echo htmlspecialchars($bw['word']); ?></div>
                                <div style="color:#6b7280;font-size:0.85rem"><?php echo htmlspecialchars($bw['created_at'] ?? '') ?></div>
                            </div>
                        </div>
                        <div style="display:flex;gap:8px;align-items:center">
                            <div>
                                <?php if ($bw['active']): ?>
                                    <span style="background:#d1fae5;color:#065f46;padding:6px 10px;border-radius:999px;font-size:0.85rem">Đang kích hoạt</span>
                                <?php else: ?>
                                    <span style="background:#fee2e2;color:#b91c1c;padding:6px 10px;border-radius:999px;font-size:0.85rem">Chưa kích hoạt</span>
                                <?php endif; ?>
                            </div>
                            <div style="display:flex;gap:8px;align-items:center">
                                <a href="admin.php?action=bad_words_toggle&id=<?php echo $bw['id']; ?>" style="background:#0ea5a0;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;display:inline-block"><?= $bw['active'] ? 'Tắt' : 'Bật' ?></a>
                                <a href="admin.php?action=bad_words_delete&id=<?php echo $bw['id']; ?>" onclick="return confirm('Xóa từ khoá này?')" style="background:#ef4444;color:#fff;padding:8px 12px;border-radius:6px;text-decoration:none;display:inline-block">Xóa</a>
                            </div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
