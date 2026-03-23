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
$userId = $data['userId'] ?? null;
$amount = $data['amount'] ?? null;
$type = $data['type'] ?? 'add'; // 'add' or 'set'

if (!$userId || $amount === null) {
    echo json_encode(['ok' => false, 'error' => 'Dados incompletos.']);
    exit;
}

try {
    $db = getDB();
    if ($type === 'add') {
        $stmt = $db->prepare("UPDATE users SET balance = balance + :amount WHERE id = :id");
    } else {
        $stmt = $db->prepare("UPDATE users SET balance = :amount WHERE id = :id");
    }
    
    $stmt->execute(['amount' => $amount, 'id' => $userId]);

    echo json_encode(['ok' => true]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar saldo: ' . $e->getMessage()]);
}
?>
