<?php
/**
 * Get Settings Endpoint
 * Fetches global settings from the database
 */

require_once '../config.php';

// Check if admin
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    jsonResponse(false, null, 'Acesso negado');
}

try {
    $db = getDB();
    
    // Auto-create table if missing (to avoid errors)
    $db->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `key` VARCHAR(50) PRIMARY KEY,
        `value` TEXT,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $db->query("SELECT `key`, `value` FROM `settings`");
    $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Default values if empty
    if (empty($settings)) {
        $settings = [
            'markup_correios' => '30',
            'markup_transporte' => '25',
            'markup_coleta' => '15'
        ];
    }

    jsonResponse(true, $settings);

} catch (Exception $e) {
    error_log("Get settings error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao buscar configurações');
}
?>
