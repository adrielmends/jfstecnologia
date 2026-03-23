<?php
/**
 * Process Order Endpoint
 * Receives order data, saves to DB, and routes payment
 */

require_once '../config.php';
require_once '../PaymentRouter.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

// 1. Receive and Decode JSON
$rawData = file_get_contents('php://input');
$orderData = json_decode($rawData, true);

if (!$orderData) {
    jsonResponse(false, null, 'Dados do pedido ausentes ou inválidos');
}

try {
    $db = getDB();
    
    // Auto-migrate: Add missing columns if they don't exist
    $cols = [
        'customer_email' => "VARCHAR(255) DEFAULT NULL",
        'modality'       => "VARCHAR(100) DEFAULT NULL",
        'weight'         => "VARCHAR(50) DEFAULT NULL",
        'sender_cep' => "VARCHAR(10) DEFAULT NULL",
        'sender_doc' => "VARCHAR(20) DEFAULT NULL",
        'sender_name' => "VARCHAR(255) DEFAULT NULL",
        'sender_street' => "VARCHAR(255) DEFAULT NULL",
        'sender_number' => "VARCHAR(50) DEFAULT NULL",
        'sender_complement' => "VARCHAR(255) DEFAULT NULL",
        'sender_neighborhood' => "VARCHAR(100) DEFAULT NULL",
        'sender_city_uf' => "VARCHAR(100) DEFAULT NULL",
        'receiver_cep' => "VARCHAR(10) DEFAULT NULL",
        'receiver_doc' => "VARCHAR(20) DEFAULT NULL",
        'receiver_name' => "VARCHAR(255) DEFAULT NULL",
        'receiver_contact' => "VARCHAR(255) DEFAULT NULL",
        'receiver_street' => "VARCHAR(255) DEFAULT NULL",
        'receiver_number' => "VARCHAR(50) DEFAULT NULL",
        'receiver_complement' => "VARCHAR(255) DEFAULT NULL",
        'receiver_neighborhood' => "VARCHAR(100) DEFAULT NULL",
        'receiver_city_uf' => "VARCHAR(100) DEFAULT NULL",
        'scheduled_date' => "DATE DEFAULT NULL",
        'scheduled_time' => "TIME DEFAULT NULL"
    ];

    foreach ($cols as $col => $def) {
        try {
            // Check if column exists
            $check = $db->query("SHOW COLUMNS FROM `orders` LIKE '$col'")->fetch();
            if (!$check) {
                $db->exec("ALTER TABLE `orders` ADD COLUMN `$col` $def");
            }
        } catch (Exception $e) { /* Ignore errors during migration */ }
    }

    $stmt = $db->prepare("INSERT INTO orders (
        external_ref, channel, locker_id, device_id,
        customer_name, customer_email, customer_cpf, modality, weight,
        necessity, packaging, service, 
        scheduled_date, scheduled_time, total_value,
        sender_cep, sender_doc, sender_name, sender_street, sender_number, 
        sender_complement, sender_neighborhood, sender_city_uf,
        receiver_cep, receiver_doc, receiver_name, receiver_contact, 
        receiver_street, receiver_number, receiver_complement, 
        receiver_neighborhood, receiver_city_uf,
        status, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

    $externalRef = uniqid('ORD_');
    $stmt->execute([
        $externalRef,
        $orderData['channel'] ?? 'WEB',
        $orderData['locker_id'] ?? null,
        $orderData['device_id'] ?? null,
        $orderData['name'] ?? 'Cliente Totem',
        $orderData['email'] ?? null,
        $orderData['cpf'] ?? '',
        $orderData['modality'] ?? null,
        $orderData['weight'] ?? null,
        $orderData['necessity'] ?? '',
        $orderData['packaging'] ?? '',
        $orderData['service'] ?? '',
        $orderData['scheduled_date'] ?? null,
        $orderData['scheduled_time'] ?? null,
        (float)$orderData['total'],
        // Sender
        $orderData['sender_cep'] ?? null,
        $orderData['sender_doc'] ?? null,
        $orderData['sender_name'] ?? null,
        $orderData['sender_street'] ?? null,
        $orderData['sender_number'] ?? null,
        $orderData['sender_complement'] ?? null,
        $orderData['sender_neighborhood'] ?? null,
        $orderData['sender_city_uf'] ?? null,
        // Receiver
        $orderData['receiver_cep'] ?? null,
        $orderData['receiver_doc'] ?? null,
        $orderData['receiver_name'] ?? null,
        $orderData['receiver_contact'] ?? null,
        $orderData['receiver_street'] ?? null,
        $orderData['receiver_number'] ?? null,
        $orderData['receiver_complement'] ?? null,
        $orderData['receiver_neighborhood'] ?? null,
        $orderData['receiver_city_uf'] ?? null
    ]);

    $orderId = $db->lastInsertId();

    // 3. Use Payment Router to initiate payment
    $router = new PaymentRouter($db);
    $paymentResult = $router->initiatePayment($orderId, $orderData);

    // 4. Success Response
    jsonResponse(true, [
        'orderId' => $orderId,
        'payment' => $paymentResult
    ]);

} catch (Exception $e) {
    error_log("Order processing error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro fatal ao processar o pedido: ' . $e->getMessage());
}
?>
