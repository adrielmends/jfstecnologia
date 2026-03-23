<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['ok' => false, 'error' => 'Acesso negado.']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$type = $data['type'] ?? '';
$id = $data['id'] ?? '';

if (!$type || !$id) {
    echo json_encode(['ok' => false, 'error' => 'Parâmetros inválidos.']);
    exit;
}

$db = getDB();

try {
    switch ($type) {
        case 'order':
            $stmt = $db->prepare("DELETE FROM orders WHERE id = ?");
            break;
        case 'location':
            // Check for dependent lockers
            $check = $db->prepare("SELECT COUNT(*) FROM lockers WHERE location_id = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                echo json_encode(['ok' => false, 'error' => 'Não é possível excluir: existem totens vinculados a esta unidade.']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM locations WHERE id = ?");
            break;
        case 'locker':
            $stmt = $db->prepare("DELETE FROM lockers WHERE id = ?");
            break;
        case 'user':
            // Prevent self-deletion
            if ($id == $_SESSION['user_id']) {
                echo json_encode(['ok' => false, 'error' => 'Você não pode excluir sua própria conta.']);
                exit;
            }
            $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
            break;
        case 'service':
            $stmt = $db->prepare("DELETE FROM shipping_services WHERE id = ?");
            break;
        default:
            echo json_encode(['ok' => false, 'error' => 'Tipo inválido.']);
            exit;
    }

    $stmt->execute([$id]);
    echo json_encode(['ok' => true]);

} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'error' => 'Erro no banco de dados: ' . $e->getMessage()]);
}
