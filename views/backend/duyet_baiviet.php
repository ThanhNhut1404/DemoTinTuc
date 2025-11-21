<?php
// Fragment: Duyệt bài viết
// Expecting: $baiviets (array of posts). Each item should at least have keys: id, tieu_de, id_tac_gia OR ten_tac_gia, ngay_dang, trang_thai
?>
<div class="card">
    <div class="card-header">
        <h3 style="margin:0;">Duyệt bài viết</h3>
    </div>
    <div class="card-body">
        <?php if (empty($baiviets)): ?>
            <p>Không có bài viết chờ duyệt.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th style="width:60px">ID</th>
                            <th>Tiêu đề</th>
                            <th style="width:160px">Tác giả</th>
                            <th style="width:140px">Ngày đăng</th>
                            <th style="width:120px">Trạng thái</th>
                            <th style="width:260px">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($baiviets as $b): ?>
                            <tr>
                                <td><?= htmlspecialchars($b['id']) ?></td>
                                <td><?= htmlspecialchars($b['tieu_de'] ?? '') ?></td>
                                <td><?= htmlspecialchars($b['ten_tac_gia'] ?? $b['id_tac_gia'] ?? '') ?></td>
                                <td><?= htmlspecialchars($b['ngay_dang'] ?? '') ?></td>
                                <td>
                                    <?php
                                        $st = isset($b['trang_thai']) ? (int)$b['trang_thai'] : -1;
                                        if ($st === 0) echo 'Chờ duyệt';
                                        elseif ($st === 1) echo 'Đã duyệt';
                                        elseif ($st === 2) echo 'Từ chối';
                                        else echo 'Không rõ';
                                    ?>
                                </td>
                                <td>
                                    <?php
                                        $status = strtolower(trim((string)($b['trang_thai'] ?? '')));
                                        $isPending = in_array($status, ['cho_duyet', '0', 'pending'], true);
                                    ?>
                                    <?php if ($isPending): ?>
                                        <form method="post" action="admin.php?action=bai_viet&sub=duyet_action" style="display:inline">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
                                            <input type="hidden" name="action_type" value="approve">
                                            <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Bạn có chắc muốn duyệt bài viết này?')">Duyệt</button>
                                        </form>

                                        <form method="post" action="admin.php?action=bai_viet&sub=duyet_action" style="display:inline; margin-left:6px">
                                            <input type="hidden" name="id" value="<?= htmlspecialchars($b['id']) ?>">
                                            <input type="hidden" name="action_type" value="reject">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn từ chối bài viết này?')">Từ chối</button>
                                        </form>

                                        <a href="admin.php?action=bai_viet&sub=sua&id=<?= htmlspecialchars($b['id']) ?>" class="btn btn-primary btn-sm" style="margin-left:6px">Sửa</a>
                                    <?php else: ?>
                                        <?php if ($status === 'da_dang' || $status === 'da-dang' || $status === 'dang'): ?>
                                            <span class="badge badge-success">Đã đăng</span>
                                        <?php elseif ($status === 'tu_choi' || $status === 'tu-choi' || $status === '2'): ?>
                                            <span class="badge badge-secondary">Từ chối</span>
                                        <?php else: ?>
                                            <span class="badge"><?= htmlspecialchars($b['trang_thai'] ?? '') ?></span>
                                        <?php endif; ?>
                                        <a href="admin.php?action=bai_viet&sub=sua&id=<?= htmlspecialchars($b['id']) ?>" class="btn btn-primary btn-sm" style="margin-left:8px">Sửa</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
