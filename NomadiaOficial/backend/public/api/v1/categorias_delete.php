<?php
header('Content-Type: application/json; charset=utf-8');

// Basic CORS for dev
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: POST, OPTIONS, DELETE');
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
    echo json_encode(['error' => 'db_connection_failed', 'message' => $mysqli->connect_error]);
    exit;
}

session_start();
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'unauthenticated']);
    exit;
}

// check admin role
$stmt = $mysqli->prepare('SELECT role FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $userId);
$stmt->execute();
$r = $stmt->get_result();
$row = $r->fetch_assoc();
if (!$row || strtolower($row['role'] ?? '') !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'forbidden']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$id = (int)($input['id'] ?? 0);
if ($id <= 0) {
    http_response_code(422);
    echo json_encode(['error' => 'validation', 'message' => 'id is required']);
    exit;
}

// Optional: check foreign keys (experiencias -> categoria or category) before deleting
$colCheck = $mysqli->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = 'experiencias' AND COLUMN_NAME IN ('categoria','category')");
$colCheck->bind_param('s', $dbName);
$colCheck->execute();
$colsRes = $colCheck->get_result();
$cols = [];
while ($c = $colsRes->fetch_assoc()) $cols[] = $c['COLUMN_NAME'];
foreach ($cols as $col) {
    $q = $mysqli->prepare("SELECT COUNT(*) as cnt FROM experiencias WHERE $col = ?");
    $q->bind_param('i', $id);
    $q->execute();
    $r2 = $q->get_result()->fetch_assoc();
    if (($r2['cnt'] ?? 0) > 0) {
        http_response_code(409);
        echo json_encode(['error' => 'conflict', 'message' => 'categoria in use by experiencias']);
        exit;
    }
}

$del = $mysqli->prepare('DELETE FROM categorias WHERE id = ? LIMIT 1');
$del->bind_param('i', $id);
if (!$del->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'delete_failed', 'message' => $del->error]);
    exit;
}

echo json_encode(['deleted' => (int)$id]);

$mysqli->close();

?>
