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
header('Access-Control-Allow-Methods: GET, OPTIONS');
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

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener todas las reservas de las experiencias del guía
    // IMPORTANTE: experiencias solo tiene 'title' (no titulo), usa usuario_id (no user_id)
    // IMPORTANTE: pagos usa 'method' (no payment_method)
    $sql = "SELECT 
                r.id as reserva_id,
                r.usuario_id as cliente_id,
                r.experiencia_id,
                r.fecha_reserva,
                r.cantidad as num_personas,
                r.total,
                r.status,
                r.created_at,
                e.title as experiencia_titulo,
                e.price as experiencia_precio,
                e.imagen,
                e.categoria,
                u.name as cliente_nombre,
                u.email as cliente_email,
                p.id as pago_id,
                p.amount as pago_monto,
                p.method as pago_metodo,
                p.status as pago_status,
                p.created_at as pago_fecha
            FROM reservas r
            INNER JOIN experiencias e ON r.experiencia_id = e.id
            LEFT JOIN users u ON r.usuario_id = u.id
            LEFT JOIN pagos p ON r.id = p.reserva_id
            WHERE e.guia_id = ?
            ORDER BY r.created_at DESC";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Debug log
    error_log("Reservas Guia - User ID: $userId, Rows found: " . $result->num_rows);
    
    $reservas = [];
    while ($row = $result->fetch_assoc()) {
        $reservas[] = [
            'id' => $row['reserva_id'],
            'cliente_id' => $row['cliente_id'],
            'cliente_nombre' => $row['cliente_nombre'],
            'cliente_email' => $row['cliente_email'],
            'experiencia_id' => $row['experiencia_id'],
            'experiencia_titulo' => $row['experiencia_titulo'],
            'experiencia_precio' => $row['experiencia_precio'],
            'experiencia_imagen' => $row['imagen'],
            'experiencia_categoria' => $row['categoria'],
            'fecha_experiencia' => $row['fecha_reserva'],
            'num_personas' => $row['num_personas'],
            'total' => $row['total'],
            'status' => $row['status'],
            'created_at' => $row['created_at'],
            'pago' => $row['pago_id'] ? [
                'id' => $row['pago_id'],
                'monto' => $row['pago_monto'],
                'metodo' => $row['pago_metodo'],
                'status' => $row['pago_status'],
                'fecha' => $row['pago_fecha']
            ] : null
        ];
    }
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'reservas' => $reservas,
        'total' => count($reservas)
    ]);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
