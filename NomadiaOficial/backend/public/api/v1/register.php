<?php
header('Content-Type: application/json; charset=utf-8');
// CORS dev helper: allow localhost any port
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

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';
$role = $input['role'] ?? 'traveler';

if (!$name || !$email || !$password) {
    http_response_code(422);
    echo json_encode(['error' => 'validation', 'message' => 'name, email and password are required']);
    exit;
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'db_connection_failed', 'message' => $mysqli->connect_error]);
    exit;
}

$emailEsc = $mysqli->real_escape_string($email);
$check = $mysqli->query("SELECT id FROM users WHERE email = '$emailEsc' LIMIT 1");
if ($check && $check->num_rows > 0) {
    http_response_code(409);
    echo json_encode(['error' => 'exists', 'message' => 'Email already registered']);
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$nameEsc = $mysqli->real_escape_string($name);
$roleEsc = $mysqli->real_escape_string($role);

$now = date('Y-m-d H:i:s');
$insert = "INSERT INTO users (name,email,password,role,created_at,updated_at) VALUES ('{$nameEsc}','{$emailEsc}','{$hash}','{$roleEsc}','{$now}','{$now}')";
if (!$mysqli->query($insert)) {
    http_response_code(500);
    echo json_encode(['error' => 'insert_failed', 'message' => $mysqli->error]);
    exit;
}

$userId = $mysqli->insert_id;

// start session and set cookie
session_start();
$_SESSION['user_id'] = $userId;

$user = [
    'id' => $userId,
    'name' => $name,
    'email' => $email,
    'role' => $role,
];

echo json_encode(['user' => $user]);

$mysqli->close();
