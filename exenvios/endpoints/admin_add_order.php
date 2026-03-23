<?php
/**
 * Admin Add Order Endpoint
 * Manually adds an order from the Admin Panel
 */

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

// Check admin session (basic check)
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    jsonResponse(false, null, 'Acesso negado');
}

$rawData = file_get_contents('php://input');
$orderData = json_decode($rawData, true);

if (!$orderData || !isset($orderData['client_name'])) {
    jsonResponse(false, null, 'Dados do pedido inválidos');
}

try {
    $db = getDB();

    $stmt = $db->prepare("INSERT INTO orders (
        external_ref, 
        channel,
        customer_name, 
        service, 
        total_value, 
        status, 
        created_at
    ) VALUES (?, 'ADMIN', ?, ?, ?, 'paid', NOW())");

    $stmt->execute([
        $orderData['external_ref'],
        $orderData['client_name'],
        $orderData['service'],
        (float)$orderData['total_value']
    ]);

    jsonResponse(true, ['orderId' => $db->lastInsertId()], 'Encomenda cadastrada com sucesso');

} catch (Exception $e) {
    error_log("Admin add order error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao cadastrar encomenda: ' . $e->getMessage());
}
?>
