<?php
header('Content-Type: application/json; charset=utf-8');

// Dev helper: create or promote a user to admin. Only intended for local development.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    if (!preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        http_response_code(403);
        echo json_encode(['error'=>'forbidden','message'=>'dev only']);
        exit;
    }
}

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

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$name = trim($input['name'] ?? 'Admin User');
$email = trim($input['email'] ?? 'admin@example.com');
$password = $input['password'] ?? 'admin123';

if (!$email || !$password) {
    http_response_code(422);
    echo json_encode(['error'=>'validation','message'=>'email and password required']);
    exit;
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error'=>'db_connection_failed','message'=>$mysqli->connect_error]);
    exit;
}

$emailEsc = $mysqli->real_escape_string($email);
$nameEsc = $mysqli->real_escape_string($name);

// check existing user
$res = $mysqli->query("SELECT id,email,name,role FROM users WHERE email = '$emailEsc' LIMIT 1");
if ($res && $res->num_rows > 0) {
    $u = $res->fetch_assoc();
    // promote to admin and optionally update password
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $hashEsc = $mysqli->real_escape_string($hash);
    $mysqli->query("UPDATE users SET role='admin', password='$hashEsc', name='$nameEsc', updated_at=NOW() WHERE id = " . intval($u['id']));
    $u['role'] = 'admin';
    $u['password'] = null;
    echo json_encode(['user'=>$u,'created'=>false,'email'=>$email,'password'=>$password]);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$hashEsc = $mysqli->real_escape_string($hash);
$sql = "INSERT INTO users (name,email,password,role,created_at,updated_at) VALUES ('$nameEsc','$emailEsc','$hashEsc','admin',NOW(),NOW())";
$ok = $mysqli->query($sql);
if (!$ok) {
    http_response_code(500);
    echo json_encode(['error'=>'db_insert_failed','message'=>$mysqli->error]);
    exit;
}
$id = $mysqli->insert_id;
echo json_encode(['user'=>['id'=>$id,'name'=>$name,'email'=>$email,'role'=>'admin'],'created'=>true,'email'=>$email,'password'=>$password]);
$mysqli->close();
