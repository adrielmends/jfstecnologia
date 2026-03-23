<?php
require_once 'config.php';
try {
    $db = getDB();
    $cols = [
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

    foreach ($cols as $col => $type) {
        try {
            $db->exec("ALTER TABLE orders ADD COLUMN $col $type DEFAULT NULL");
            echo "Column $col added successfully.<br>";
        } catch (Exception $e) {
            echo "Column $col already exists or error: " . $e->getMessage() . "<br>";
        }
    }
    echo "Migration completed.";
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage();
}
