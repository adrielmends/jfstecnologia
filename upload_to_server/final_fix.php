<?php
/**
 * FINAL DATABASE FIX SCRIPT
 * Access this via: exenvios.com.br/final_fix.php
 */
require_once 'config.php';

echo "<h1>Diagnostic & Fix Tool</h1>";

try {
    $db = getDB();
    echo "<p style='color:blue;'>Connected to database: " . DB_NAME . "</p>";

    $columnsToAdd = [
        'customer_email' => 'VARCHAR(255)',
        'modality' => 'VARCHAR(255)',
        'weight' => 'VARCHAR(50)',
        'scheduled_date' => 'DATE',
        'scheduled_time' => 'TIME',
        'sender_cep' => 'VARCHAR(10)',
        'sender_doc' => 'VARCHAR(20)',
        'sender_name' => 'VARCHAR(255)',
        'sender_street' => 'VARCHAR(255)',
        'sender_number' => 'VARCHAR(50)',
        'sender_complement' => 'VARCHAR(255)',
        'sender_neighborhood' => 'VARCHAR(100)',
        'sender_city_uf' => 'VARCHAR(100)',
        'receiver_cep' => 'VARCHAR(10)',
        'receiver_doc' => 'VARCHAR(20)',
        'receiver_name' => 'VARCHAR(255)',
        'receiver_contact' => 'VARCHAR(255)',
        'receiver_street' => 'VARCHAR(255)',
        'receiver_number' => 'VARCHAR(50)',
        'receiver_complement' => 'VARCHAR(255)',
        'receiver_neighborhood' => 'VARCHAR(100)',
        'receiver_city_uf' => 'VARCHAR(100)'
    ];

    echo "<h3>Updating 'orders' table...</h3>";
    
    foreach ($columnsToAdd as $col => $type) {
        try {
            // Check if column exists first (portable way)
            $check = $db->query("SHOW COLUMNS FROM `orders` LIKE '$col'");
            if ($check->rowCount() == 0) {
                $db->exec("ALTER TABLE `orders` ADD COLUMN `$col` $type DEFAULT NULL");
                echo "<span style='color:green;'>[+] Column '$col' added.</span><br>";
            } else {
                echo "<span style='color:gray;'>[ok] Column '$col' already exists.</span><br>";
            }
        } catch (Exception $inner) {
            echo "<span style='color:red;'>[!] Error adding '$col': " . $inner->getMessage() . "</span><br>";
        }
    }

    echo "<h3>Updating 'shipping_services' table...</h3>";
    try {
        $check = $db->query("SHOW COLUMNS FROM `shipping_services` LIKE 'size'");
        if ($check->rowCount() == 0) {
            $db->exec("ALTER TABLE `shipping_services` ADD COLUMN `size` VARCHAR(100) DEFAULT NULL AFTER `name` ");
            echo "<span style='color:green;'>[+] Column 'size' added to shipping_services.</span><br>";
        } else {
            echo "<span style='color:gray;'>[ok] Column 'size' already exists in shipping_services.</span><br>";
        }
    } catch (Exception $e) {
        echo "<span style='color:red;'>[!] Error updating shipping_services: " . $e->getMessage() . "</span><br>";
    }

    echo "<h3>Final Verification:</h3>";
    $q = $db->query("DESCRIBE `orders` ");
    echo "<ul>";
    while($row = $q->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>" . $row['Field'] . "</li>";
    }
    echo "</ul>";

    echo "<p style='font-weight:bold; color:green;'>DONE! Please try to process an order now.</p>";

} catch (Exception $e) {
    echo "<h2 style='color:red;'>FATAL ERROR</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
?>
