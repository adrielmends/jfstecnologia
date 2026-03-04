<?php
/**
 * Get Orders Endpoint
 * Returns all orders from DB for Admin display
 */

require_once '../config.php';

try {
    $db = getDB();
    
    // Fetch last 50 orders
    $stmt = $db->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 50");
    $orders = $stmt->fetchAll();

    jsonResponse(true, $orders);

} catch (Exception $e) {
    error_log("Get orders error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao buscar encomendas: ' . $e->getMessage());
}
?>
