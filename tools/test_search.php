<?php
require __DIR__ . '/../vendor/autoload.php';

try {
    $m = new Website\TinTuc\Models\ThanhVienModel();

    echo "Test search with ASCII keyword 'a'...\n";
    $r = $m->search('a');
    echo 'Rows: ' . count($r) . "\n\n";

    echo "Test search with Vietnamese keyword 'văn'...\n";
    $r2 = $m->search('văn');
    echo 'Rows: ' . count($r2) . "\n\n";
    if (count($r2)) var_dump(array_slice($r2, 0, 2));

} catch (PDOException $e) {
    echo "PDOException: " . $e->getMessage() . "\n";
    if (isset($e->errorInfo)) var_dump($e->errorInfo);
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
