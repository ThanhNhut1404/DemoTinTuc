<?php
// Test DateTime
$dob = DateTime::createFromFormat('Y-m-d', '2000-01-01');
$today = new DateTime('today');

var_dump($dob);
var_dump($today);
echo ($dob >= $today) ? 'Không hợp lệ' : 'Hợp lệ';
?>
