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

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    // list users (limit 200)
    $res = $mysqli->query("SELECT id, name, email, role, created_at, updated_at FROM users ORDER BY id DESC LIMIT 200");
    $users = [];
    while ($row = $res->fetch_assoc()) $users[] = $row;
    echo json_encode(['users'=>$users]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$action = $input['action'] ?? '';
if ($action === 'update_role') {
    $target = intval($input['user_id'] ?? 0);
    $role = $mysqli->real_escape_string($input['role'] ?? 'traveler');
    if (!$target) { http_response_code(422); echo json_encode(['error'=>'invalid_user']); exit; }
    $stmt = $mysqli->prepare("UPDATE users SET role = ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param('si', $role, $target);
    if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'db_update_failed','message'=>$stmt->error]); exit; }
        // insert audit log
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
            $det = json_encode(['role'=>$role]);
            $act = 'update_role';
            $tt = 'user';
            $aid = intval($userId);
            $ins->bind_param('issis', $aid, $act, $tt, $target, $det);
            $ins->execute();
            $ins->close();
        }
        echo json_encode(['ok'=>true]);
    exit;
}

if ($action === 'delete') {
    $target = intval($input['user_id'] ?? 0);
    if (!$target) { http_response_code(422); echo json_encode(['error'=>'invalid_user']); exit; }
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $target);
    if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'db_delete_failed','message'=>$stmt->error]); exit; }
        // audit delete
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
            $det = json_encode(['deleted_user'=>$target]);
            $act = 'delete_user';
            $tt = 'user';
            $aid = intval($userId);
            $ins->bind_param('issis', $aid, $act, $tt, $target, $det);
            $ins->execute();
            $ins->close();
        }
        echo json_encode(['ok'=>true]);
    exit;
}

    if ($action === 'create') {
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $role = $mysqli->real_escape_string($input['role'] ?? 'traveler');
        if (!$email || !$password) { http_response_code(422); echo json_encode(['error'=>'validation']); exit; }
        // check exists
        $emailEsc = $mysqli->real_escape_string($email);
        $r = $mysqli->query("SELECT id FROM users WHERE email = '$emailEsc' LIMIT 1");
        if ($r && $r->num_rows > 0) { http_response_code(409); echo json_encode(['error'=>'exists']); exit; }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $hashEsc = $mysqli->real_escape_string($hash);
        $nameEsc = $mysqli->real_escape_string($name ?: explode('@',$email)[0]);
        $ins = $mysqli->query("INSERT INTO users (name,email,password,role,created_at,updated_at) VALUES ('$nameEsc','$emailEsc','$hashEsc','$role',NOW(),NOW())");
        if (!$ins) { http_response_code(500); echo json_encode(['error'=>'db_insert_failed','message'=>$mysqli->error]); exit; }
        $newId = $mysqli->insert_id;
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
        $ins2 = $mysqli->prepare("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) VALUES (?,?,?,?,?,NOW())");
        if ($ins2) {
            $det = json_encode(['created_user'=>$newId,'email'=>$email]);
            $act = 'create_user';
            $tt = 'user';
            $aid = intval($userId);
            $detEsc = $det;
            $ins2->bind_param('issis', $aid, $act, $tt, $newId, $detEsc);
            $ins2->execute();
            $ins2->close();
        }
        echo json_encode(['ok'=>true,'user'=>['id'=>$newId,'email'=>$email,'name'=>$nameEsc,'role'=>$role]]);
        exit;
    }

http_response_code(400); echo json_encode(['error'=>'unknown_action']);
$mysqli->close();
