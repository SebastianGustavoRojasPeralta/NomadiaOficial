<?php
require_once __DIR__ . '/../../../vendor/autoload.php';

use App\Database\DB;

// Permitir múltiples puertos de localhost
$allowedOrigins = ['http://localhost:5173', 'http://localhost:5174'];
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: DELETE, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$userId = $_SESSION['user_id'];
$mysqli = DB::getConnection();

// Aceptar tanto DELETE como GET (con parámetro id)
$experienciaId = null;

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Leer del body
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $experienciaId = $data['id'] ?? null;
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Leer de query string
    $experienciaId = $_GET['id'] ?? null;
}

if (!$experienciaId) {
    http_response_code(400);
    echo json_encode(['error' => 'ID de experiencia requerido']);
    exit;
}

// Verificar que la experiencia pertenece al usuario
$stmt = $mysqli->prepare("SELECT id FROM experiencias WHERE id = ? AND guia_id = ?");
$stmt->bind_param('ii', $experienciaId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(403);
    echo json_encode(['error' => 'No tienes permiso para eliminar esta experiencia o no existe']);
    exit;
}

// Eliminar la experiencia (esto también eliminará registros relacionados si hay FK con ON DELETE CASCADE)
$stmt = $mysqli->prepare("DELETE FROM experiencias WHERE id = ? AND guia_id = ?");
$stmt->bind_param('ii', $experienciaId, $userId);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Experiencia eliminada exitosamente',
        'id' => $experienciaId
    ]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Error al eliminar la experiencia: ' . $mysqli->error]);
}

$stmt->close();
$mysqli->close();
