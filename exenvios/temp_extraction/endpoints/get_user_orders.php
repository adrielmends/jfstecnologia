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
        WHERE (customer_cpf IS NOT NULL AND REPLACE(REPLACE(REPLACE(customer_cpf, '.', ''), '-', ''), ' ', '') = ?)
           OR (customer_name IS NOT NULL AND (customer_name = ? OR customer_name LIKE ?))
           OR (customer_email IS NOT NULL AND customer_email = ?)
        ORDER BY created_at DESC 
        LIMIT 100
    ");
    
    $ordersStmt->execute([
        $cpf ?: '00000000000', 
        $name, 
        $name ? "%{$firstName}%" : 'NOMEMUITOEXCLUSIVO123',
        $email
    ]);
    
    $orders = $ordersStmt->fetchAll();

    // If still empty and during debugging we want to see ALL orders to know why, we can temporarily return all 
    // but that's a security risk. It's better that we use the LIKE matching.
    
    jsonResponse(true, $orders);

} catch (Exception $e) {
    error_log("Get user orders error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao buscar encomendas do usuário: ' . $e->getMessage());
}
?>
