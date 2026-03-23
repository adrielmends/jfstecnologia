<?php
/**
 * Global Configuration & Database Connection
 * Site: Ex-Envios
 */

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'exen_exenvios');
define('DB_USER', 'exen_Ex-Envios');
define('DB_PASS', '12sC5LjycvG4l*iH'); // <--- COLOQUE A SENHA QUE VOCÊ CRIOU NO CYBERPANEL AQUI
define('GEMINI_API_KEY', 'AIzaSyA97ClzuIQ0S8TW_SDqrTaA7ezZnk2Tg2c');

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
            throw new Exception("Connection failed: " . $e->getMessage());
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
