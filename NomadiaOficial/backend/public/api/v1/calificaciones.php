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

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'GET') {
    // list ratings for experiencia_id
    $experiencia_id = intval($_GET['experiencia_id'] ?? 0);
    if (!$experiencia_id) { http_response_code(422); echo json_encode(['error'=>'validation']); exit; }
    $eid = $mysqli->real_escape_string($experiencia_id);
    $q = $mysqli->query("SELECT c.*, u.name as usuario_name FROM calificaciones c JOIN users u ON u.id = c.usuario_id WHERE c.experiencia_id = $eid ORDER BY c.created_at DESC");
    $rows = [];
    while ($row = $q->fetch_assoc()) $rows[] = $row;
    echo json_encode($rows);
    exit;
}

// POST -> create rating
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
if ($method === 'POST') {
    if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }
    $experiencia_id = intval($input['experiencia_id'] ?? 0);
    $rating = intval($input['rating'] ?? 0);
    $comentario = $mysqli->real_escape_string($input['comentario'] ?? '');
    if (!$experiencia_id || $rating <= 0 || $rating > 5) { http_response_code(422); echo json_encode(['error'=>'validation']); exit; }

    $eid = $mysqli->real_escape_string($experiencia_id);
    $uid = $mysqli->real_escape_string($userId);
    $ins = $mysqli->query("INSERT INTO calificaciones (experiencia_id, usuario_id, rating, comentario, created_at, updated_at) VALUES ($eid, $uid, $rating, '$comentario', NOW(), NOW())");
    if (!$ins) { http_response_code(500); echo json_encode(['error'=>'db_insert_failed','message'=>$mysqli->error]); exit; }
    $id = $mysqli->insert_id;
    $q = $mysqli->query("SELECT c.*, u.name as usuario_name FROM calificaciones c JOIN users u ON u.id = c.usuario_id WHERE c.id = $id LIMIT 1");
    $row = $q->fetch_assoc();
    echo json_encode(['calificacion'=>$row]);
    exit;
}

http_response_code(405); echo json_encode(['error'=>'method_not_allowed']);

