<?php
require_once 'config.php';
try {
    $db = getDB();
    $sql = file_get_contents('sync_setup.sql');
    $db->exec($sql);
    echo "Migration successful!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
