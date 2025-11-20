<?php
// Endpoint para obtener detalles completos de una reserva (reserva + experiencia + pago)
header('Content-Type: application/json; charset=utf-8');

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
    }
}
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../../app/Database/DB.php';
use App\Database\DB;

session_start();
$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    http_response_code(401);
    echo json_encode(['error' => 'not_authenticated']);
    exit;
}

$reservaId = $_GET['id'] ?? null;

if (!$reservaId) {
    http_response_code(400);
    echo json_encode(['error' => 'missing_reserva_id']);
    exit;
}

try {
    $mysqli = DB::getConnection();
    
    // Log para debugging
    error_log("reserva_detalles.php - User ID: $userId, Reserva ID: $reservaId");
    
    // Obtener la reserva con la experiencia
    $stmt = $mysqli->prepare("
        SELECT 
            r.id as reserva_id,
            r.experiencia_id,
            r.usuario_id,
            r.fecha_reserva,
            r.cantidad,
            r.total,
            r.status as reserva_status,
            r.created_at as reserva_created_at,
            r.updated_at as reserva_updated_at,
            e.title,
            e.description,
            e.price,
            e.location,
            e.duration_minutes,
            e.categoria,
            e.imagen,
            e.guia_id
        FROM reservas r
        INNER JOIN experiencias e ON r.experiencia_id = e.id
        WHERE r.id = ? AND r.usuario_id = ?
        LIMIT 1
    ");
    
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $mysqli->error);
    }
    
    $stmt->bind_param('ii', $reservaId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    error_log("reserva_detalles.php - Rows found: " . $result->num_rows);
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'reserva_not_found', 'debug' => "No se encontró reserva con ID $reservaId para usuario $userId"]);
        $stmt->close();
        $mysqli->close();
        exit;
    }
    
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // Estructurar datos de la reserva
    $reserva = [
        'id' => (int)$row['reserva_id'],
        'experiencia_id' => (int)$row['experiencia_id'],
        'usuario_id' => (int)$row['usuario_id'],
        'fecha_reserva' => $row['fecha_reserva'],
        'cantidad' => (int)$row['cantidad'],
        'total' => (float)$row['total'],
        'status' => $row['reserva_status'],
        'created_at' => $row['reserva_created_at'],
        'updated_at' => $row['reserva_updated_at']
    ];
    
    // Estructurar datos de la experiencia
    $experiencia = [
        'id' => (int)$row['experiencia_id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'price' => (float)$row['price'],
        'location' => $row['location'],
        'duration_minutes' => (int)$row['duration_minutes'],
        'categoria' => $row['categoria'],
        'imagen' => $row['imagen'],
        'guia_id' => (int)$row['guia_id']
    ];
    
    // Obtener información del pago si existe
    $stmt2 = $mysqli->prepare("
        SELECT 
            id,
            reserva_id,
            amount,
            method,
            status,
            created_at,
            updated_at
        FROM pagos
        WHERE reserva_id = ?
        ORDER BY created_at DESC
        LIMIT 1
    ");
    
    $stmt2->bind_param('i', $reservaId);
    $stmt2->execute();
    $result2 = $stmt2->get_result();
    
    $pago = null;
    if ($result2->num_rows > 0) {
        $pagoRow = $result2->fetch_assoc();
        $pago = [
            'id' => (int)$pagoRow['id'],
            'reserva_id' => (int)$pagoRow['reserva_id'],
            'amount' => (float)$pagoRow['amount'],
            'method' => $pagoRow['method'],
            'status' => $pagoRow['status'],
            'created_at' => $pagoRow['created_at'],
            'updated_at' => $pagoRow['updated_at']
        ];
    }
    
    $stmt2->close();
    $mysqli->close();
    
    // Respuesta exitosa
    echo json_encode([
        'reserva' => $reserva,
        'experiencia' => $experiencia,
        'pago' => $pago
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'database_error',
        'message' => $e->getMessage()
    ]);
}
