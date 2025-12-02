<?php
require_once __DIR__ . '/vendor/autoload.php';
use Website\TinTuc\Database;

$db = new Database();
$conn = $db->connect();

try {
    $conn->exec('ALTER TABLE bai_viet ADD COLUMN id_the_tag INT NULL AFTER id_chuyen_muc');
    echo "✓ Column id added successfully.\n";
} catch (Exception $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "✓ Column id already exists.\n";
    } else {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>
