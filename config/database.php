<?php
/**
 * Database Configuration — Smart Farming IoT
 * PDO connection to MySQL with JSON response helpers
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'henaratc_db');
define('DB_USER', 'henaratc_user');
define('DB_PASS', 'SmartFarming123**');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO database connection
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            jsonResponse(['error' => 'Database connection failed: ' . $e->getMessage()], 500);
            exit;
        }
    }
    return $pdo;
}

/**
 * Send JSON response with HTTP status code
 */
function jsonResponse(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Get JSON body from PUT/POST requests
 */
function getJsonBody(): array {
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (stripos($contentType, 'application/json') !== false) {
        $body = json_decode(file_get_contents('php://input'), true);
        return $body ?: [];
    }
    // Fallback to form data
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        return $_POST;
    }
    // For PUT with form data
    parse_str(file_get_contents('php://input'), $data);
    return $data;
}

/**
 * Get request method (supports method override)
 */
function getMethod(): string {
    return strtoupper($_SERVER['REQUEST_METHOD']);
}
