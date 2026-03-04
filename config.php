<?php
/**
 * Global Configuration & Database Connection
 * Site: Ex-Envios
 */

// Database Credentials (Update these with your actual server data)
define('DB_HOST', 'localhost');
define('DB_NAME', 'exenvios_db');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * Get Database Connection
 */
function getDB() {
    static $db = null;
    if ($db === null) {
        try {
            $db = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (PDOException $e) {
            error_log("Connection failed: " . $e->getMessage());
            die("Algo deu errado na conexão com o banco de dados.");
        }
    }
    return $db;
}

// Global Response Helper
function jsonResponse($ok, $data = null, $error = null) {
    header('Content-Type: application/json');
    echo json_encode([
        'ok' => $ok,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}
?>
