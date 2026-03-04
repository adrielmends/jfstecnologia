<?php
/**
 * Process Order Endpoint
 * Receives order data, saves to DB, and generates Asaas PIX
 */

require_once '../config.php';
require_once '../asaas-checkout.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

// 1. Receive and Decode JSON
$rawData = file_get_contents('php://input');
$orderData = json_decode($rawData, true);

if (!$orderData) {
    jsonResponse(false, null, 'Dados do pedido ausentes ou inválidos');
}

/**
 * Expected orderData fields:
 * - name, email, cpf, phone (Buyer info)
 * - necessity, packaging, packagingValue, service, value, tracking (Order details)
 * - total (Total amount)
 */

try {
    $db = getDB();

    // 2. Prepare/Get Asaas Customer
    $customerInfo = [
        'name' => $orderData['name'] ?? 'Cliente Totem',
        'email' => $orderData['email'] ?? 'vendas@exenvios.com.br',
        'cpfCnpj' => $orderData['cpf'] ?? '',
        'phone' => $orderData['phone'] ?? ''
    ];

    $customerId = getOrCreateAsaasCustomer($customerInfo);

    if (!$customerId) {
        jsonResponse(false, null, 'Erro ao sincronizar cliente com gateway de pagamento');
    }

    // 3. Generate PIX Payment via Asaas
    $payment = createAsaasPayment([
        'customerId' => $customerId,
        'billingType' => 'PIX',
        'value' => (float)$orderData['total'],
        'description' => 'Envio Ex-Envios: ' . ($orderData['service'] ?? 'Serviço'),
        'externalReference' => uniqid('ORD_')
    ]);

    if (!$payment['ok']) {
        jsonResponse(false, null, 'Erro ao gerar PIX: ' . $payment['error']);
    }

    // 4. Save Order to Database
    $stmt = $db->prepare("INSERT INTO orders (
        external_ref, 
        customer_name, 
        customer_cpf, 
        necessity, 
        packaging, 
        service, 
        total_value, 
        asaas_id, 
        status, 
        created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

    $stmt->execute([
        $payment['data']['externalReference'],
        $customerInfo['name'],
        $customerInfo['cpfCnpj'],
        $orderData['necessity'] ?? '',
        $orderData['packaging'] ?? '',
        $orderData['service'] ?? '',
        (float)$orderData['total'],
        $payment['data']['id']
    ]);

    $orderId = $db->lastInsertId();

    // 5. Success Response with Payment Details
    jsonResponse(true, [
        'orderId' => $orderId,
        'pix' => $payment['data']['pix'] ?? null,
        'paymentId' => $payment['data']['id']
    ]);

} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro fatal ao processar o pedido: ' . $e->getMessage());
}
?>
