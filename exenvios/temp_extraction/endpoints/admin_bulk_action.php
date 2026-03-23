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
$action = $data['action'] ?? '';
$ids = $data['ids'] ?? [];
$type = $data['type'] ?? 'order';

if (empty($ids) || !is_array($ids)) {
    echo json_encode(['ok' => false, 'error' => 'Nenhum item selecionado.']);
    exit;
}

$db = getDB();

try {
    if ($action === 'delete') {
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        
        switch ($type) {
            case 'order':
                $stmt = $db->prepare("DELETE FROM orders WHERE id IN ($placeholders)");
                break;
            case 'user':
                // Check if admin is trying to delete self
                if (in_array($_SESSION['user_id'], $ids)) {
                    echo json_encode(['ok' => false, 'error' => 'Você não pode excluir sua própria conta em massa.']);
                    exit;
                }
                $stmt = $db->prepare("DELETE FROM users WHERE id IN ($placeholders)");
                break;
            default:
                echo json_encode(['ok' => false, 'error' => 'Tipo inválido para ação em massa.']);
                exit;
        }
        
        $stmt->execute($ids);
        echo json_encode(['ok' => true, 'message' => count($ids) . ' itens excluídos com sucesso.']);
    } else {
        echo json_encode(['ok' => false, 'error' => 'Ação inválida.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erro ao processar ação em massa: ' . $e->getMessage()]);
}
?>
