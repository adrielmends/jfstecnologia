<?php
/**
 * Get Orders Endpoint
 * Returns all orders from DB for Admin display
 */

require_once '../config.php';

try {
    $db = getDB();
    
    // Fetch last 50 orders with linked user info
    $stmt = $db->query("
        SELECT o.*, u.name as user_linked_name 
        FROM orders o 
        LEFT JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC 
        LIMIT 100
    ");
    $orders = $stmt->fetchAll();

    jsonResponse(true, $orders);

} catch (Exception $e) {
    error_log("Get orders error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao buscar encomendas: ' . $e->getMessage());
}
?>
