<?php
require_once __DIR__ . '/../vendor/autoload.php';
// kết nối database
use Website\TinTuc\Database;
$db = Database::getInstance()->getConnection();
// test kết nối
echo "<h2>✅ Kết nối database thành công!</h2>";
