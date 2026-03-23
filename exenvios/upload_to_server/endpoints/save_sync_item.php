<?php
/**
 * Save Sync Item Endpoint
 * Saves or updates an item in locations, shipping_services, or lockers
 */

require_once '../config.php';

session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    jsonResponse(false, null, 'Acesso negado');
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['type']) || !isset($data['item'])) {
    jsonResponse(false, null, 'Parâmetros inválidos');
}

$type = $data['type'];
$item = $data['item'];

try {
    $db = getDB();
    
    if ($type === 'service') {
        // Safe-check/Add column size if not exists (Lazy migration)
        try { $db->exec("ALTER TABLE `shipping_services` ADD COLUMN `size` VARCHAR(100) DEFAULT NULL AFTER `name` "); } catch(Exception $e) {}

        $stmt = $db->prepare("INSERT INTO `shipping_services` (id, name, size, category, method, price, base_price, markup, description, status) 
                            VALUES (:id, :name, :size, :category, :method, :price, :base_price, :markup, :description, :status)
                            ON DUPLICATE KEY UPDATE 
                            name=VALUES(name), size=VALUES(size), category=VALUES(category), method=VALUES(method), price=VALUES(price), 
                            base_price=VALUES(base_price), markup=VALUES(markup), description=VALUES(description), status=VALUES(status)");
        $stmt->execute([
            'id' => $item['id'],
            'name' => $item['name'],
            'size' => $item['size'] ?? null,
            'category' => $item['cat'] ?? $item['category'],
            'method' => $item['method'],
            'price' => $item['price'] ?? 0,
            'base_price' => $item['base'] ?? 0,
            'markup' => $item['markup'] ?? 0,
            'description' => $item['description'] ?? '',
            'status' => $item['status'] ?? 'active'
        ]);
    } 
    elseif ($type === 'location') {
        $stmt = $db->prepare("INSERT INTO `locations` (id, name, address, sched_info, express_info, type, totens, occupancy, status) 
                            VALUES (:id, :name, :address, :sched_info, :express_info, :type, :totens, :occupancy, :status)
                            ON DUPLICATE KEY UPDATE 
                            name=VALUES(name), address=VALUES(address), sched_info=VALUES(sched_info), 
                            express_info=VALUES(express_info), type=VALUES(type), totens=VALUES(totens), 
                            occupancy=VALUES(occupancy), status=VALUES(status)");
        $stmt->execute([
            'id' => $item['id'],
            'name' => $item['name'],
            'address' => $item['address'],
            'sched_info' => $item['sched_info'] ?? $item['sched'] ?? '',
            'express_info' => $item['express_info'] ?? $item['express'] ?? '',
            'type' => $item['type'] ?? 'Unidade',
            'totens' => $item['totens'] ?? '01',
            'occupancy' => $item['occupancy'] ?? '5/10',
            'status' => $item['status'] ?? 'online'
        ]);
    }
    elseif ($type === 'locker') {
        $stmt = $db->prepare("INSERT INTO `lockers` (id, label, location_id, hardware_ref, type, status, capacity) 
                            VALUES (:id, :label, :location_id, :hardware_ref, :type, :status, :capacity)
                            ON DUPLICATE KEY UPDATE 
                            label=VALUES(label), location_id=VALUES(location_id), hardware_ref=VALUES(hardware_ref), 
                            type=VALUES(type), status=VALUES(status), capacity=VALUES(capacity)");
        $stmt->execute([
            'id' => $item['id'],
            'label' => $item['label'],
            'location_id' => $item['location_id'],
            'hardware_ref' => $item['hardware_ref'] ?? $item['hardware'] ?? '',
            'type' => $item['type'] ?? 'Hibrido',
            'status' => $item['status'] ?? 'online',
            'capacity' => $item['capacity'] ?? 10
        ]);
    }

    jsonResponse(true, 'Item salvo com sucesso');

} catch (Exception $e) {
    error_log("Save Sync Item Error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao salvar: ' . $e->getMessage());
}
