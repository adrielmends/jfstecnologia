<?php
session_start();
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acesso negado.']);
    exit;
}

require_once '../config.php';

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? null;

if (!$orderId) {
    echo json_encode(['ok' => false, 'error' => 'ID da encomenda é obrigatório.']);
    exit;
}

try {
    $db = getDB();
    
    // Check if order exists
    $stmt = $db->prepare("SELECT id FROM orders WHERE id = ?");
    $stmt->execute([$orderId]);
    if (!$stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Encomenda não encontrada.']);
        exit;
    }

    $allowedFields = [
        'status', 'service', 'total_value',
        'sender_name', 'sender_cep', 'sender_doc', 'sender_street', 'sender_number', 'sender_complement', 'sender_neighborhood', 'sender_city_uf',
        'receiver_name', 'receiver_cep', 'receiver_doc', 'receiver_street', 'receiver_number', 'receiver_complement', 'receiver_neighborhood', 'receiver_city_uf', 'receiver_contact'
    ];

    $updates = [];
    $params = [];

    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $updates[] = "$field = ?";
            $params[] = $data[$field];
        }
    }

    if (empty($updates)) {
        echo json_encode(['ok' => true, 'message' => 'Nenhuma alteração enviada.']);
        exit;
    }

    $sql = "UPDATE orders SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $orderId;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['ok' => true, 'message' => 'Encomenda atualizada com sucesso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar encomenda: ' . $e->getMessage()]);
}
?>
