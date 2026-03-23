<?php
require_once 'config.php';
try {
    $db = getDB();
    $tables = ['locations', 'shipping_services', 'lockers', 'users', 'orders'];
    foreach ($tables as $t) {
        $count = $db->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "$t: $count records\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
