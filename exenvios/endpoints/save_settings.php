<?php
/**
 * Save Settings Endpoint
 * Saves global markup configurations to the database
 */

require_once '../config.php';

// Check if admin
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    jsonResponse(false, null, 'Acesso negado');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data || !isset($data['settings'])) {
    jsonResponse(false, null, 'Dados ausentes');
}

try {
    $db = getDB();
    
    // Auto-create table if missing
    $db->exec("CREATE TABLE IF NOT EXISTS `settings` (
        `key` VARCHAR(50) PRIMARY KEY,
        `value` TEXT,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    $stmt = $db->prepare("INSERT INTO `settings` (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
    
    foreach ($data['settings'] as $key => $value) {
        $stmt->execute([$key, $value]);
    }

    jsonResponse(true, 'Configurações salvas com sucesso');

} catch (Exception $e) {
    error_log("Save settings error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao salvar configurações: ' . $e->getMessage());
}
?>
