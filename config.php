<?php
$conn = new mysqli("localhost", "root", "", "website_tin_tuc");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
