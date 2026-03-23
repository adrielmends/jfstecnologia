<?php
require_once 'config.php';
try {
    $db = getDB();
    $stmt = $db->query("SELECT id, locker_id, status FROM orders LIMIT 10");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($rows, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
