<div class="card">
    <h2 style="margin-top:0;margin-bottom:14px">📅 Lịch đăng bài</h2>
    
    <?php if (empty($baiviets)): ?>
        <p style="text-align:center;color:#999">Không có bài viết nào được lên lịch đăng trong tương lai.</p>
    <?php else: ?>
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse;font-size:14px">
                <thead>
                    <tr style="background:#f5f7fa;border-bottom:1px solid #e6eef8">
                        <th style="padding:12px;text-align:left;font-weight:600">Tiêu đề</th>
                        <th style="padding:12px;text-align:left;font-weight:600">Chuyên mục</th>
                        <th style="padding:12px;text-align:center;font-weight:600">Ngày đăng</th>
                        <th style="padding:12px;text-align:center;font-weight:600">Thời gian còn lại</th>
                        <th style="padding:12px;text-align:center;font-weight:600">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($baiviets as $b): ?>
                        <?php
                            // Tính thời gian còn lại
                            $ngayDang = strtotime($b['ngay_dang']);
                            $now = time();
                            $diff = $ngayDang - $now;
                            
                            if ($diff > 86400) {
                                $thoiGian = floor($diff / 86400) . ' ngày';
                            } elseif ($diff > 3600) {
                                $thoiGian = floor($diff / 3600) . ' giờ';
                            } else {
                                $thoiGian = floor($diff / 60) . ' phút';
                            }
                        ?>
                        <tr style="border-bottom:1px solid #f1f5f9">
                            <td style="padding:12px;max-width:300px">
                                <a href="admin.php?action=edit&id=<?= htmlspecialchars($b['id']) ?>" style="color:#0066cc;text-decoration:none">
                                    <strong><?= htmlspecialchars(substr($b['tieu_de'], 0, 60)) ?></strong>
                                </a>
                            </td>
                            <td style="padding:12px;color:#666">
                                <?php if (!empty($b['ten_chuyen_muc'])): ?>
                                    <small style="background:#f0f0f0;padding:4px 8px;border-radius:4px">
                                        <?= htmlspecialchars($b['ten_chuyen_muc']) ?>
                                    </small>
                                <?php else: ?>
                                    <small style="color:#999">Không xác định</small>
                                <?php endif; ?>
                            </td>
                            <td style="padding:12px;text-align:center;color:#666">
                                <code style="font-size:12px;background:#f5f5f5;padding:2px 6px;border-radius:3px">
                                    <?= date('d/m/Y H:i', $ngayDang) ?>
                                </code>
                            </td>
                            <td style="padding:12px;text-align:center;color:#0066cc;font-weight:600">
                                <?= htmlspecialchars($thoiGian) ?>
                            </td>
                            <td style="padding:12px;text-align:center">
                                <a href="admin.php?action=edit&id=<?= htmlspecialchars($b['id']) ?>" class="btn btn-primary btn-sm" style="padding:6px 12px;margin:0 4px">Sửa</a>
                                <a href="admin.php?action=delete&id=<?= htmlspecialchars($b['id']) ?>" class="btn btn-danger btn-sm" style="padding:6px 12px;margin:0 4px" onclick="return confirm('Xoá bài này?')">Xoá</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
