<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký tài khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .register-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 350px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        input {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>Đăng ký</h2>
    <form action="index.php?action=do_register" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required><br>

    <label for="password">Mật khẩu:</label>
    <input type="password" id="password" name="password" required><br>

    <label for="confirm_password">Xác nhận mật khẩu:</label>
    <input type="password" id="confirm_password" name="confirm_password" required><br>

    <button type="submit">Đăng ký</button>
</form>

    <p style="text-align:center;margin-top:10px;">
        Đã có tài khoản? <a href="index.php?action=login">Đăng nhập</a>
    </p>
</div>
</body>
</html>
