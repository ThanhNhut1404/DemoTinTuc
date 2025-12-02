<?php
use Website\TinTuc\Models\TagModel;

$tagModel = new TagModel();

// Handle delete
if ($_GET['sub'] === 'xoa' && isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($tagModel->delete($id)) {
        $_SESSION['flash_success'] = '✅ Xóa thẻ tag thành công!';
    } else {
        $_SESSION['flash_error'] = '❌ Lỗi khi xóa thẻ tag!';
    }
    header("Location: admin.php?action=tag");
    exit;
}

// Handle add via AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['sub'] === 'them') {
    $ten_tag = trim($_POST['ten_tag'] ?? '');
    header('Content-Type: application/json');
    
    if (empty($ten_tag)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên thẻ tag!']);
        exit;
    }
    
    if ($tagModel->create($ten_tag)) {
        echo json_encode(['success' => true, 'message' => 'Thêm thẻ tag thành công!']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi khi thêm thẻ tag!']);
    }
    exit;
}

// Handle update via AJAX POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['sub'] === 'sua') {
    $id = $_GET['id'] ?? null;
    $ten_tag = trim($_POST['ten_tag'] ?? '');
    header('Content-Type: application/json');
    
    if (empty($ten_tag)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tên thẻ tag!']);
        exit;
    }
    
    if ($id && $tagModel->update($id, $ten_tag)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật thẻ tag thành công!']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật thẻ tag!']);
    }
    exit;
}

$sub = $_GET['sub'] ?? 'danhsach';
$tags = $tagModel->getAll();
?>

<div class="card">
    <h2>Quản lý thẻ Tag</h2>
    <p>Danh sách thẻ tag, thêm mới, chỉnh sửa, xóa.</p>

    <div class="menu-links">
        <a href="admin.php?action=tag&sub=danhsach" class="tag">📋 Danh sách thẻ tag</a>
        <a href="admin.php?action=tag&sub=them" class="tag">➕ Thêm thẻ tag</a>
    </div>

    <div class="fragment">
        <?php if ($sub === 'danhsach'): ?>
            <!-- Danh sách tags -->
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50px;">ID</th>
                        <th>Tên thẻ tag</th>
                        <th class="col-actions">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tags): ?>
                        <?php foreach ($tags as $tag): ?>
                            <tr>
                                <td><?= htmlspecialchars($tag['id']) ?></td>
                                <td><?= htmlspecialchars($tag['ten_tag'] ?? '') ?></td>
                                <td class="col-actions">
                                    <button class="btn btn-edit btnSuaTag" data-id="<?= $tag['id'] ?>" data-name="<?= htmlspecialchars($tag['ten_tag'] ?? '') ?>" style="background: #3b82f6; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 12px;">✏️ Sửa</button>
                                    <a class="btn btn-delete" href="admin.php?action=tag&sub=xoa&id=<?= urlencode($tag['id']) ?>" onclick="return confirm('Bạn có chắc muốn xóa thẻ tag này?');" style="background: #ef4444; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; display: inline-block; font-size: 12px;">🗑️ Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: 20px; color: #999;">Chưa có thẻ tag nào</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        <?php elseif ($sub === 'them'): ?>
            <!-- Form thêm tag -->
            <div style="max-width: 600px;">
                <h3 style="margin-bottom: 20px; color: #333;">Thêm thẻ tag mới</h3>
                
                <div id="themMessage" style="display: none; padding: 12px; border-radius: 6px; margin-bottom: 15px; font-weight: 500;"></div>

                <form id="formThemTag" style="display: flex; flex-direction: column; gap: 15px;">
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Tên thẻ tag:</label>
                        <input type="text" id="tenTagThem" name="ten_tag" required 
                            style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
                    </div>
                    <div>
                        <button type="submit" class="btn" style="background: #0ea5e9; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            ➕ Thêm thẻ tag
                        </button>
                    </div>
                </form>
            </div>

        <?php else: ?>
            <p style="color: #999;">Chọn một mục từ menu bên trên.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Modal Sửa Tag -->
<div id="modalSuaTag" class="modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
    <div class="modal-content" style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0; color: #333; font-size: 20px;">Sửa thẻ tag</h3>
            <button id="closeModalSua" class="btn-close" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">✕</button>
        </div>

        <div id="suaMessage" style="display: none; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-weight: 500;"></div>

        <form id="formSuaTag" style="display: flex; flex-direction: column; gap: 15px;">
            <input type="hidden" id="tagIdSua" name="tag_id" value="">
            <div>
                <label style="display: block; font-weight: 600; margin-bottom: 6px; color: #333;">Tên thẻ tag:</label>
                <input type="text" id="tenTagSua" name="ten_tag" required 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; box-sizing: border-box;">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" id="btnCancelSua" class="btn" style="background: #6b7280; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Hủy
                </button>
                <button type="submit" class="btn" style="background: #0ea5e9; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    ✏️ Cập nhật
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.card {
    background: #fff;
    padding: 20px 25px;
    border-radius: 16px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    margin-top: 20px;
}

.card h2 {
    color: #007bff;
    font-size: 24px;
    margin-bottom: 10px;
}

.card p {
    color: #555;
    margin-bottom: 20px;
}

.menu-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}

.menu-links .tag {
    background: #0d6efd;
    color: #fff;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    border: none;
    transition: 0.2s;
    display: inline-block;
}

.menu-links .tag:hover {
    background: #0b5ed7;
}

.fragment {
    margin-top: 18px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: #f8f9fa;
    border-bottom: 2px solid #dee2e6;
}

.table th {
    padding: 12px;
    text-align: left;
    font-weight: 600;
    color: #333;
}

.table td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
    color: #555;
}

.col-actions {
    text-align: center;
}

.table .col-actions {
    width: auto;
}
</style>

<script>
(function() {
    const formThemTag = document.getElementById('formThemTag');
    const tenTagThem = document.getElementById('tenTagThem');
    const themMessage = document.getElementById('themMessage');
    
    const modalSua = document.getElementById('modalSuaTag');
    const formSua = document.getElementById('formSuaTag');
    const closeModalSua = document.getElementById('closeModalSua');
    const btnCancelSua = document.getElementById('btnCancelSua');
    const tenTagSua = document.getElementById('tenTagSua');
    const tagIdSua = document.getElementById('tagIdSua');
    const suaMessage = document.getElementById('suaMessage');
    
    const btnsSuaTag = document.querySelectorAll('.btnSuaTag');

    // Form thêm
    if (formThemTag) {
        formThemTag.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = tenTagThem.value.trim();
            if (!name) {
                showMessage(themMessage, 'Vui lòng nhập tên thẻ tag!', 'error');
                return;
            }

            try {
                const response = await fetch('admin.php?action=tag&sub=them', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `ten_tag=${encodeURIComponent(name)}`
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(themMessage, data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showMessage(themMessage, data.message, 'error');
                }
            } catch (err) {
                showMessage(themMessage, 'Lỗi kết nối: ' + err.message, 'error');
            }
        });
    }

    // Form sửa
    if (formSua) {
        formSua.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = tenTagSua.value.trim();
            const id = tagIdSua.value;
            
            if (!name) {
                showMessage(suaMessage, 'Vui lòng nhập tên thẻ tag!', 'error');
                return;
            }

            try {
                const response = await fetch(`admin.php?action=tag&sub=sua&id=${id}`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `ten_tag=${encodeURIComponent(name)}`
                });

                const data = await response.json();

                if (data.success) {
                    showMessage(suaMessage, data.message, 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showMessage(suaMessage, data.message, 'error');
                }
            } catch (err) {
                showMessage(suaMessage, 'Lỗi kết nối: ' + err.message, 'error');
            }
        });
    }

    // Edit button listeners
    btnsSuaTag.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            tagIdSua.value = id;
            tenTagSua.value = name;
            suaMessage.style.display = 'none';
            modalSua.style.display = 'flex';
            tenTagSua.focus();
        });
    });

    // Close modal
    function closeModal() {
        modalSua.style.display = 'none';
    }

    closeModalSua.addEventListener('click', closeModal);
    btnCancelSua.addEventListener('click', closeModal);

    modalSua.addEventListener('click', function(e) {
        if (e.target === modalSua) closeModal();
    });

    function showMessage(el, msg, type) {
        el.textContent = msg;
        el.style.display = 'block';
        if (type === 'success') {
            el.style.background = '#d3f9d8';
            el.style.color = '#2b8a3e';
            el.style.border = '1px solid #a9e34b';
        } else {
            el.style.background = '#ffe3e3';
            el.style.color = '#d20000';
            el.style.border = '1px solid #ffb7b7';
        }
    }
})();
</script>
