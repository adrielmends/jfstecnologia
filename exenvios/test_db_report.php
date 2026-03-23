<?php
// test_db_report.php
require_once 'config.php';
$log = "";

try {
    $db = getDB();
    $log .= "Connected to database successfully.\n";
    
    $tables = ['users', 'orders', 'locations', 'shipping_services', 'lockers', 'settings'];
    foreach ($tables as $table) {
        try {
            $count = $db->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            $log .= "Table '$table': $count records.\n";
            
            if ($count > 0) {
                $sample = $db->query("SELECT * FROM `$table` LIMIT 1")->fetch(PDO::FETCH_ASSOC);
                $log .= "Sample from '$table': " . json_encode($sample) . "\n";
            }
        } catch (Exception $e) {
            $log .= "Error reading table '$table': " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    $log .= "CRITICAL DATABASE ERROR: " . $e->getMessage() . "\n";
}

file_put_contents('db_report.txt', $log);
echo "Report generated in db_report.txt";
?>
