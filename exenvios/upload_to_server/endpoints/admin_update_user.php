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
$userId = $data['user_id'] ?? null;
$name = $data['name'] ?? null;
$email = $data['email'] ?? null;
$phone = $data['phone'] ?? null;
$cpf = $data['cpf'] ?? null;
$balance = isset($data['balance']) ? (float)$data['balance'] : null;
$role = $data['role'] ?? null;
$password = $data['password'] ?? null;

if (!$userId) {
    echo json_encode(['ok' => false, 'error' => 'ID do usuário é obrigatório.']);
    exit;
}

if ($name !== null) {
    $nameParts = explode(' ', trim($name));
    if (count($nameParts) < 2) {
        echo json_encode(['ok' => false, 'error' => 'Por favor, informe Nome e Sobrenome.']);
        exit;
    }
}

try {
    $db = getDB();
    
    // Check if user exists
    $stmt = $db->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    if (!$stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Usuário não encontrado.']);
        exit;
    }

    $updates = [];
    $params = [];

    if ($name !== null) {
        $updates[] = "name = ?";
        $params[] = $name;
    }

    if ($email !== null) {
        // Verifica se no confita com outro usurio
        $stmtEmail = $db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmtEmail->execute([$email, $userId]);
        if ($stmtEmail->fetch()) {
            echo json_encode(['ok' => false, 'error' => 'Este email j est cadastrado em outra conta.']);
            exit;
        }
        $updates[] = "email = ?";
        $params[] = $email;
    }

    if ($phone !== null) {
        $updates[] = "phone = ?";
        $params[] = $phone;
    }

    if ($cpf !== null) {
        $updates[] = "cpf = ?";
        $params[] = $cpf;
    }

    if ($balance !== null) {
        $updates[] = "balance = ?";
        $params[] = $balance;
    }

    if ($role === 'user' || $role === 'admin') {
        $updates[] = "role = ?";
        $params[] = $role;
    }

    if (!empty($password)) {
        $updates[] = "password = ?";
        $params[] = password_hash($password, PASSWORD_DEFAULT);
    }

    if (empty($updates)) {
        echo json_encode(['ok' => true, 'message' => 'Nenhuma alteração enviada.']);
        exit;
    }

    $sql = "UPDATE users SET " . implode(", ", $updates) . " WHERE id = ?";
    $params[] = $userId;

    $stmt = $db->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['ok' => true, 'message' => 'Usuário atualizado com sucesso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
}
?>
