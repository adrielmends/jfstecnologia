<?php
/**
 * User Login Endpoint
 */

require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, null, 'Método não permitido');
}

$rawData = file_get_contents('php://input');
$loginData = json_decode($rawData, true);

if (!$loginData) {
    jsonResponse(false, null, 'Dados de login ausentes');
}

$email = trim($loginData['email'] ?? '');
$password = $loginData['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse(false, null, 'Email e Senha são obrigatórios');
}

try {
    $db = getDB();

    // Fetch user
    $stmt = $db->prepare("SELECT id, name, password, email, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Start Session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'] ?? 'user';

        jsonResponse(true, ['message' => 'Login realizado com sucesso', 'userName' => $user['name'], 'role' => $user['role'] ?? 'user']);
    } else {
        jsonResponse(false, null, 'Email ou senha incorretos');
    }

} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    jsonResponse(false, null, 'Erro ao realizar login: ' . $e->getMessage());
}
