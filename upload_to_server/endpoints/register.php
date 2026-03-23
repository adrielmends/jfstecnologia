<?php
/**
 * User Registration Endpoint
 */

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

$rawData = file_get_contents('php://input');
$userData = json_decode($rawData, true);

if (!$userData) {
    jsonResponse(false, null, 'Dados do cadastro ausentes');
}

$name = trim($userData['name'] ?? '');
$email = trim($userData['email'] ?? '');
$password = $userData['password'] ?? '';
$cpf = trim($userData['cpf'] ?? '');
$phone = trim($userData['phone'] ?? '');

if (empty($name) || empty($email) || empty($password)) {
    jsonResponse(false, null, 'Nome, Email e Senha são obrigatórios');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jsonResponse(false, null, 'Email inválido');
}

try {
    $db = getDB();

    // Check if user already exists
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(false, null, 'Este email já está cadastrado');
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $db->prepare("INSERT INTO users (name, email, password, cpf, phone, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->execute([$name, $email, $hashedPassword, $cpf, $phone]);

    $userId = $db->lastInsertId();

    // Start Session
    session_start();
    $_SESSION['user_id'] = $userId;
    $_SESSION['user_name'] = $name;
    $_SESSION['user_email'] = $email;

    jsonResponse(true, ['message' => 'Cadastro realizado com sucesso', 'userId' => $userId]);

} catch (Exception $e) {
    error_log("Registration error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao realizar cadastro: ' . $e->getMessage());
}
