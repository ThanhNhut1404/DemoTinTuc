<div class="card">
    <h2 style="margin-top:0;margin-bottom:14px">Thêm mới bài viết</h2>

    <form method="POST" action="admin.php?action=store" enctype="multipart/form-data" style="display:grid;grid-template-columns:1fr 420px;gap:20px;align-items:start">
        <div>
            <div class="form-group" style="margin-bottom:12px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Tiêu đề</label>
                <input type="text" name="tieu_de" required style="width:100%;padding:10px;border:1px solid #e6eef8;border-radius:8px" />
            </div>

            <div class="form-group" style="margin-bottom:12px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Mô tả ngắn</label>
                <textarea name="mo_ta_ngan" rows="3" style="width:100%;padding:10px;border:1px solid #e6eef8;border-radius:8px"></textarea>
            </div>

            <div class="form-group" style="margin-bottom:12px">
                <label style="display:block;font-weight:600;margin-bottom:6px">Nội dung</label>
                <textarea name="noi_dung" rows="6" style="width:100%;padding:10px;border:1px solid #e6eef8;border-radius:8px"></textarea>
            </div>

            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center;margin-top:8px">
                <div style="flex:1;min-width:160px">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Chuyên mục cha</label>
                    <select id="id_chuyen_muc_cha" name="id_chuyen_muc_cha" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="">-- Chọn chuyên mục cha --</option>
                        <?php if (!empty($chuyenMucChaList)): ?>
                            <?php foreach ($chuyenMucChaList as $cmc): ?>
                                <option value="<?= htmlspecialchars($cmc['id'] ?? '') ?>"><?= htmlspecialchars($cmc['ten_chuyen_muc'] ?? $cmc['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?php
                                // Fallback: derive parent ids from $chuyenMucList if controller didn't provide parents
                                $parentsSeen = [];
                                if (!empty($chuyenMucList)) {
                                    foreach ($chuyenMucList as $cmf) {
                                        $pid = $cmf['id_cha'] ?? null;
                                        if ($pid && !in_array($pid, $parentsSeen, true)) {
                                            $parentsSeen[] = $pid;
                                        }
                                    }
                                    // Try to show readable names for parents: prefer $chuyenMucChaList if available,
                                    // otherwise try to find a matching entry in $chuyenMucList that may contain the parent's name.
                                    foreach ($parentsSeen as $pid) {
                                        $label = 'Chuyên mục cha ' . htmlspecialchars($pid);
                                        if (!empty($chuyenMucChaList)) {
                                            foreach ($chuyenMucChaList as $pc) {
                                                if (($pc['id'] ?? '') == $pid) {
                                                    $label = htmlspecialchars($pc['ten_chuyen_muc'] ?? $pc['name'] ?? $label);
                                                    break;
                                                }
                                            }
                                        } else {
                                            // try to find a chuyenMuc entry whose id matches the pid (in case parents were included)
                                            foreach ($chuyenMucList as $possible) {
                                                if (($possible['id'] ?? '') == $pid) {
                                                    $label = htmlspecialchars($possible['ten_chuyen_muc'] ?? $possible['name'] ?? $label);
                                                    break;
                                                }
                                            }
                                        }
                                        echo '<option value="' . htmlspecialchars($pid) . '">' . $label . '</option>';
                                    }
                                }
                                ?>
                        <?php endif; ?>
                    </select>

                    <label style="display:block;font-weight:600;margin:10px 0 6px">Chuyên mục con</label>
                    <select id="id_chuyen_muc" name="id_chuyen_muc" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="">-- Chọn chuyên mục con --</option>
                        <?php if (!empty($chuyenMucList) && empty($chuyenMucChaList)): ?>
                            <?php foreach ($chuyenMucList as $cm): ?>
                                <option value="<?= htmlspecialchars($cm['id'] ?? '') ?>"><?= htmlspecialchars($cm['ten_chuyen_muc'] ?? $cm['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div style="flex:1;min-width:160px">
                    <label style="display:block;font-weight:600;margin-bottom:6px">Thẻ tag</label>
                    <select name="tag" style="width:100%;padding:8px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="">-- Chọn thẻ tag --</option>
                        <?php if (!empty($tagList)): ?>
                            <?php foreach ($tagList as $t): ?>
                                <option value="<?= htmlspecialchars($t['id'] ?? '') ?>"><?= htmlspecialchars($t['ten_tag'] ?? $t['name'] ?? '') ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div style="display:flex;gap:12px;align-items:center;margin-top:12px">
                <label style="display:flex;align-items:center;gap:8px"><input type="checkbox" name="la_noi_bat"> Tin nổi bật</label>
                <label style="display:flex;align-items:center;gap:8px">Trạng thái
                    <select name="trang_thai" style="margin-left:6px;padding:6px;border:1px solid #e6eef8;border-radius:8px">
                        <option value="Nhap">Nháp</option>
                        <option value="Cho_duyet">Chờ duyệt</option>
                        <option value="Da_dang">Đã đăng</option>
                        <option value="Tu_choi">Từ chối</option>
                    </select>
                </label>
                <label style="display:flex;align-items:center;gap:8px">Ngày đăng
                    <input type="datetime-local" name="ngay_dang" style="margin-left:6px;padding:6px;border:1px solid #e6eef8;border-radius:8px" />
                </label>
            </div>

            <div style="margin-top:16px">
                <button type="submit" class="btn btn-role">Lưu bài viết</button>
            </div>
        </div>

        <aside style="min-width:240px">
            <div style="border:1px dashed #e6eef8;padding:12px;border-radius:8px;background:#fafcff;text-align:center">
                <label style="display:block;font-weight:600;margin-bottom:8px">Ảnh đại diện</label>
                <input id="anh_dai_dien_input" type="file" name="anh_dai_dien" accept="image/*" />
                <div style="margin-top:10px;color:#6b7280;font-size:13px">Kích thước đề xuất: 800x450</div>
            </div>

                <div style="margin-top:14px;border-radius:8px;padding:12px;background:#fff;border:1px solid #f1f5f9">
                <label style="display:block;font-weight:600;margin-bottom:8px">Xem trước</label>
                <div id="preview-container" style="width:100%;height:260px;border-radius:6px;background:#f8fafc;display:flex;align-items:center;justify-content:center;color:#cbd5e1;position:relative;overflow:hidden">
                    <img id="preview-image" src="" alt="" style="display:none;width:100%;height:100%;object-fit:cover;border-radius:6px" />
                    <div id="preview-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#cbd5e1">Chưa có ảnh</div>
                </div>
            </div>
        </aside>
    </form>
</div>

<!-- Rich editor (CKEditor 5) with simple upload -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
ClassicEditor
    .create(document.querySelector('textarea[name="noi_dung"]'), {
        simpleUpload: {
            // The URL that the images are uploaded to.
            uploadUrl: 'admin.php?action=upload_image'
        }
    })
    .catch(error => { console.error(error); });
</script>
<script>
// Category parent -> child wiring
(function(){
    // Data: chuyenMucList should be an array of child categories with 'id', 'ten_chuyen_muc', 'id_cha'
    const chuyenMucList = <?php echo json_encode(!empty($chuyenMucList) ? $chuyenMucList : []); ?>;
    const parentSelect = document.getElementById('id_chuyen_muc_cha');
    const childSelect = document.getElementById('id_chuyen_muc');

    if (!parentSelect || !childSelect) return;

    // Build map: parentId -> [children]
    const map = {};
    chuyenMucList.forEach(c => {
        const pid = c.id_cha || c.idCha || c.parent_id || null;
        if (!pid) return;
        if (!map[pid]) map[pid] = [];
        map[pid].push(c);
    });

    function populateChildren(parentId) {
        // Clear
        childSelect.innerHTML = '<option value="">-- Chọn chuyên mục con --</option>';
        if (!parentId) return;
        const list = map[parentId] || [];
        list.forEach(ch => {
            const opt = document.createElement('option');
            opt.value = ch.id;
            opt.textContent = ch.ten_chuyen_muc || ch.name || ('Chuyên mục ' + ch.id);
            childSelect.appendChild(opt);
        });
    }

    // On change, repopulate child select
    parentSelect.addEventListener('change', function(){
        populateChildren(this.value);
    });

    // If parent already selected (e.g. when editing), populate children and keep selection
    (function initFromServer(){
        const selectedParent = parentSelect.value;
        if (selectedParent) populateChildren(selectedParent);
    })();
})();

// Image preview
(function(){
    const input = document.getElementById('anh_dai_dien_input');
    const img = document.getElementById('preview-image');
    const placeholder = document.getElementById('preview-placeholder');
    if (!input) return;
    input.addEventListener('change', function(){
        const f = this.files && this.files[0];
        if (!f) {
            img.style.display = 'none';
            img.src = '';
            placeholder.style.display = 'flex';
            return;
        }
        const url = URL.createObjectURL(f);
        img.src = url;
        img.style.display = 'block';
        placeholder.style.display = 'none';
    });
})();
</script>