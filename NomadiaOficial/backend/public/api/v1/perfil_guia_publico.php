<?php
header('Content-Type: application/json; charset=utf-8');

// CORS
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
        $result[trim($name)] = trim($value, " \t\n\r\0\x0B\"'");
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
    echo json_encode(['error'=>'db_connection_failed']);
    exit;
}

// Obtener perfil público del guía por ID
$guiaId = intval($_GET['id'] ?? 0);
if (!$guiaId) {
    http_response_code(400);
    echo json_encode(['error'=>'validation','message'=>'ID de guía requerido']);
    exit;
}

// Obtener datos del guía
$stmt = $mysqli->prepare("SELECT id, name, email, foto, bio, ubicacion, idiomas_hablados, certificaciones, anos_experiencia, total_tours, created_at FROM users WHERE id = ? AND role = 'guide' LIMIT 1");
$stmt->bind_param('i', $guiaId);
$stmt->execute();
$result = $stmt->get_result();
$guia = $result->fetch_assoc();

if (!$guia) {
    http_response_code(404);
    echo json_encode(['error'=>'not_found','message'=>'Guía no encontrado']);
    exit;
}

// Obtener experiencias del guía (solo las publicadas)
$expResult = $mysqli->query("SELECT id, title, description, price, categoria, imagen, duration_minutes, cantidad, location, published FROM experiencias WHERE guia_id = " . intval($guiaId) . " AND published = 1 ORDER BY created_at DESC");

$experiencias = [];
while ($exp = $expResult->fetch_assoc()) {
    $experiencias[] = $exp;
}

// Calcular calificación promedio del guía basada en sus experiencias
$ratingQuery = "
    SELECT AVG(c.rating) as promedio, COUNT(c.id) as total_reviews 
    FROM calificaciones c 
    INNER JOIN experiencias e ON c.experiencia_id = e.id 
    WHERE e.guia_id = " . intval($guiaId);
$ratingResult = $mysqli->query($ratingQuery);
$rating = $ratingResult->fetch_assoc();

$guia['rating_promedio'] = $rating['promedio'] ? round($rating['promedio'], 1) : null;
$guia['total_reviews'] = $rating['total_reviews'] ?? 0;
$guia['experiencias'] = $experiencias;

echo json_encode($guia);
$mysqli->close();
