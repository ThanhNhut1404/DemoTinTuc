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
            transition: all 0.3s ease;
        }

        .btn-outline-light:hover {
            background: white !important;
            color: #1e40af !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.2);
        }
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

                <!-- Tài khoản -->
                <?php if (isset($_SESSION['id_nguoi_dung'])): ?>
                    <span class="text-white fw-medium">Xin chào, <strong><?= htmlspecialchars($_SESSION['ho_ten'] ?? 'User') ?></strong>!</span>
                    <a href="index.php?action=logout" class="btn btn-outline-light btn-sm px-4"
                       onclick="return confirm('Bạn có chắc muốn đăng xuất không?')">
                        Đăng xuất
                    </a>
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
</body>
</html>