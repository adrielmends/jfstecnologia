<?php
/**
 * Fast Login for Local Development
 * Bypasses database check and sets admin session
 */
require_once 'config.php';

session_start();

// Só funciona em localhost
$isLocal = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']) || ($_SERVER['HTTP_HOST'] === 'localhost:8000');

if (!$isLocal) {
    die("Acesso negado. Este recurso só está disponível em ambiente local.");
}

// Inicializa o banco se necessário (SQLite)
getDB();

// Mock Admin Data
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Administrador Local';
$_SESSION['user_email'] = 'admin@exenvios.com';
$_SESSION['user_role'] = 'admin';

header('Location: /admin');
exit;
