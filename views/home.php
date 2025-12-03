<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php?action=login");
    exit;
}
?>

<?php include 'header.php'; ?>  <!-- đặt ở đây -->

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang chủ</title>
</head>
<body>
    <?php
    // Hiển thị flash message nếu có
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $isError = strpos($flash, '❌') === 0;
        $isSuccess = strpos($flash, '✅') === 0;
        $bgColor = $isError ? '#f8d7da' : '#d4edda';
        $textColor = $isError ? '#721c24' : '#155724';
        echo "<div id=\"flash-message\" style=\"padding:15px;margin:15px;border-radius:8px;background:{$bgColor};color:{$textColor};text-align:center;font-weight:500;border:1px solid " . ($isError ? '#f5c6cb' : '#c3e6cb') . ";\">" . htmlspecialchars($flash) . "</div>";
        
        // Nếu là thông báo thành công, delay 2 giây rồi cuộn xuống nội dung
        if ($isSuccess) {
            echo "<script>
                setTimeout(function() {
                    var msg = document.getElementById('flash-message');
                    if (msg) {
                        msg.style.transition = 'opacity 0.5s ease';
                        msg.style.opacity = '0';
                        setTimeout(function() { msg.remove(); }, 500);
                    }
                }, 2000);
            </script>";
        }
    }
    ?>
    <h1>Chào mừng <?= htmlspecialchars($_SESSION['user']['email']) ?> đã đăng nhập!</h1>
    <a href="index.php?action=logout">Đăng xuất</a>
</body>
</html>
