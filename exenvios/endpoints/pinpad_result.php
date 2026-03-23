<?php
/**
 * PIN Pad Result Endpoint
 * Receives result from physical PIN pad at the locker
 */

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

$rawData = file_get_contents('php://input');
$result = json_decode($rawData, true);

if (!$result || !isset($result['orderId']) || !isset($result['status'])) {
    jsonResponse(false, null, 'Dados incompletos');
}

try {
    $db = getDB();
    $orderId = (int)$result['orderId'];
    $status = $result['status']; // APPROVED, DECLINED, ERROR
    $payload = $result['rawPayload'] ?? null;

    if ($status === 'APPROVED') {
        // Update Payment
        $stmt = $db->prepare("UPDATE payments SET status = 'PAID', raw_payload = ? WHERE order_id = ? AND method = 'CARD_PINPAD' AND status = 'PENDING'");
        $stmt->execute([json_encode($payload), $orderId]);

        // Update Order
        $stmt = $db->prepare("UPDATE orders SET status = 'paid', confirmed_at = NOW() WHERE id = ?");
        $stmt->execute([$orderId]);

        jsonResponse(true, ['message' => 'Pagamento aprovado e registrado']);
    } else {
        // Update Payment
        $newStatus = ($status === 'DECLINED') ? 'DECLINED' : 'ERROR';
        $stmt = $db->prepare("UPDATE payments SET status = ?, raw_payload = ? WHERE order_id = ? AND method = 'CARD_PINPAD' AND status = 'PENDING'");
        $stmt->execute([$newStatus, json_encode($payload), $orderId]);

        // Mark order as failed if needed, or leave pending for retry
        if ($status === 'ERROR') {
            $db->prepare("UPDATE orders SET status = 'failed' WHERE id = ?")->execute([$orderId]);
        }

        jsonResponse(false, null, 'Pagamento não aprovado: ' . $status);
    }

} catch (Exception $e) {
    error_log("Pinpad result error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro interno ao processar resultado do PIN pad');
}
