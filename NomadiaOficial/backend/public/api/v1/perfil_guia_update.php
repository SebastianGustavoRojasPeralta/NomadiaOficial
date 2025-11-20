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

session_start();
$userId = $_SESSION['user_id'] ?? null;
if (!$userId) { 
    http_response_code(401); 
    echo json_encode(['error'=>'not_authenticated']); 
    exit; 
}

// Verificar que sea guía
$userCheck = $mysqli->query("SELECT role FROM users WHERE id = " . intval($userId));
$user = $userCheck->fetch_assoc();
if (!$user || $user['role'] !== 'guide') {
    http_response_code(403);
    echo json_encode(['error'=>'forbidden','message'=>'Solo los guías pueden editar su perfil']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];
    $types = '';

    // Campos de texto
    if (isset($_POST['name'])) {
        $updates[] = "name = ?";
        $params[] = $_POST['name'];
        $types .= 's';
    }
    if (isset($_POST['bio'])) {
        $updates[] = "bio = ?";
        $params[] = $_POST['bio'];
        $types .= 's';
    }
    if (isset($_POST['ubicacion'])) {
        $updates[] = "ubicacion = ?";
        $params[] = $_POST['ubicacion'];
        $types .= 's';
    }
    if (isset($_POST['idiomas_hablados'])) {
        $updates[] = "idiomas_hablados = ?";
        $params[] = $_POST['idiomas_hablados'];
        $types .= 's';
    }
    if (isset($_POST['certificaciones'])) {
        $updates[] = "certificaciones = ?";
        $params[] = $_POST['certificaciones'];
        $types .= 's';
    }
    if (isset($_POST['anos_experiencia'])) {
        $updates[] = "anos_experiencia = ?";
        $params[] = intval($_POST['anos_experiencia']);
        $types .= 'i';
    }

    // Manejo de foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../uploads/guias/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($ext, $allowedExts)) {
            http_response_code(400);
            echo json_encode(['error'=>'validation','message'=>'Formato de imagen no permitido']);
            exit;
        }

        $filename = 'guia_' . $userId . '_' . time() . '.' . $ext;
        $uploadPath = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
            $updates[] = "foto = ?";
            $params[] = '/uploads/guias/' . $filename;
            $types .= 's';
        }
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error'=>'validation','message'=>'No hay campos para actualizar']);
        exit;
    }

    $params[] = $userId;
    $types .= 'i';

    $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        // Obtener perfil actualizado
        $result = $mysqli->query("SELECT id, name, email, foto, bio, ubicacion, idiomas_hablados, certificaciones, anos_experiencia, total_tours, created_at FROM users WHERE id = " . intval($userId));
        $profile = $result->fetch_assoc();
        
        echo json_encode(['success'=>true, 'profile'=>$profile]);
    } else {
        http_response_code(500);
        echo json_encode(['error'=>'update_failed','message'=>$mysqli->error]);
    }
}

$mysqli->close();
