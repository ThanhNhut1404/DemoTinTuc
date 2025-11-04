<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Đăng xuất</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #000000ff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .logout-box {
      background: white;
      padding: 30px;
      border-radius: 10px;
      width: 350px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      text-align: center;
    }
    a {
      display: inline-block;
      margin-top: 15px;
      text-decoration: none;
      color: #007bff;
    }
    a:hover {
      color: #0056b3;
    }
  </style>
</head>
<body>
<div class="logout-box">
  <h2>Bạn đã đăng xuất!</h2>
  <p>Cảm ơn bạn đã sử dụng trang web.</p>
  <a href="index.php?action=login">← Quay lại trang đăng nhập</a>
</div>
</body>
</html>
