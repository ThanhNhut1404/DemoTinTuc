<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            color: #333;
        }

        header {
            background: #fff;
            padding: 15px 30px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
            color: #007bff;
        }

        header nav a {
            margin-left: 20px;
            text-decoration: none;
            color: #007bff;
            font-weight: 500;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 0 20px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .welcome {
            text-align: center;
            padding: 50px 20px;
        }

        .welcome h2 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #007bff;
        }

        .welcome p {
            font-size: 18px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <header>
        <h1>Website Tin Tức</h1>
        <nav>
            <a href="index.php?action=home">Trang chủ</a>
            <a href="index.php?action=login">Đăng nhập</a>
            <a href="index.php?action=register">Đăng ký</a>
        </nav>
    </header>

    <div class="container">
        <div class="welcome">
            <h2>Chào mừng đến với Website Tin Tức!</h2>
            <p>Tại đây bạn có thể xem các tin tức mới nhất, đăng ký tài khoản để nhận thông báo, và quản lý thông tin cá nhân.</p>
        </div>
    </div>
</body>
</html>
