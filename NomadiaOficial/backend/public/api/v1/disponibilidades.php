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
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE');
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
    // list disponibilidades by experiencia_id (query param) or by guia's experiencias
    $experiencia_id = intval($_GET['experiencia_id'] ?? 0);
    if ($experiencia_id) {
        $eid = $mysqli->real_escape_string($experiencia_id);
        $q = $mysqli->query("SELECT * FROM disponibilidades WHERE experiencia_id = $eid ORDER BY fecha ASC");
        $rows = [];
        while ($r = $q->fetch_assoc()) $rows[] = $r;
        echo json_encode($rows);
        exit;
    }
    // if no experiencia_id, and user is guide, list all disponibilidades for their experiencias
    if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
    $q = $mysqli->query("SELECT d.* FROM disponibilidades d JOIN experiencias e ON e.id = d.experiencia_id WHERE e.guia_id = " . intval($userId) . " ORDER BY d.fecha ASC");
    $rows = [];
    while ($r = $q->fetch_assoc()) $rows[] = $r;
    echo json_encode($rows);
    exit;
}

// POST -> create disponibilidad
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
if ($method === 'POST') {
    if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
    $experiencia_id = intval($input['experiencia_id'] ?? 0);
    $fecha = $mysqli->real_escape_string($input['fecha'] ?? '');
    $cupos = intval($input['cupos'] ?? 1);
    if (!$experiencia_id || !$fecha) { http_response_code(422); echo json_encode(['error'=>'validation']); exit; }
    // verify ownership
    $eid = $mysqli->real_escape_string($experiencia_id);
    $qr = $mysqli->query("SELECT guia_id FROM experiencias WHERE id = $eid LIMIT 1");
    if (!$qr || $qr->num_rows === 0) { http_response_code(404); echo json_encode(['error'=>'experiencia_not_found']); exit; }
    $exp = $qr->fetch_assoc();
    if ((int)$exp['guia_id'] !== (int)$userId) { http_response_code(403); echo json_encode(['error'=>'not_owner']); exit; }

    $f = $mysqli->real_escape_string($fecha);
    $c = $mysqli->real_escape_string($cupos);
    $ins = $mysqli->query("INSERT INTO disponibilidades (experiencia_id, fecha, cupos, created_at, updated_at) VALUES ($eid, '$f', $c, NOW(), NOW())");
    if (!$ins) { http_response_code(500); echo json_encode(['error'=>'db_insert_failed','message'=>$mysqli->error]); exit; }
    $id = $mysqli->insert_id;
    $q2 = $mysqli->query("SELECT * FROM disponibilidades WHERE id = $id LIMIT 1");
    $row = $q2->fetch_assoc();
    // audit log (if table exists)
    $check = $mysqli->query("SHOW TABLES LIKE 'admin_audit_logs'");
    if ($check && $check->num_rows > 0) {
        $details = json_encode(['guia_id'=>$userId, 'experiencia_id'=>$experiencia_id, 'fecha'=>$fecha, 'cupos'=>$cupos]);
        $detailsEsc = $mysqli->real_escape_string($details);
        $mysqli->query("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES (NULL, 'create_disponibilidad', 'disponibilidad', $id, '$detailsEsc', NOW())");
    }
    echo json_encode(['disponibilidad'=>$row]);
    exit;
}

// DELETE via query param ?id= or POST with action=delete
if ($method === 'DELETE' || ($method === 'POST' && ($input['action'] ?? '') === 'delete')) {
    // support both DELETE and POST-delete
    $id = intval($_GET['id'] ?? ($input['id'] ?? 0));
    if (!$id) { http_response_code(422); echo json_encode(['error'=>'validation']); exit; }
    // verify ownership via join
    $qv = $mysqli->query("SELECT d.* FROM disponibilidades d JOIN experiencias e ON e.id = d.experiencia_id WHERE d.id = $id LIMIT 1");
    if (!$qv || $qv->num_rows === 0) { http_response_code(404); echo json_encode(['error'=>'not_found']); exit; }
    $row = $qv->fetch_assoc();
    $expq = $mysqli->query("SELECT guia_id FROM experiencias WHERE id = " . intval($row['experiencia_id']) . " LIMIT 1");
    $exp = $expq->fetch_assoc();
    if ((int)$exp['guia_id'] !== (int)$userId) { http_response_code(403); echo json_encode(['error'=>'not_owner']); exit; }
    $del = $mysqli->query("DELETE FROM disponibilidades WHERE id = $id");
    if (!$del) { http_response_code(500); echo json_encode(['error'=>'delete_failed']); exit; }
    // audit delete (if table exists)
    $check2 = $mysqli->query("SHOW TABLES LIKE 'admin_audit_logs'");
    if ($check2 && $check2->num_rows > 0) {
        $details = json_encode(['guia_id'=>$userId, 'experiencia_id'=>$row['experiencia_id'], 'fecha'=>$row['fecha'], 'cupos'=>$row['cupos']]);
        $detailsEsc = $mysqli->real_escape_string($details);
        $mysqli->query("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES (NULL, 'delete_disponibilidad', 'disponibilidad', $id, '$detailsEsc', NOW())");
    }
    echo json_encode(['deleted'=>true]);
    exit;
}

http_response_code(405); echo json_encode(['error'=>'method_not_allowed']);


