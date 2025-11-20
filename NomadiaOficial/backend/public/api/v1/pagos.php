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

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
    $reserva_id = intval($input['reserva_id'] ?? 0);
    $amount = floatval($input['amount'] ?? 0);
    $method = $mysqli->real_escape_string($input['method'] ?? 'mock');
    if (!$reserva_id || $amount <= 0) { http_response_code(422); echo json_encode(['error'=>'validation']); exit; }

    // check reservation ownership
    $rid = $mysqli->real_escape_string($reserva_id);
    $qr = $mysqli->query("SELECT * FROM reservas WHERE id = $rid LIMIT 1");
    if (!$qr || $qr->num_rows === 0) { http_response_code(404); echo json_encode(['error'=>'reserva_not_found']); exit; }
    $row = $qr->fetch_assoc();
    if ((int)$row['usuario_id'] !== (int)$userId) { http_response_code(403); echo json_encode(['error'=>'not_owner']); exit; }

    // simulate payment: insert pago with status 'completed', update reserva status to 'paid'
    $amt = $mysqli->real_escape_string($amount);
    $ins = $mysqli->query("INSERT INTO pagos (reserva_id, amount, method, status, created_at, updated_at) VALUES ($rid, '$amt', '$method', 'completed', NOW(), NOW())");
    if (!$ins) { http_response_code(500); echo json_encode(['error'=>'db_insert_failed']); exit; }
    $pid = $mysqli->insert_id;
    $mysqli->query("UPDATE reservas SET status='paid', updated_at=NOW() WHERE id = $rid");
    $qp = $mysqli->query("SELECT * FROM pagos WHERE id = $pid LIMIT 1");
    $p = $qp->fetch_assoc();
    // audit log (if table exists)
    $check = $mysqli->query("SHOW TABLES LIKE 'admin_audit_logs'");
    if ($check && $check->num_rows > 0) {
        $details = json_encode(['usuario_id'=>$userId, 'reserva_id'=>$reserva_id, 'amount'=>$amount, 'method'=>$method, 'status'=>'completed']);
        $detailsEsc = $mysqli->real_escape_string($details);
        $mysqli->query("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES ($userId, 'create_pago', 'pago', $pid, '$detailsEsc', NOW())");
    }
    echo json_encode(['pago'=>$p]);
    exit;
}

http_response_code(405); echo json_encode(['error'=>'method_not_allowed']);
