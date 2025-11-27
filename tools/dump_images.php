<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/Database.php';

use Website\TinTuc\Database;

$dbObj = new Database();
$conn = $dbObj->connect();

function fetchSample($conn, $table, $limit = 5) {
    try {
        $stmt = $conn->query("SELECT * FROM `" . $table . "` LIMIT " . (int)$limit);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return ['error' => $e->getMessage()];
    }
}

$result = [
    'banner' => fetchSample($conn, 'banner', 10),
    'quang_cao' => fetchSample($conn, 'quang_cao', 10),
    'bai_viet' => fetchSample($conn, 'bai_viet', 10),
];

header('Content-Type: application/json; charset=utf-8');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
