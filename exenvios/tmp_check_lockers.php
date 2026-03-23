<?php
require_once 'config.php';
try {
    $db = getDB();
    $sqlLockers = "SELECT l.*, loc.name as location_name,
                   (SELECT COUNT(*) FROM orders o WHERE o.locker_id = l.id AND o.status NOT IN ('delivered', 'failed')) as occupied_count 
                   FROM `lockers` l 
                   LEFT JOIN `locations` loc ON l.location_id = loc.id ";
    $lockersStmt = $db->query($sqlLockers);
    $lockers = $lockersStmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($lockers, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo $e->getMessage();
}
