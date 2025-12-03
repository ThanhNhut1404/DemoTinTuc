<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quên mật khẩu</title>
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

        .form-container {
            background: #fff;
            padding: 40px 35px;
            border-radius: 15px;
            width: 360px;
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

        label {
            font-size: 15px;
            font-weight: 500;
            color: #333;
            align-self: flex-start;
            margin-bottom: 5px;
            margin-left: 5px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: #007bff;
            box-shadow: 0 0 6px rgba(0, 123, 255, 0.3);
            outline: none;
        }

        button {
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

        button:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        p {
            margin-top: 18px;
            font-size: 15px;
        }

        a {
            color: #007bff;
            text-decoration: none;
            font-weight: 500;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Quên mật khẩu</h2>
        <?php if (isset($_GET['sent']) && $_GET['sent'] == '1'): ?>
            <p id="fp-success" style="color:green; font-weight:600;">Yêu cầu đặt lại mật khẩu đã được gửi. Vui lòng kiểm tra email của bạn.</p>
            <p style="margin-top:12px;">Nếu bạn không thấy email, hãy kiểm tra mục SPAM hoặc chờ vài phút.</p>
            <p style="margin-top:12px;">Bạn sẽ được chuyển về trang đăng nhập trong <span id="fp-count">5</span> giây.</p>
            <script>
                (function(){
                    var t = 5;
                    var el = document.getElementById('fp-count');
                    var iv = setInterval(function(){
                        t--; if (t < 0) { clearInterval(iv); return; }
                        if (el) el.textContent = t;
                    }, 1000);
                    setTimeout(function(){ window.location.href = 'index.php?action=login'; }, 5000);
                })();
            </script>
        <?php else: ?>
        <form action="index.php?action=forgot_password" method="POST">

            <label for="email">Email:</label>
            <input id="email" type="email" name="email" placeholder="Nhập email của bạn" required>
            <button type="submit">Gửi yêu cầu</button>
        </form>
        <?php endif; ?>
        <p><a href="index.php?action=login">← Quay lại đăng nhập</a></p>
    </div>
</body>
</html>
