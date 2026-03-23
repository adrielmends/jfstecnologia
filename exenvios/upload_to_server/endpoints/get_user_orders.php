<?php
/**
 * Get User Orders Endpoint
 * Returns orders for the logged-in user linked via CPF or name
 */

require_once '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    jsonResponse(false, null, 'Usuário não autenticado');
}

try {
    $db = getDB();
    
    // First, get the user's CPF and Name
    $stmt = $db->prepare("SELECT cpf, name, email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        jsonResponse(false, null, 'Usuário não encontrado no banco de dados');
    }
    
    $cpf = preg_replace('/\D/', '', $user['cpf'] ?? '');
    $name = trim($user['name'] ?? '');
    
    // Match logic:
    // 1. Exact CPF match (normalized)
    $email = trim($user['email'] ?? '');
    $nameParts = explode(' ', $name);
    $firstName = $nameParts[0];

    $ordersStmt = $db->prepare("
        SELECT * FROM orders 
        WHERE (user_id = ?)
           OR (customer_cpf IS NOT NULL AND customer_cpf != '' AND REPLACE(REPLACE(REPLACE(customer_cpf, '.', ''), '-', ''), ' ', '') = ?)
           OR (sender_doc IS NOT NULL AND sender_doc != '' AND REPLACE(REPLACE(REPLACE(sender_doc, '.', ''), '-', ''), ' ', '') = ?)
           OR (customer_name IS NOT NULL AND customer_name != '' AND (customer_name = ? OR customer_name LIKE ?))
           OR (sender_name IS NOT NULL AND sender_name != '' AND (sender_name = ? OR sender_name LIKE ?))
           OR (customer_email IS NOT NULL AND customer_email != '' AND customer_email = ?)
        ORDER BY created_at DESC 
        LIMIT 100
    ");
    
    $ordersStmt->execute([
        $_SESSION['user_id'],
        $cpf ?: '00000000000', 
        $cpf ?: '00000000000',
        $name, 
        ($name && strlen($firstName) > 2) ? "%{$firstName}%" : 'NOMEMUITOEXCLUSIVO123',
        $name,
        ($name && strlen($firstName) > 2) ? "%{$firstName}%" : 'NOMEMUITOEXCLUSIVO123',
        $email
    ]);
    
    $orders = $ordersStmt->fetchAll();
    
    jsonResponse(true, $orders);

} catch (Exception $e) {
    error_log("Get user orders error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao buscar encomendas do usuário: ' . $e->getMessage());
}
?>
