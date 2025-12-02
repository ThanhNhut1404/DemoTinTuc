<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #007bff, #00c6ff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-box {
            background: rgba(255,255,255,0.95);
            width: 420px;
            padding: 45px 40px;
            border-radius: 26px;
            border: 1px solid rgba(255,255,255,0.4);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.12), 0 8px 16px rgba(0,0,0,0.05);
            transition: 0.3s;
        }

        h2 {
    margin-bottom: 25px;
    color: #333;
    font-size: 24px;
    letter-spacing: 0.5px;

    text-align: center;     /* căn giữa */
    font-weight: 700;       /* in đậm */
    text-transform: uppercase; /* in hoa */
}


        label {
            display: block;
            text-align: left;
            width: 100%;
            font-weight: 500;
            color: #444;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .input-group {
            position: relative;
            width: 100%;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 14px 40px 14px 15px; /* Padding để icon không chồng chữ */
            border: 1px solid #dcdcdc;
            border-radius: 12px;
            font-size: 15px;
            transition: 0.3s;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.35);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #666;
        }

        .toggle-password:hover {
            color: #007bff;
        }

        .forgot-password {
            text-align: right;
            margin-bottom: 20px;
        }

        .forgot-password a {
            font-size: 13px;
            color: #007bff;
            text-decoration: none;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        button {
            width: 100%;
            padding: 14px;
            font-size: 17px;
            background: linear-gradient(90deg, #007bff, #0052cc);
            border: none;
            border-radius: 12px;
            color: #fff;
            cursor: pointer;
            font-weight: 600;
            transition: 0.3s;
        }

        button:hover {
            transform: translateY(-2px);
            background: linear-gradient(90deg, #0056b3, #003d99);
        }

        p {
            margin-top: 15px;
            font-size: 14px;
            color: #444;
            text-align: center;
        }

        a {
            color: #007bff;
            font-weight: 600;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Đăng nhập</h2>
    <form action="index.php?action=do_login" method="POST">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Mật khẩu:</label>
        <div class="input-group">
            <input type="password" id="password" name="mat_khau" required>
            <span class="toggle-password" onclick="togglePassword('password', this)">👁️</span>
        </div>

        <div class="forgot-password">
            <a href="index.php?action=forgot_password">Quên mật khẩu?</a>

        </div>

        <button type="submit">Đăng nhập</button>
    </form>

    <p>
        Chưa có tài khoản? <a href="index.php?action=register">Đăng ký ngay</a>
    </p>
</div>

<script>
    function togglePassword(id, icon) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            icon.textContent = "🙈";
        } else {
            input.type = "password";
            icon.textContent = "👁️";
        }
    }
</script>
</body>
</html>
