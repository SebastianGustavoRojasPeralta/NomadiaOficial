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
header('Access-Control-Allow-Methods: POST, PUT, OPTIONS');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $experienciaId = $_POST['id'] ?? null;
    
    if (!$experienciaId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de experiencia requerido']);
        exit;
    }

    // Verificar que la experiencia pertenece al usuario (la columna es guia_id, no user_id)
    $stmt = $mysqli->prepare("SELECT id FROM experiencias WHERE id = ? AND guia_id = ?");
    $stmt->bind_param('ii', $experienciaId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(403);
        echo json_encode(['error' => 'No tienes permiso para editar esta experiencia']);
        exit;
    }

    // Detectar columnas
    $columnsResult = $mysqli->query("SHOW COLUMNS FROM experiencias");
    $columns = [];
    while ($row = $columnsResult->fetch_assoc()) {
        $columns[] = $row['Field'];
    }

    $hasTitle = in_array('title', $columns);
    $hasDescription = in_array('description', $columns);
    $hasPrice = in_array('price', $columns);
    $hasImagen = in_array('imagen', $columns);
    $hasDurationMinutes = in_array('duration_minutes', $columns);
    $hasCapacity = in_array('capacity', $columns);
    $hasQuantity = in_array('cantidad', $columns);
    $hasIdiomaPrincipal = in_array('idioma_principal', $columns);
    $hasIdiomasAdicionales = in_array('idiomas_adicionales', $columns);
    $hasImagenes = in_array('imagenes', $columns);

    // Campos básicos
    $title = $_POST['title'] ?? $_POST['titulo'] ?? null;
    $description = $_POST['description'] ?? $_POST['descripcion'] ?? null;
    $price = $_POST['price'] ?? $_POST['precio'] ?? null;
    $categoria = $_POST['categoria'] ?? null;
    $location = $_POST['location'] ?? null;
    $durationMinutes = $_POST['duration_minutes'] ?? $_POST['duracion'] ?? null;
    $capacity = $_POST['capacity'] ?? $_POST['cantidad'] ?? null;
    $idiomaPrincipal = $_POST['idioma_principal'] ?? null;
    $idiomasAdicionales = $_POST['idiomas_adicionales'] ?? null;

    // Preparar query de actualización
    $updates = [];
    $types = '';
    $values = [];

    if ($title !== null) {
        $col = $hasTitle ? 'title' : 'titulo';
        $updates[] = "$col = ?";
        $types .= 's';
        $values[] = $title;
    }

    if ($description !== null) {
        $col = $hasDescription ? 'description' : 'descripcion';
        $updates[] = "$col = ?";
        $types .= 's';
        $values[] = $description;
    }

    if ($price !== null) {
        $col = $hasPrice ? 'price' : 'precio';
        $updates[] = "$col = ?";
        $types .= 'd';
        $values[] = floatval($price);
    }

    if ($categoria !== null) {
        $updates[] = "categoria = ?";
        $types .= 's';
        $values[] = $categoria;
    }

    if ($location !== null) {
        $updates[] = "location = ?";
        $types .= 's';
        $values[] = $location;
    }

    if ($durationMinutes !== null && $hasDurationMinutes) {
        $updates[] = "duration_minutes = ?";
        $types .= 'i';
        $values[] = intval($durationMinutes);
    }

    if ($capacity !== null) {
        if ($hasCapacity) {
            $updates[] = "capacity = ?";
            $types .= 'i';
            $values[] = intval($capacity);
        } else if ($hasQuantity) {
            $updates[] = "cantidad = ?";
            $types .= 'i';
            $values[] = intval($capacity);
        }
    }

    // Manejar idiomas
    if ($idiomaPrincipal !== null && $hasIdiomaPrincipal) {
        $updates[] = "idioma_principal = ?";
        $types .= 's';
        $values[] = $idiomaPrincipal;
    }

    if ($idiomasAdicionales !== null && $hasIdiomasAdicionales) {
        $updates[] = "idiomas_adicionales = ?";
        $types .= 's';
        $values[] = $idiomasAdicionales;
    }

    // Manejar imagen principal si se subió una nueva
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../../../uploads/experiencias/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];
        $fileType = $_FILES['image']['type'];
        
        // Algunos navegadores no detectan AVIF correctamente, verificar por extensión también
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
        
        if (in_array($fileType, $allowedTypes) || in_array($ext, $allowedExtensions)) {
            $newName = uniqid('exp_') . '.' . $ext;
            $uploadPath = $uploadDir . $newName;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
                $col = $hasImagen ? 'imagen' : 'image';
                $updates[] = "$col = ?";
                $types .= 's';
                $values[] = '/uploads/experiencias/' . $newName;
            } else {
                error_log("Failed to move uploaded file: " . $_FILES['image']['tmp_name'] . " to " . $uploadPath);
            }
        } else {
            error_log("Invalid file type: $fileType, extension: $ext");
        }
    }

    // Manejar imágenes adicionales si se subieron
    if (!empty($_FILES)) {
        $uploadDir = __DIR__ . '/../../../uploads/experiencias/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $additionalImages = [];
        
        // Buscar archivos con patrón 'images[]'
        foreach ($_FILES as $key => $fileData) {
            if ($key === 'image') continue; // Saltar imagen principal
            
            if (is_array($fileData['name'])) {
                $fileCount = count($fileData['name']);
                for ($i = 0; $i < $fileCount; $i++) {
                    if ($fileData['error'][$i] === UPLOAD_ERR_OK) {
                        $ext = strtolower(pathinfo($fileData['name'][$i], PATHINFO_EXTENSION));
                        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
                        
                        if (in_array($ext, $allowedExtensions)) {
                            $newName = uniqid('exp_') . '_' . $i . '.' . $ext;
                            $uploadPath = $uploadDir . $newName;
                            
                            if (move_uploaded_file($fileData['tmp_name'][$i], $uploadPath)) {
                                $additionalImages[] = '/uploads/experiencias/' . $newName;
                            }
                        }
                    }
                }
            }
        }
        
        // Si se subieron imágenes adicionales, actualizar columna imagenes
        if (!empty($additionalImages) && $hasImagenes) {
            $imagenesJson = json_encode($additionalImages);
            $updates[] = "imagenes = ?";
            $types .= 's';
            $values[] = $imagenesJson;
        }
    }

    if (empty($updates)) {
        http_response_code(400);
        echo json_encode(['error' => 'No hay datos para actualizar']);
        exit;
    }

    // Agregar el ID al final
    $types .= 'i';
    $values[] = $experienciaId;

    $sql = "UPDATE experiencias SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param($types, ...$values);

    if ($stmt->execute()) {
        // Obtener la experiencia actualizada
        $stmtGet = $mysqli->prepare("SELECT * FROM experiencias WHERE id = ?");
        $stmtGet->bind_param('i', $experienciaId);
        $stmtGet->execute();
        $result = $stmtGet->get_result();
        $experiencia = $result->fetch_assoc();

        // Decodificar JSON de imágenes adicionales
        if (isset($experiencia['imagenes']) && !empty($experiencia['imagenes'])) {
            // Limpiar escapes literales de MySQL
            $cleanJson = str_replace(['\"', '\\/'], ['"', '/'], $experiencia['imagenes']);
            $decoded = json_decode($cleanJson, true);
            if (is_array($decoded)) {
                $experiencia['imagenes'] = $decoded;
            } else {
                $experiencia['imagenes'] = [];
            }
        }

        // Registrar en audit log (usar target_type y target_id, no entity_type y entity_id)
        $details = json_encode(['experiencia_id' => $experienciaId, 'campos_actualizados' => $updates]);
        $detailsEsc = $mysqli->real_escape_string($details);
        $mysqli->query("INSERT INTO admin_audit_logs (admin_id, action, target_type, target_id, details, created_at) 
                       VALUES ($userId, 'update_experiencia', 'experiencia', $experienciaId, '$detailsEsc', NOW())");

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Experiencia actualizada exitosamente',
            'experiencia' => $experiencia
        ]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al actualizar la experiencia: ' . $mysqli->error]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
}
