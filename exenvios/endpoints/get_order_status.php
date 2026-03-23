<?php
/**
 * Get Order Status Endpoint
 * Returns the current status of a specific order
 */

require_once '../config.php';

if (!isset($_GET['id'])) {
    jsonResponse(false, null, 'ID do pedido ausente');
}

try {
    $db = getDB();
    $orderId = (int)$_GET['id'];

    $stmt = $db->prepare("SELECT status FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch();

    if ($order) {
        jsonResponse(true, ['status' => $order['status']]);
    } else {
        jsonResponse(false, null, 'Pedido não encontrado');
    }

} catch (Exception $e) {
    error_log("Get order status error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao buscar status: ' . $e->getMessage());
}
