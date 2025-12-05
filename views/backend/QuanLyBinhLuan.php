<?php
// Hàm dịch trạng thái
function translateTrangThaiComment($status) {
    $statuses = [
        'Hien' => '🟢 Hiển thị',
        'An' => '🔴 Ẩn'
    ];
    return $statuses[$status] ?? $status;
}

// Hàm rút gọn text dài
function truncateText($text, $length = 100) {
    $text = strip_tags((string)$text);
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

// Nhóm bình luận theo bài viết
$commentsByPost = [];
if (!empty($binhLuans)) {
    foreach ($binhLuans as $comment) {
        $postId = $comment['id_bai_viet'] ?? 0;
        if (!isset($commentsByPost[$postId])) {
            $commentsByPost[$postId] = [
                'tieu_de' => $comment['tieu_de'] ?? 'Không xác định',
                'comments' => []
            ];
        }
        $commentsByPost[$postId]['comments'][] = $comment;
    }
}
?>
<style>
    .backend-comment-card {
        padding: 24px;
        background: #f7f9fc;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }

    .backend-comment-card h2 {
        margin-top: 0;
        margin-bottom: 24px;
        font-size: 1.6rem;
        color: #1f2937;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 10px;
    }

    .stats {
        display: flex;
        gap: 20px;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }

    .stat-card {
        background: white;
        padding: 15px 20px;
        border-radius: 8px;
        border-left: 3px solid #0d6efd;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .stat-value {
        color: #1f2937;
        font-size: 1.5rem;
        font-weight: 700;
        margin-top: 5px;
    }

    .posts-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .post-item {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .post-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .post-header {
        padding: 16px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s;
    }

    .post-header:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .post-title {
        font-weight: 600;
        font-size: 1.1rem;
        margin: 0;
        flex: 1;
    }

    .post-stats {
        background: rgba(255, 255, 255, 0.2);
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .toggle-icon {
        margin-left: 12px;
        transition: transform 0.3s;
    }

    .toggle-icon.open {
        transform: rotate(180deg);
    }

    .post-comments {
        max-height: 0;
        overflow: hidden;
        padding: 0 16px;
        border-top: 1px solid transparent;
        background: #fafafa;
        transition: all 0.3s ease;
    }

    .post-comments.show {
        max-height: 2000px;
        padding: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .comments-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .comment-item {
        background: white;
        border-left: 4px solid #0d6efd;
        padding: 12px;
        border-radius: 6px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        display: block !important;
    }

    .comment-item.hidden {
        border-left-color: #ef4444;
        opacity: 0.7;
    }

    .comment-item.filtered-out {
        display: none !important;
    }

    .comment-author {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.9rem;
    }

    .comment-date {
        font-size: 0.8rem;
        color: #6b7280;
    }

    .comment-content {
        color: #374151;
        font-size: 0.9rem;
        margin: 8px 0;
        line-height: 1.5;
    }

    .comment-actions {
        display: flex;
        gap: 8px;
        margin-top: 10px;
    }

    .action-btn {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        font-size: 0.8rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-block;
    }

    .action-btn.toggle {
        background: #d1fae5;
        color: #065f46;
    }

    .action-btn.toggle:hover {
        background: #a7f3d0;
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #b91c1c;
    }

    .action-btn.delete:hover {
        background: #fecaca;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }

    .status-badge {
        display: inline-block;
        background: #d1fae5;
        color: #065f46;
        padding: 3px 6px;
        border-radius: 3px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .status-badge[data-status="An"] {
        background: #fee2e2;
        color: #b91c1c;
    }

    .pending-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
</style>

<div class="backend-comment-card">
    <h2>Danh sách Bình luận theo bài viết</h2>

    <?php
    $totalComments = count($binhLuans ?? []);
    $hiddenComments = count(array_filter($binhLuans ?? [], fn($c) => $c['trang_thai'] === 'An'));
    $visibleComments = $totalComments - $hiddenComments;
    $postsWithComments = count($commentsByPost);
    ?>

    <div style="margin-bottom: 24px;">
        <div style="display: flex; gap: 12px; align-items: center;">
            <input type="text" id="searchInput" placeholder="Tìm kiếm bài viết hoặc bình luận..." style="
                flex: 1;
                padding: 12px 16px;
                border: 2px solid #e5e7eb;
                border-radius: 8px;
                font-size: 0.95rem;
                transition: all 0.3s;
            " onkeyup="filterPosts()">
            <button onclick="clearSearch()" style="
                padding: 12px 16px;
                background: #ef4444;
                color: white;
                border: none;
                border-radius: 8px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            " onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='#ef4444'">
                ✕ Xóa
            </button>
        </div>
        <div id="searchResult" style="margin-top: 8px; font-size: 0.9rem; color: #6b7280; display: none;"></div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <div class="stat-label">Bài viết có bình luận</div>
            <div class="stat-value"><?= $postsWithComments ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Tổng bình luận</div>
            <div class="stat-value"><?= $totalComments ?></div>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <div class="stat-label">Hiển thị</div>
            <div class="stat-value" style="color: #10b981;"><?= $visibleComments ?></div>
        </div>
        <div class="stat-card" style="border-left-color: #ef4444;">
            <div class="stat-label">Ẩn</div>
            <div class="stat-value" style="color: #ef4444;"><?= $hiddenComments ?></div>
        </div>
    </div>

    <?php if (empty($commentsByPost)): ?>
        <div class="empty-state">
            <div style="font-size:3rem;margin-bottom:10px">💭</div>
            <p>Chưa có bình luận nào</p>
        </div>
    <?php else: ?>
        <div class="posts-list">
            <?php foreach ($commentsByPost as $postId => $postData): ?>
                <?php 
                $pendingCount = count(array_filter($postData['comments'], fn($c) => $c['trang_thai'] === 'An'));
                ?>
                <div class="post-item" data-post-id="<?= $postId ?>">
                    <div class="post-header" onclick="togglePost(this)">
                        <div style="flex: 1;">
                            <p class="post-title"><?php if ($pendingCount > 0): ?><span class="pending-indicator" title="<?= $pendingCount ?> bình luận chờ duyệt"></span> <?php endif; ?>📝 <?= htmlspecialchars($postData['tieu_de']) ?></p>
                        </div>
                        <div class="post-stats">
                            <?= count($postData['comments']) ?> bình luận
                        </div>
                        <div class="toggle-icon">▼</div>
                    </div>
                    
                    <div class="post-comments">
                        <div class="comments-list">
                            <?php foreach ($postData['comments'] as $comment): ?>
                                <div class="comment-item <?= ($comment['trang_thai'] === 'An') ? 'hidden' : '' ?>" data-id="<?= $comment['id'] ?>" data-status="<?= $comment['trang_thai'] ?>">
                                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                                        <div>
                                            <div class="comment-author">👤 <?= htmlspecialchars($comment['ho_ten'] ?? 'Ẩn danh') ?></div>
                                            <div class="comment-date">📅 <?= htmlspecialchars($comment['ngay_binh_luan'] ?? '-') ?></div>
                                        </div>
                                        <span class="status-badge" data-status="<?= $comment['trang_thai'] ?>">
                                            <?= translateTrangThaiComment($comment['trang_thai']) ?>
                                        </span>
                                    </div>

                                    <div class="comment-content">
                                        <?= htmlspecialchars(truncateText($comment['noi_dung'] ?? '', 200)) ?>
                                    </div>

                                    <div class="comment-actions">
                                        <button type="button" onclick="toggleCommentAjax(<?= $comment['id'] ?>, this)" class="action-btn toggle">
                                            <?= ($comment['trang_thai'] === 'Hien') ? '🔒 Ẩn' : '🔓 Hiển thị' ?>
                                        </button>
                                        <a href="admin.php?action=comment_delete&id=<?= $comment['id'] ?>" class="action-btn delete" onclick="return confirm('Xóa bình luận này?')">
                                            🗑️ Xóa
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function togglePost(element) {
    const commentsDiv = element.nextElementSibling;
    const toggleIcon = element.querySelector('.toggle-icon');
    
    commentsDiv.classList.toggle('show');
    toggleIcon.classList.toggle('open');
}

async function toggleCommentAjax(commentId, btn) {
    btn.disabled = true;
    try {
        const url = 'admin.php?action=comment_toggle_status_ajax&id=' + encodeURIComponent(commentId);
        const res = await fetch(url, { method: 'GET', headers: { 'Accept': 'application/json' } });
        const data = await res.json();
        if (data.success) {
            const item = document.querySelector('.comment-item[data-id="' + commentId + '"]');
            if (item) {
                const newStatus = data.new_status;
                item.setAttribute('data-status', newStatus);
                
                // Update status badge
                const badge = item.querySelector('.status-badge');
                if (badge) {
                    badge.setAttribute('data-status', newStatus);
                    badge.textContent = data.new_status_label;
                }
                
                // Update button text
                const toggleBtn = item.querySelector('.action-btn.toggle');
                if (toggleBtn) toggleBtn.textContent = (newStatus === 'Hien') ? '🔒 Ẩn' : '🔓 Hiển thị';
                
                // Hide/show item based on status
                if (newStatus === 'An') {
                    item.classList.add('hidden');
                } else {
                    item.classList.remove('hidden');
                }
                
                // Update post header pending indicator
                updatePostPendingIndicator(item);
            }
        } else {
            alert('Lỗi: ' + (data.message || 'Không thể thay đổi trạng thái'));
        }
    } catch (err) {
        console.error(err);
        alert('Lỗi mạng');
    } finally {
        btn.disabled = false;
    }
}

function updatePostPendingIndicator(commentItem) {
    const postItem = commentItem.closest('.post-item');
    if (!postItem) return;
    
    const comments = postItem.querySelectorAll('.comment-item');
    const pendingCount = Array.from(comments).filter(c => c.getAttribute('data-status') === 'An').length;
    
    const postHeader = postItem.querySelector('.post-header');
    if (postHeader) {
        const title = postHeader.querySelector('.post-title');
        const existingDot = title.querySelector('.pending-indicator');
        
        if (pendingCount > 0) {
            if (!existingDot) {
                const dot = document.createElement('span');
                dot.className = 'pending-indicator';
                dot.title = pendingCount + ' bình luận chờ duyệt';
                title.insertBefore(dot, title.firstChild);
                title.insertBefore(document.createTextNode(' '), dot.nextSibling);
            } else {
                existingDot.title = pendingCount + ' bình luận chờ duyệt';
            }
        } else {
            if (existingDot) existingDot.remove();
            // Remove the extra space if it exists
            const firstChild = title.firstChild;
            if (firstChild && firstChild.nodeType === 3 && firstChild.textContent.trim() === '') {
                firstChild.remove();
            }
        }
    }
}

function filterPosts() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const postItems = document.querySelectorAll('.post-item');
    let visibleCount = 0;
    let totalMatches = 0;

    postItems.forEach(postItem => {
        const postTitle = postItem.querySelector('.post-title').textContent.toLowerCase();
        const comments = postItem.querySelectorAll('.comment-item');
        
        // 1. Kiểm tra xem Tiêu đề có khớp không
        const isTitleMatch = postTitle.includes(searchInput);
        
        let postHasMatch = isTitleMatch; // Mặc định hiển thị bài nếu tiêu đề khớp
        let visibleCommentsCount = 0;

        comments.forEach(comment => {
            const commentText = comment.textContent.toLowerCase();
            const isCommentMatch = commentText.includes(searchInput);
            
            if (searchInput) {
                // LOGIC QUAN TRỌNG ĐÃ SỬA:
                // Hiển thị bình luận nếu: 
                // (1) Bình luận đó chứa từ khóa 
                // HOẶC (2) Tiêu đề bài viết chứa từ khóa (Hiển thị hết để xem ngữ cảnh)
                if (isCommentMatch || isTitleMatch) {
                    comment.classList.remove('filtered-out');
                    visibleCommentsCount++;
                    if (isCommentMatch) totalMatches++; // Chỉ đếm match thực tế
                    postHasMatch = true;
                } else {
                    comment.classList.add('filtered-out');
                }
            } else {
                // Không tìm kiếm thì hiện hết
                comment.classList.remove('filtered-out');
            }
        });

        // Xử lý hiển thị bài viết
        if (postHasMatch || !searchInput) {
            postItem.style.display = 'block';
            if (searchInput) visibleCount++;
            
            // Tự động mở nếu có bình luận khớp cụ thể (không phải do tiêu đề)
            // Nếu chỉ khớp tiêu đề thì cứ để đóng, người dùng click sẽ thấy bình luận
            if (visibleCommentsCount > 0 && !isTitleMatch && searchInput) {
                const postComments = postItem.querySelector('.post-comments');
                const toggleIcon = postItem.querySelector('.toggle-icon');
                if (!postComments.classList.contains('show')) {
                    postComments.classList.add('show');
                    toggleIcon.classList.add('open');
                }
            }
        } else {
            postItem.style.display = 'none';
        }
    });

    // Hiển thị kết quả text
    const searchResult = document.getElementById('searchResult');
    if (searchInput) {
        searchResult.style.display = 'block';
        if (visibleCount === 0) {
            searchResult.textContent = `Không tìm thấy kết quả phù hợp`;
        } else {
            // Logic hiển thị text thông báo thông minh hơn
            searchResult.textContent = `Tìm thấy ${visibleCount} bài viết phù hợp`;
        }
    } else {
        searchResult.style.display = 'none';
    }
}
</script>
