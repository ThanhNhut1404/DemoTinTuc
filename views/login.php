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
            background: #fff;
            padding: 40px 35px;
            border-radius: 15px;
            width: 350px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
            letter-spacing: 0.5px;
        }

        form {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .form-group {
            width: 100%;
            text-align: left;
            margin-bottom: 18px;
            position: relative; /* để đặt con mắt */
        }

        label {
            display: block;
            font-weight: 500;
            color: #ccccccff;
            margin-bottom: 6px;
            font-size: 14px;
            padding-left: 3px;
        }

        input {
            width: 100%;
            padding: 12px 40px 12px 15px; /* thêm padding phải cho nút con mắt */
            border: 1px solid #ddddddff;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
            outline: none;
        }

        .toggle-password {
            position: absolute;
            top: 65%;
            right: 10px;
            transform: translateY(-50%);
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .toggle-password svg {
            width: 22px;
            height: 22px;
            fill: #bbbbbbff;
            transition: fill 0.3s ease;
        }

        .toggle-password:hover svg {
            fill: #bbbbbbff;
        }

        button.submit-btn {
            width: 100%;
            background: #007bff;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        button.submit-btn:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        .extra-links {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }

        .extra-links a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        .extra-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Đăng nhập</h2>
    <form action="index.php?action=do_login" method="POST">

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" placeholder="Nhập email" required>
        </div>

        <div class="form-group">
            <label>Mật khẩu:</label>
            <input id="mat_khau" type="password" name="mat_khau" placeholder="Nhập mật khẩu" required>
            <button type="button" class="toggle-password" aria-label="Hiện mật khẩu" title="Hiện/Ẩn mật khẩu">
                <svg id="eyeIcon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 5c-7 0-11 6-11 7s4 7 11 7 11-6 11-7-4-7-11-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                    <circle cx="12" cy="12" r="2.5"/>
                </svg>
            </button>
        </div>

        <button type="submit" class="submit-btn">Đăng nhập</button>
    </form>

    <div class="extra-links">
        <p><a href="index.php?action=forgot">Quên mật khẩu?</a></p>
        <p>Chưa có tài khoản? <a href="index.php?action=register">Đăng ký</a></p>
    </div>
</div>

<script>
    const toggleBtn = document.querySelector('.toggle-password');
    const pwdInput = document.getElementById('mat_khau');
    const eyeIcon = document.getElementById('eyeIcon');
    let visible = false;

    toggleBtn.addEventListener('click', function () {
        visible = !visible;

        if (visible) {
            // Hiển thị mật khẩu + con mắt có gạch chéo
            pwdInput.type = 'text';
            eyeIcon.innerHTML = `
                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z" fill="none" stroke="#bbbbbbff" stroke-width="2"/>
                <circle cx="12" cy="12" r="3" fill="#bbbbbbff"/>
                <line x1="3" y1="3" x2="21" y2="21" stroke="#bbbbbbff" stroke-width="2"/>
            `;
        } else {
            // Ẩn mật khẩu + icon bình thường
            pwdInput.type = 'password';
            eyeIcon.innerHTML = `
                <path d="M12 5c-7 0-11 6-11 7s4 7 11 7 11-6 11-7-4-7-11-7zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8z"/>
                <circle cx="12" cy="12" r="2.5"/>
            `;
        }
    });
</script>
</body>
</html>
