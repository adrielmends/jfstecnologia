<?php
/**
 * Repair Orders Linkage Script - VERBOSE VERSION
 */

require_once 'config.php';

try {
    $db = getDB();
    
    echo "--- DATABASE MIGRATION START ---\n";
    
    // 1. Ensure columns exist
    $colsToEnsure = [
        'user_id' => "INT DEFAULT NULL",
        'customer_email' => "VARCHAR(255) DEFAULT NULL"
    ];

    foreach ($colsToEnsure as $col => $def) {
        $check = $db->query("SHOW COLUMNS FROM `orders` LIKE '$col'")->fetch();
        if (!$check) {
            $db->exec("ALTER TABLE `orders` ADD COLUMN `$col` $def");
            echo "- Column '$col' added.\n";
        } else {
            echo "- Column '$col' already exists.\n";
        }
    }

    echo "\n--- LINKAGE REPAIR START ---\n";
    
    $users = $db->query("SELECT id, name, email, cpf FROM users")->fetchAll();
    $totalRepaired = 0;
    
    foreach ($users as $user) {
        $uid = $user['id'];
        $email = trim($user['email']);
        $cpf = preg_replace('/\D/', '', $user['cpf'] ?? '');
        $name = trim($user['name']);
        
        echo "Processing User ID $uid ($email)...\n";

        // Update orders by Email (priority)
        if ($email) {
            $stmt = $db->prepare("UPDATE orders SET user_id = ? WHERE user_id IS NULL AND (customer_email = ? OR sender_name LIKE ?)");
            $stmt->execute([$uid, $email, "%$email%"]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                echo "  Linked $count orders by Email.\n";
                $totalRepaired += $count;
            }
        }
        
        // Update orders by CPF (normalized)
        if ($cpf) {
            $stmt = $db->prepare("UPDATE orders SET user_id = ? WHERE user_id IS NULL AND (
                REPLACE(REPLACE(REPLACE(customer_cpf, '.', ''), '-', ''), ' ', '') = ?
                OR REPLACE(REPLACE(REPLACE(sender_doc, '.', ''), '-', ''), ' ', '') = ?
            )");
            $stmt->execute([$uid, $cpf, $cpf]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                echo "  Linked $count orders by CPF.\n";
                $totalRepaired += $count;
            }
        }
        
        // Update orders by Name (Fuzzy)
        if ($name) {
            $stmt = $db->prepare("UPDATE orders SET user_id = ? WHERE user_id IS NULL AND (
                customer_name LIKE ? OR sender_name LIKE ?
            )");
            $stmt->execute([$uid, "%$name%", "%$name%"]);
            $count = $stmt->rowCount();
            if ($count > 0) {
                echo "  Linked $count orders by Name.\n";
                $totalRepaired += $count;
            }
        }
    }
    
    echo "\n--- SUCCESS ---\n";
    echo "Total orders linked: $totalRepaired\n";
    echo "Check your panel now!\n";

} catch (Exception $e) {
    echo "\nCRITICAL ERROR: " . $e->getMessage() . "\n";
}
