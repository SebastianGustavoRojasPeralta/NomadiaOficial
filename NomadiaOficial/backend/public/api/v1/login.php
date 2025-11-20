<?php
header('Content-Type: application/json; charset=utf-8');

// CORS handling: allow requests from localhost/127.0.0.1 origins (dev).
// Accept any port on localhost to be robust when Vite picks a different port.
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    // Allow local development origins: http://localhost(:port) and http://127.0.0.1(:port)
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
    }
}
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// If this is a preflight request, exit early with 200
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
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

$email = trim($input['email'] ?? '');
$password = $input['password'] ?? '';

if (!$email || !$password) {
    http_response_code(422);
    echo json_encode(['error' => 'validation', 'message' => 'email and password are required']);
    exit;
}

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo json_encode(['error' => 'db_connection_failed', 'message' => $mysqli->connect_error]);
    exit;
}

$emailEsc = $mysqli->real_escape_string($email);
$res = $mysqli->query("SELECT id,name,email,password,role FROM users WHERE email = '$emailEsc' LIMIT 1");
if (!$res || $res->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'invalid_credentials']);
    exit;
}

$user = $res->fetch_assoc();
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'invalid_credentials']);
    exit;
}

$sessionStarted = false;
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    $sessionStarted = true;
}
$_SESSION['user_id'] = $user['id'];

unset($user['password']);

// Create a server-side session token and store it in the session
$token = bin2hex(random_bytes(16));
$_SESSION['token'] = $token;

// Set an HttpOnly cookie so browser sends it automatically on subsequent requests.
// For local dev `secure` must be false (no HTTPS); in production set `secure` => true.
setcookie('session_token', $token, [
    'expires' => time() + 60 * 60 * 24, // 1 day
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => false,
]);

// Return only the user object in JSON (no token in body)
echo json_encode(['user' => $user]);

$mysqli->close();
