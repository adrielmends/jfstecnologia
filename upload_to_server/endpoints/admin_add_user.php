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
$name = $data['name'] ?? null;
$email = $data['email'] ?? null;
$phone = $data['phone'] ?? null;
$cpf = $data['cpf'] ?? null;
$balance = $data['balance'] ?? 0;
// Default to 123456 if empty or not provided
$password = (!empty($data['password'])) ? $data['password'] : '123456'; 

if (!$name || !$email || !$password) {
    echo json_encode(['ok' => false, 'error' => 'Nome, email e senha são obrigatórios.']);
    exit;
}

$nameParts = explode(' ', trim($name));
if (count($nameParts) < 2) {
    echo json_encode(['ok' => false, 'error' => 'Por favor, informe Nome e Sobrenome.']);
    exit;
}

try {
    $db = getDB();
    
    // Check if email exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['ok' => false, 'error' => 'Este email já está cadastrado.']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $db->prepare("INSERT INTO users (name, email, password, cpf, phone, balance) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $hashedPassword, $cpf, $phone, $balance]);

    echo json_encode(['ok' => true, 'id' => $db->lastInsertId()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'Erro ao criar usuário: ' . $e->getMessage()]);
}
?>
