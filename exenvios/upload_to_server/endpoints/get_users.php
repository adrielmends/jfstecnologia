<?php
session_start();
header('Content-Type: application/json');

// Security check: Only admins can access this (simplistic check for now)
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'Acesso negado.']);
    exit;
}

require_once '../config.php';

try {
    $db = getDB();
    $stmt = $db->query("SELECT id, name, email, cpf, phone, balance, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['ok' => true, 'users' => $users]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
?>
