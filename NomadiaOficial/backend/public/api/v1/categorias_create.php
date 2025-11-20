<?php
header('Content-Type: application/json; charset=utf-8');

// Basic CORS for dev
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin && preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Credentials: true');
}
header('Access-Control-Allow-Methods: POST, OPTIONS');
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

function slugify($text) {
    $text = preg_replace('~[^\pL0-9]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-a-zA-Z0-9]+~', '', $text);
    $text = trim($text, '-');
    $text = strtolower($text);
    if (empty($text)) return 'cat-' . bin2hex(random_bytes(4));
    return $text;
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
$nombre = trim($input['nombre'] ?? '');
$descripcion = trim($input['descripcion'] ?? '');
$estado = $input['estado'] ?? 'Activo';

if ($nombre === '') {
    http_response_code(422);
    echo json_encode(['error' => 'validation', 'message' => 'nombre is required']);
    exit;
}

$slug = slugify($nombre);

// ensure unique slug: append suffix if needed
$baseSlug = $slug;
$i = 1;
while (true) {
    $check = $mysqli->prepare('SELECT id FROM categorias WHERE slug = ? LIMIT 1');
    $check->bind_param('s', $slug);
    $check->execute();
    $res = $check->get_result();
    if ($res->num_rows === 0) break;
    $slug = $baseSlug . '-' . $i;
    $i++;
}

$ins = $mysqli->prepare('INSERT INTO categorias (nombre, slug, descripcion, estado, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
$ins->bind_param('ssss', $nombre, $slug, $descripcion, $estado);
if (!$ins->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'insert_failed', 'message' => $ins->error]);
    exit;
}

$newId = $mysqli->insert_id;
$resq = $mysqli->query('SELECT id, nombre, slug, descripcion, estado, created_at, updated_at FROM categorias WHERE id = ' . (int)$newId . ' LIMIT 1');
$created = $resq ? $resq->fetch_assoc() : null;

echo json_encode(['categoria' => $created]);

$mysqli->close();

?>
