<?php
header('Content-Type: application/json; charset=utf-8');

// CORS
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
    }
}
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

function load_env($path) {
    $result = [];
    if (!file_exists($path)) return $result;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!strpos($line, '=')) continue;
        list($name, $value) = explode('=', $line, 2);
        $result[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
    }
    return $result;
}

$envPath = __DIR__ . '/../../../../.env';
$env = load_env($envPath);
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'nomadia';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error'=>'db_connection_failed']);
    exit;
}

session_start();
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) { 
    http_response_code(401); 
    echo json_encode(['error'=>'not_authenticated']); 
    exit; 
}

// Obtener perfil del guía autenticado
$stmt = $mysqli->prepare("SELECT id, name, email, role, foto, bio, ubicacion, idiomas_hablados, certificaciones, anos_experiencia, total_tours, created_at FROM users WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $userId);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

if (!$profile) {
    http_response_code(404);
    echo json_encode(['error'=>'not_found']);
    exit;
}

echo json_encode($profile);
$mysqli->close();
