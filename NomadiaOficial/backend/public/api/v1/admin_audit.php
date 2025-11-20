<?php
header('Content-Type: application/json; charset=utf-8');
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
if ($mysqli->connect_errno) { http_response_code(500); echo json_encode(['error'=>'db_connection_failed','message'=>$mysqli->connect_error]); exit; }

session_start();
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
// check admin
$stmt = $mysqli->prepare("SELECT role FROM users WHERE id = ? LIMIT 1"); $stmt->bind_param('i',$userId); $stmt->execute(); $r=$stmt->get_result(); $u=$r->fetch_assoc(); $stmt->close();
if (!($u && isset($u['role']) && strtolower($u['role'])==='admin')) { http_response_code(403); echo json_encode(['error'=>'forbidden']); exit; }

// list audits
$res = $mysqli->query("SELECT a.id,a.admin_id,a.action,a.target_type,a.target_id,a.details,a.created_at,u.name as admin_name FROM admin_audit_logs a LEFT JOIN users u ON u.id=a.admin_id ORDER BY a.created_at DESC LIMIT 200");
$rows = [];
while ($row = $res->fetch_assoc()) $rows[] = $row;

echo json_encode(['audits'=>$rows]);
$mysqli->close();
