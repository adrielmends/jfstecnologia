<?php
require_once 'config.php';
try {
    $db = getDB();
    $stmt = $db->query("DESCRIBE payments");
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
