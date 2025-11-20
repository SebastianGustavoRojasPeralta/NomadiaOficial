<?php
header('Content-Type: application/json; charset=utf-8');
// CORS dev helper
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
    }
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
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
        $name = trim($name);
        $value = trim($value);
        $value = preg_replace('/(^["\']|["\']$)/', '', $value);
        $result[$name] = $value;
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
    echo json_encode(['error'=>'db_connection_failed','message'=>$mysqli->connect_error]);
    exit;
}

session_start();
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }

// Prevent admins from being converted to guide
$q = $mysqli->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
$q->bind_param('i', $userId);
$q->execute();
$r = $q->get_result();
$row = $r->fetch_assoc();
$q->close();
if ($row && isset($row['role']) && strtolower($row['role']) === 'admin') {
    http_response_code(403);
    echo json_encode(['error'=>'forbidden','message'=>'admins cannot be converted to guia']);
    exit;
}

// Update role to 'guia' (dev-only endpoint)
$role = 'guia';
$stmt = $mysqli->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
if (!$stmt) { http_response_code(500); echo json_encode(['error'=>'db_prepare_failed','message'=>$mysqli->error]); exit; }
$stmt->bind_param('si', $role, $userId);
if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'db_update_failed','message'=>$stmt->error]); exit; }
$stmt->close();

$q = $mysqli->prepare("SELECT id, name, email, role, created_at, updated_at FROM users WHERE id = ? LIMIT 1");
$q->bind_param('i', $userId);
$q->execute();
$res = $q->get_result();
$user = $res->fetch_assoc();
$q->close();

echo json_encode(['user'=>$user]);
$mysqli->close();
