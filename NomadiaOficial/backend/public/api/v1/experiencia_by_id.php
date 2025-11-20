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
header('Access-Control-Allow-Methods: GET, OPTIONS');
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

// Get experiencia by ID (only published for public access)
$id = intval($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    echo json_encode(['error'=>'validation','message'=>'ID required']);
    exit;
}

$stmt = $mysqli->prepare("SELECT e.*, u.name as guia_nombre, u.foto as guia_foto, u.ubicacion as guia_ubicacion, u.bio as guia_bio, u.idiomas_hablados as guia_idiomas, u.anos_experiencia as guia_anos_experiencia 
                          FROM experiencias e 
                          LEFT JOIN users u ON e.guia_id = u.id 
                          WHERE e.id = ? AND e.published = 1 LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) {
    http_response_code(404);
    echo json_encode(['error'=>'not_found','message'=>'Experiencia not found']);
    exit;
}

// Decodificar JSON de imágenes adicionales
if (isset($row['imagenes']) && !empty($row['imagenes'])) {
    // El problema: MySQL guarda el JSON con escapes literales: [\"\/..."]
    // Solución: Limpiar los escapes antes de decodificar
    $cleanJson = str_replace(['\"', '\/'], ['"', '/'], $row['imagenes']);
    $decoded = json_decode($cleanJson, true);
    
    if (is_array($decoded)) {
        $row['imagenes'] = $decoded;
    } else {
        $row['imagenes'] = [];
    }
}

echo json_encode($row);
$mysqli->close();
