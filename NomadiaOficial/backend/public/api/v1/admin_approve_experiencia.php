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

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? '';
if ($action === 'list') {
    $res = $mysqli->query("
        SELECT 
            e.id, 
            e.title, 
            e.description, 
            e.price, 
            e.guia_id, 
            e.published, 
            e.created_at, 
            e.updated_at,
            e.cantidad,
            u.name as guia_name,
            u.email as guia_email
        FROM experiencias e
        LEFT JOIN users u ON e.guia_id = u.id
        ORDER BY 
            CASE WHEN e.published = 0 THEN 0 ELSE 1 END,
            e.created_at DESC 
        LIMIT 200
    ");
    $rows = [];
    while ($row = $res->fetch_assoc()) {
        // Convert published to integer for clarity
        $row['published'] = intval($row['published']);
        $rows[] = $row;
    }
    echo json_encode(['experiencias'=>$rows]);
    exit;
}

if ($action === 'approve') {
    $id = intval($input['experiencia_id'] ?? 0);
    if (!$id) { http_response_code(422); echo json_encode(['error'=>'invalid_id']); exit; }
    $stmt = $mysqli->prepare("UPDATE experiencias SET published = 1, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'db_update_failed','message'=>$stmt->error]); exit; }
        // audit
        $ai = $mysqli->prepare("CREATE TABLE IF NOT EXISTS admin_audit_logs (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            admin_id BIGINT UNSIGNED NOT NULL,
            action VARCHAR(100) NOT NULL,
            target_type VARCHAR(100) NULL,
            target_id BIGINT NULL,
            details TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        if ($ai) { $ai->execute(); $ai->close(); }
        $ins = $mysqli->prepare("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES (?,?,?,?,?,NOW())");
        if ($ins) {
            $det = json_encode(['approved_experiencia'=>$id]);
            $act = 'approve_experiencia';
            $tt = 'experiencia';
            $aid = intval($userId);
            $ins->bind_param('issis', $aid, $act, $tt, $id, $det);
            $ins->execute();
            $ins->close();
        }
        echo json_encode(['ok'=>true]);
    exit;
}

if ($action === 'reject') {
    $id = intval($input['experiencia_id'] ?? 0);
    if (!$id) { http_response_code(422); echo json_encode(['error'=>'invalid_id']); exit; }
    // Instead of deleting, mark as rejected (published = -1)
    $stmt = $mysqli->prepare("UPDATE experiencias SET published = -1, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('i', $id);
    if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'db_update_failed','message'=>$stmt->error]); exit; }
        // audit rejection
        $ai = $mysqli->prepare("CREATE TABLE IF NOT EXISTS admin_audit_logs (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            admin_id BIGINT UNSIGNED NOT NULL,
            action VARCHAR(100) NOT NULL,
            target_type VARCHAR(100) NULL,
            target_id BIGINT NULL,
            details TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        if ($ai) { $ai->execute(); $ai->close(); }
        $ins = $mysqli->prepare("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES (?,?,?,?,?,NOW())");
        if ($ins) {
            $det = json_encode(['rejected_experiencia'=>$id]);
            $act = 'reject_experiencia';
            $tt = 'experiencia';
            $aid = intval($userId);
            $ins->bind_param('issis', $aid, $act, $tt, $id, $det);
            $ins->execute();
            $ins->close();
        }
        echo json_encode(['ok'=>true]);
    exit;
}

http_response_code(400); echo json_encode(['error'=>'unknown_action']);
$mysqli->close();
