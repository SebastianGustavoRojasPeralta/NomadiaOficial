<?php
header('Content-Type: application/json; charset=utf-8');

// CORS dev helper (allow localhost any port)
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

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    // list my reservations
    if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
    $userIdEsc = $mysqli->real_escape_string($userId);
    $res = $mysqli->query("SELECT r.*, e.title as experiencia_title FROM reservas r JOIN experiencias e ON e.id = r.experiencia_id WHERE r.usuario_id = '$userIdEsc' ORDER BY r.created_at DESC");
    $rows = [];
    while ($row = $res->fetch_assoc()) $rows[] = $row;
    echo json_encode($rows);
    exit;
}

// POST -> create reservation
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
if ($method === 'POST') {
    if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
    $experiencia_id = intval($input['experiencia_id'] ?? 0);
    $fecha_reserva = $mysqli->real_escape_string($input['fecha_reserva'] ?? date('Y-m-d H:i:s'));
    $cantidad = intval($input['cantidad'] ?? 1);
    if (!$experiencia_id || $cantidad <= 0) { http_response_code(422); echo json_encode(['error'=>'validation','message'=>'experiencia_id and cantidad required']); exit; }

    // get experiencia price
    $eid = $mysqli->real_escape_string($experiencia_id);
    $r = $mysqli->query("SELECT id,title,price FROM experiencias WHERE id = '$eid' LIMIT 1");
    if (!$r || $r->num_rows === 0) { http_response_code(404); echo json_encode(['error'=>'experiencia_not_found']); exit; }
    $exp = $r->fetch_assoc();
    $price = floatval($exp['price']);
    $total = round($price * $cantidad, 2);

    $u = $mysqli->real_escape_string($userId);
    $cid = $mysqli->real_escape_string($cantidad);
    $fr = $mysqli->real_escape_string($fecha_reserva);
    $status = $mysqli->real_escape_string($input['status'] ?? 'pending');
    $ins = $mysqli->query("INSERT INTO reservas (experiencia_id, usuario_id, fecha_reserva, cantidad, total, status, created_at, updated_at) VALUES ('$eid', '$u', '$fr', $cid, '$total', '$status', NOW(), NOW())");
    if (!$ins) { http_response_code(500); echo json_encode(['error'=>'db_insert_failed','message'=>$mysqli->error]); exit; }
    $id = $mysqli->insert_id;
    // audit log (if table exists)
    $check = $mysqli->query("SHOW TABLES LIKE 'admin_audit_logs'");
    if ($check && $check->num_rows > 0) {
        $details = json_encode(['usuario_id'=>$userId, 'experiencia_id'=>$experiencia_id, 'cantidad'=>$cantidad, 'total'=>$total]);
        $detailsEsc = $mysqli->real_escape_string($details);
        // Use user_id as admin_id since the user is performing the action
        $mysqli->query("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES ($userId, 'create_reserva', 'reserva', $id, '$detailsEsc', NOW())");
    }
    $res2 = $mysqli->query("SELECT r.*, e.title as experiencia_title FROM reservas r JOIN experiencias e ON e.id = r.experiencia_id WHERE r.id = $id LIMIT 1");
    $row = $res2->fetch_assoc();
    echo json_encode(['reserva'=>$row]);
    exit;
}

http_response_code(405);
echo json_encode(['error'=>'method_not_allowed']);
