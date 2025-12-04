<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= $title ?? 'Tin tức hôm nay'; ?></title>
    <link rel="stylesheet" href="/public/assets/css/frontend.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        /* MÀU XANH ĐẬM SIÊU ĐẸP - ĐẬM HƠN RẤT NHIỀU */
        .header-top {
            background: linear-gradient(135deg, #1e40af, #1e40af) !important; /* Đậm + có chiều sâu */
            /* Hoặc dùng màu phẳng cực đậm: */
            /* background-color: #1e40af !important; */
            padding: 1.1rem 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            position: relative;
            z-index: 1000;
        }

        .header-content {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-container {
            position: relative;
            max-width: 520px;
            width: 100%;
        }

        #searchBox {
            width: 100%;
            padding: 0.85rem 1.2rem;
            padding-right: 55px;
            border: none;
            border-radius: 50px;
            font-size: 1.05rem;
            background: #ffffff;
            box-shadow: 0 4px 15px rgba(0,0,0,0.12);
            transition: all 0.3s ease;
        }

        #searchBox:focus {
            outline: none;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.3), 0 6px 20px rgba(0,0,0,0.15);
            transform: translateY(-1px);
        }

        .search-button {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: #1e40af;
            color: white;
            border: none;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
            box-shadow: 0 3px 10px rgba(30,64,175,0.4);
        }

        .search-button:hover {
            background: #1e3a8a;
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 6px 16px rgba(30,64,175,0.5);
        }

        #suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 14px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(59,130,246,0.15);
            max-height: 380px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
            margin-top: 8px;
            padding: 8px 0;
            list-style: none;
        }

        #suggestions li {
            padding: 12px 18px;
            font-size: 15px;
            color: #1f2937;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #suggestions li:hover {
            background: linear-gradient(90deg, #dbeafe, #bfdbfe);
            color: #1e40af;
            font-weight: 500;
            transform: translateX(6px);
        }

        .logo-title {
            font-size: 2.9rem;
            font-weight: 900;
            color: #1e40af;
            text-align: center;
            margin: 2.2rem 0 1rem;
            letter-spacing: 0.8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .tagline {
            background: linear-gradient(to right, #dbeafe, #bfdbfe);
            color: #1e40af;
            padding: 0.85rem 2.2rem;
            border-radius: 50px;
            display: inline-block;
            font-size: 1.15rem;
            font-weight: 600;
            margin-bottom: 2rem;
            box-shadow: 0 4px 12px rgba(59,130,246,0.2);
        }

        /* Nút đăng nhập / đăng xuất đẹp hơn */
        .btn-outline-light {
            border: 2px solid rgba(255,255,255,0.7) !important;
            border-radius: 50px !important;
            padding: 0.6rem 1.6rem !important;
            font-weight: 600;
            color: #fff !important;
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: white !important;
            color: #1e40af !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
        /* Account name inside avatar button */
        .account-name {
            color: #fff;
            font-weight: 700;
            display: inline-block;
            margin-right: 6px;
            white-space: nowrap;
            font-size: 0.95rem;
        }
        .account-dropdown .btn-outline-light { padding-left:10px !important; padding-right:14px !important; }
    </style>
</head>

<body style="background-color:#f8f9fa;">

    <!-- Header xanh đậm cực chất -->
    <header class="header-top">
        <div class="container">
            <div class="header-content">
                <!-- Thanh tìm kiếm -->
                <form id="searchForm" action="index.php" method="get" class="search-container">
                    <input type="hidden" name="action" value="search">
                    <div class="search-wrapper">
                        <input 
                            type="text" 
                            id="searchBox" 
                            name="q" 
                            placeholder="Bạn muốn tìm gì hôm nay?" 
                            autocomplete="off"
                            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button type="submit" class="search-button">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                            </svg>
                        </button>
                        <ul id="suggestions"></ul>
                    </div>
                </form>

                <!-- Tài khoản (compact card) -->
                <?php
                // Normalize avatar URLs and decide which avatar to show in the header button.
                // When viewing an article detail, prefer the author's avatar (`$bv['tac_gia_avatar']`).
                // Otherwise fall back to the logged-in user's avatar (from session).
                $authorAvatarUrl = null;
                if (isset($bv) && !empty($bv['tac_gia_avatar'])) {
                    $authorAvatarVal = $bv['tac_gia_avatar'];
                    if (function_exists('img_url')) {
                        $authorAvatarUrl = img_url($authorAvatarVal);
                    } else {
                        $v = trim((string)$authorAvatarVal);
                        if ($v === '') {
                            $authorAvatarUrl = '/public/uploads/no_avatar.png';
                        } elseif (preg_match('#^https?://#i', $v) || strpos($v, '/') === 0) {
                            $authorAvatarUrl = $v;
                        } else {
                            $authorAvatarUrl = '/public/uploads/' . ltrim($v, '/');
                        }
                    }
                }

                if (isset($_SESSION['id_nguoi_dung'])):
                    $user = [
                        'ten' => $_SESSION['ho_ten'] ?? null,
                        'avatar' => $_SESSION['avatar'] ?? null
                    ];
                    // Normalize session avatar URL
                    $sessAv = $user['avatar'] ?? '';
                    if (function_exists('img_url')) {
                        $sessionAvatarUrl = $sessAv ? img_url($sessAv) : '/public/uploads/no_avatar.png';
                    } else {
                        $sv = trim((string)$sessAv);
                        if ($sv === '') {
                            $sessionAvatarUrl = '/public/uploads/no_avatar.png';
                        } elseif (preg_match('#^https?://#i', $sv) || strpos($sv, '/') === 0) {
                            $sessionAvatarUrl = $sv;
                        } else {
                            $sessionAvatarUrl = '/public/uploads/' . ltrim($sv, '/');
                        }
                    }
                    // Prefer several possible sources for the display name (session top-level, user array, email)
                    $rawDisplay = $_SESSION['ho_ten'] ?? $user['ten'] ?? ($_SESSION['user']['ho_ten'] ?? null) ?? ($_SESSION['user']['email'] ?? null) ?? 'Tài khoản';
                    $displayName = htmlspecialchars($rawDisplay);
                    // Short display for header: first word (first name) — keep full name in title attribute
                    $parts = preg_split('/\s+/', trim((string)$rawDisplay));
                    $firstName = $parts[0] ?? $rawDisplay;
                    $displayShort = htmlspecialchars($firstName);

                    // Choose avatar for the header button: prefer author (when on detail), else session
                    $buttonAvatarUrl = $authorAvatarUrl ?: $sessionAvatarUrl;
                    // Use the same avatar inside the dropdown so the image is consistent when opening
                    $menuAvatarUrl = $buttonAvatarUrl;
                ?>
                    <div class="dropdown account-dropdown" style="position:relative;">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" aria-expanded="false" title="<?= $displayName ?>" style="display:flex;align-items:center;gap:10px;padding:8px 14px;">
                            <img src="<?= htmlspecialchars($buttonAvatarUrl) ?>" alt="avatar" class="account-avatar" style="width:34px;height:34px;border-radius:50%;object-fit:cover;margin-right:8px;border:2px solid rgba(255,255,255,0.85);"> 
                            <span class="account-name"><?= $displayShort ?></span>
                        </button>
                        <div class="dropdown-menu account-dropdown-menu" style="display:none;position:absolute;right:0;top:48px;min-width:260px;border-radius:10px;padding:12px;box-shadow:0 12px 30px rgba(0,0,0,0.12);background:#fff;border:1px solid #eee;z-index:1100;">
                            <div style="display:flex;gap:12px;align-items:center;padding-bottom:8px;border-bottom:1px solid #f1f5f9;margin-bottom:8px;">
                                <img src="<?= htmlspecialchars($menuAvatarUrl) ?>" alt="avatar" style="width:54px;height:54px;border-radius:50%;object-fit:cover;">
                                <div>
                                    <div style="font-weight:700;color:#222;"><?= $displayName ?></div>
                                    <a href="index.php?action=userPage" style="color:#0b5ed7;font-size:13px;text-decoration:none;">Cập nhật thông tin</a>
                                </div>
                            </div>
                            <div style="display:flex;flex-direction:column;gap:6px;">
                                <a href="index.php?action=dathich" style="padding:8px 10px;border-radius:6px;color:#222;text-decoration:none;">Đã thích</a>
                                <a href="index.php?action=daluu" style="padding:8px 10px;border-radius:6px;color:#222;text-decoration:none;">Đã lưu</a>
                                <a href="index.php?action=binhluancuatoi" style="padding:8px 10px;border-radius:6px;color:#222;text-decoration:none;">Bình luận của tôi</a>
                            </div>
                            <div style="text-align:right;margin-top:8px;">
                                <a href="index.php?action=logout" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn đăng xuất không?')">Đăng xuất</a>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="index.php?action=login" class="btn btn-outline-light btn-sm px-4">Đăng Nhập</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Tiêu đề lớn -->
    <div class="container text-center my-4">
        <h1 class="logo-title">Website Tin Tức</h1>
        <div class="tagline">Cập nhật tin tức mới nhất, nhanh chóng & chính xác</div>
    </div>

    <!-- Menu chuyên đề -->
    <?php include __DIR__ . '/../views/frontend/chu_de.php'; ?>

    <!-- JS gợi ý tìm kiếm -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const searchBox = document.getElementById("searchBox");
        const suggestionsBox = document.getElementById("suggestions");
        const searchForm = document.getElementById("searchForm");

        if (!searchBox) return;

        searchBox.addEventListener("keyup", async () => {
            let keyword = searchBox.value.trim();
            if (keyword.length === 0) {
                suggestionsBox.style.display = "none";
                return;
            }

            try {
                const res = await fetch(`index.php?action=suggest&q=${encodeURIComponent(keyword)}`);
                if (!res.ok) throw new Error();
                const suggestions = await res.json();

                suggestionsBox.innerHTML = "";
                if (!suggestions || suggestions.length === 0) {
                    suggestionsBox.innerHTML = '<li class="px-4 py-3 text-muted small">Không tìm thấy kết quả</li>';
                } else {
                    suggestions.forEach(item => {
                        const text = typeof item === 'string' ? item : (item.tieu_de || item.title || '');
                        const li = document.createElement('li');
                        li.textContent = text;
                        li.onclick = () => {
                            searchBox.value = text;
                            suggestionsBox.style.display = "none";
                            searchForm.submit();
                        };
                        suggestionsBox.appendChild(li);
                    });
                }
                suggestionsBox.style.display = "block";
            } catch (err) {
                suggestionsBox.innerHTML = '<li class="px-4 py-3 text-danger small">Lỗi tải gợi ý</li>';
                suggestionsBox.style.display = "block";
            }
        });

        document.addEventListener('click', e => {
            if (!searchBox.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.style.display = "none";
            }
        });
    });
    </script>
    <script>
    // Dropdown behavior for account card in header partial
    (function(){
        document.addEventListener('click', function(ev){
            // close any open account menus when clicking outside
            document.querySelectorAll('.account-dropdown').forEach(drop => {
                const menu = drop.querySelector('.account-dropdown-menu');
                const toggle = drop.querySelector('.dropdown-toggle');
                if (!menu || !toggle) return;
                if (drop.contains(ev.target)) return; // clicked inside -> do nothing
                menu.style.display = 'none';
                toggle.setAttribute('aria-expanded','false');
            });
        });

        document.querySelectorAll('.account-dropdown').forEach(drop => {
            const toggle = drop.querySelector('.dropdown-toggle');
            const menu = drop.querySelector('.account-dropdown-menu');
            if (!toggle || !menu) return;
            toggle.addEventListener('click', function(ev){
                ev.stopPropagation();
                const isOpen = menu.style.display === 'block';
                document.querySelectorAll('.account-dropdown-menu').forEach(m => m.style.display = 'none');
                if (!isOpen) {
                    menu.style.display = 'block';
                    toggle.setAttribute('aria-expanded','true');
                } else {
                    menu.style.display = 'none';
                    toggle.setAttribute('aria-expanded','false');
                }
            });
        });
    })();
    </script>
</body>
</html>