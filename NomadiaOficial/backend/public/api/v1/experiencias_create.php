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

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); echo json_encode(['error'=>'method_not_allowed']); exit;
}

// Support both JSON body and multipart/form-data with file upload
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

// Handle primary image upload
$uploadedImagePath = null;
if (!empty($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . '/../../uploads/experiencias';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
    $f = $_FILES['image'];
    $ext = pathinfo($f['name'], PATHINFO_EXTENSION);
    $safe = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($f['name'], PATHINFO_FILENAME));
    $filename = $safe . '-' . time() . '.' . $ext;
    $dest = $uploadDir . '/' . $filename;
    if (move_uploaded_file($f['tmp_name'], $dest)) {
        $uploadedImagePath = '/uploads/experiencias/' . $filename;
    }
}

// Handle multiple additional images
$additionalImages = [];
// PHP recibe 'images[]' del frontend como un array en $_FILES
if (!empty($_FILES)) {
    $uploadDir = __DIR__ . '/../../uploads/experiencias';
    if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
    
    // Debug: Log de todos los archivos recibidos
    error_log("=== FILES RECIBIDOS ===");
    error_log(print_r(array_keys($_FILES), true));
    
    // Buscar archivos con patrón 'images_*' o key que contenga 'images'
    foreach ($_FILES as $key => $fileData) {
        error_log("Processing key: $key");
        
        // Saltar la imagen principal
        if ($key === 'image') continue;
        
        // Manejar arrays de archivos (images[] desde frontend)
        if (is_array($fileData['name'])) {
            error_log("Array de archivos encontrado en key: $key");
            $fileCount = count($fileData['name']);
            error_log("Total de archivos en array: $fileCount");
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($fileData['error'][$i] === UPLOAD_ERR_OK) {
                    $ext = pathinfo($fileData['name'][$i], PATHINFO_EXTENSION);
                    $safe = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', pathinfo($fileData['name'][$i], PATHINFO_FILENAME));
                    $filename = $safe . '-' . time() . '-' . $i . '.' . $ext;
                    $dest = $uploadDir . '/' . $filename;
                    
                    if (move_uploaded_file($fileData['tmp_name'][$i], $dest)) {
                        $additionalImages[] = '/uploads/experiencias/' . $filename;
                        error_log("✅ Imagen adicional guardada: $filename");
                    } else {
                        error_log("❌ Error moviendo archivo: " . $fileData['name'][$i]);
                    }
                }
            }
        }
    }
    
    error_log("Total imágenes adicionales guardadas: " . count($additionalImages));
}

if (!$userId) { http_response_code(401); echo json_encode(['error'=>'not_authenticated']); exit; }

$titulo = $mysqli->real_escape_string($input['titulo'] ?? ($input['title'] ?? ''));
$descripcion = $mysqli->real_escape_string($input['descripcion'] ?? ($input['description'] ?? ''));
$precio = floatval($input['precio'] ?? ($input['price'] ?? 0));
$categoria = $mysqli->real_escape_string($input['categoria'] ?? ($input['category'] ?? ''));
$duracion = intval($input['duracion'] ?? ($input['duration_minutes'] ?? ($input['duration'] ?? 0)));
$cantidad = intval($input['cantidad'] ?? ($input['capacity'] ?? 1));
$location = $mysqli->real_escape_string($input['location'] ?? 'La Paz');
$imageEsc = $uploadedImagePath ? $mysqli->real_escape_string($uploadedImagePath) : null;

// Handle language fields
$idiomaPrincipal = $mysqli->real_escape_string($input['idioma_principal'] ?? '');
$idiomasAdicionales = $mysqli->real_escape_string($input['idiomas_adicionales'] ?? '');

// Convert additional images array to JSON
$imagenesJson = !empty($additionalImages) ? json_encode($additionalImages) : null;
$imagenesJsonEsc = $imagenesJson ? $mysqli->real_escape_string($imagenesJson) : null;

if (!$titulo) { http_response_code(422); echo json_encode(['error'=>'validation','message'=>'titulo required']); exit; }

// Detectar columnas existentes para soportar esquemas en inglés o español (y 'imagen')
$columns = [];
$res = $mysqli->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='" . $mysqli->real_escape_string($dbName) . "' AND TABLE_NAME='experiencias'");
if ($res) {
    while ($r = $res->fetch_assoc()) { $columns[] = $r['COLUMN_NAME']; }
    $res->free();
}

$colTitulo = in_array('titulo', $columns) ? 'titulo' : (in_array('title', $columns) ? 'title' : null);
$colDescripcion = in_array('descripcion', $columns) ? 'descripcion' : (in_array('description', $columns) ? 'description' : null);
$colPrecio = in_array('precio', $columns) ? 'precio' : (in_array('price', $columns) ? 'price' : null);
$colCategoria = in_array('categoria', $columns) ? 'categoria' : (in_array('location', $columns) ? 'location' : null);
$colDuracion = in_array('duracion', $columns) ? 'duracion' : (in_array('duration_minutes', $columns) ? 'duration_minutes' : null);
$colCantidad = in_array('cantidad', $columns) ? 'cantidad' : (in_array('capacity', $columns) ? 'capacity' : null);
$colEstado = in_array('estado', $columns) ? 'estado' : (in_array('published', $columns) ? 'published' : null);
$colImage = in_array('image', $columns) ? 'image' : (in_array('imagen', $columns) ? 'imagen' : null);
$colIdiomaPrincipal = in_array('idioma_principal', $columns);
$colIdiomasAdicionales = in_array('idiomas_adicionales', $columns);
$colImagenes = in_array('imagenes', $columns);

if ($colTitulo === null || $colDescripcion === null || $colPrecio === null || $colDuracion === null || $colEstado === null) {
    http_response_code(500);
    echo json_encode(['error'=>'schema_mismatch','message'=>'La tabla experiencias no tiene las columnas esperadas. Columnas detectadas: ' . implode(',', $columns)]);
    exit;
}

// Construir INSERT según el esquema detectado
if ($colTitulo === 'titulo') {
    // esquema en español
    // esquema en español
    if ($imageEsc && $colImage && $colCantidad) {
        $stmt = $mysqli->prepare("INSERT INTO experiencias (guia_id, titulo, descripcion, precio, categoria, duracion, cantidad, estado, " . $colImage . ", created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,?,NOW(),NOW())");
        if (!$stmt) { http_response_code(500); echo json_encode(['error'=>'db_prepare_failed','message'=>$mysqli->error]); exit; }
        $estado = 'published';
        $stmt->bind_param('issdsiiss', $userId, $titulo, $descripcion, $precio, $categoria, $duracion, $cantidad, $estado, $imageEsc);
    } elseif ($imageEsc && $colImage) {
        // cantidad column missing, insert without cantidad
        $stmt = $mysqli->prepare("INSERT INTO experiencias (guia_id, titulo, descripcion, precio, categoria, duracion, estado, " . $colImage . ", created_at, updated_at) VALUES (?,?,?,?,?,?,?,?,NOW(),NOW())");
        if (!$stmt) { http_response_code(500); echo json_encode(['error'=>'db_prepare_failed','message'=>$mysqli->error]); exit; }
        $estado = 'published';
        $stmt->bind_param('issdsiss', $userId, $titulo, $descripcion, $precio, $categoria, $duracion, $estado, $imageEsc);
    } elseif ($colCantidad) {
        $stmt = $mysqli->prepare("INSERT INTO experiencias (guia_id, titulo, descripcion, precio, categoria, duracion, cantidad, estado, created_at, updated_at) VALUES (?,?,?,?,?,?,?,?, NOW(), NOW())");
        if (!$stmt) { http_response_code(500); echo json_encode(['error'=>'db_prepare_failed','message'=>$mysqli->error]); exit; }
        $estado = 'published';
        $stmt->bind_param('issdsiis', $userId, $titulo, $descripcion, $precio, $categoria, $duracion, $cantidad, $estado);
    } else {
        $stmt = $mysqli->prepare("INSERT INTO experiencias (guia_id, titulo, descripcion, precio, categoria, duracion, estado, created_at, updated_at) VALUES (?,?,?,?,?,?,?, NOW(), NOW())");
        if (!$stmt) { http_response_code(500); echo json_encode(['error'=>'db_prepare_failed','message'=>$mysqli->error]); exit; }
        $estado = 'published';
        $stmt->bind_param('issdsis', $userId, $titulo, $descripcion, $precio, $categoria, $duracion, $estado);
    }
} else {
    // esquema en inglés (title, description, price, location, duration_minutes, published)
    $published = 0; // Pending approval by default
    $titleVar = $titulo;
    $descriptionVar = $descripcion;
    $priceVar = $precio;
    $locationVar = $location;
    $durationVar = $duracion;

    // Build INSERT query dynamically based on available columns
    $cols = ['guia_id', 'title', 'description', 'price', 'categoria', 'location', 'duration_minutes'];
    $vals = ['?', '?', '?', '?', '?', '?', '?'];
    $types = 'issdssi';
    $bindVars = [$userId, $titleVar, $descriptionVar, $priceVar, $categoria, $locationVar, $durationVar];
    
    if ($colCantidad) {
        $cols[] = $colCantidad;
        $vals[] = '?';
        $types .= 'i';
        $bindVars[] = $cantidad;
    }
    
    $cols[] = 'published';
    $vals[] = '?';
    $types .= 'i';
    $bindVars[] = $published;
    
    if ($imageEsc && $colImage) {
        $cols[] = $colImage;
        $vals[] = '?';
        $types .= 's';
        $bindVars[] = $imageEsc;
    }
    
    if ($colIdiomaPrincipal) {
        $cols[] = 'idioma_principal';
        $vals[] = '?';
        $types .= 's';
        $bindVars[] = $idiomaPrincipal;
    }
    
    if ($colIdiomasAdicionales) {
        $cols[] = 'idiomas_adicionales';
        $vals[] = '?';
        $types .= 's';
        $bindVars[] = $idiomasAdicionales;
    }
    
    if ($colImagenes && $imagenesJsonEsc) {
        $cols[] = 'imagenes';
        $vals[] = '?';
        $types .= 's';
        $bindVars[] = $imagenesJsonEsc;
        error_log("📸 JSON de imágenes adicionales que se guardará: " . $imagenesJsonEsc);
    } else {
        error_log("⚠️ No se guardarán imágenes adicionales. colImagenes: " . ($colImagenes ? 'true' : 'false') . ", imagenesJsonEsc: " . ($imagenesJsonEsc ? 'exists' : 'null'));
    }
    
    $cols[] = 'created_at';
    $cols[] = 'updated_at';
    $vals[] = 'NOW()';
    $vals[] = 'NOW()';
    
    $sql = "INSERT INTO experiencias (" . implode(', ', $cols) . ") VALUES (" . implode(', ', $vals) . ")";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) { 
        http_response_code(500); 
        echo json_encode(['error'=>'db_prepare_failed','message'=>$mysqli->error,'sql'=>$sql]); 
        exit; 
    }
    
    $stmt->bind_param($types, ...$bindVars);
}
if (!$stmt->execute()) { http_response_code(500); echo json_encode(['error'=>'db_insert_failed','message'=>$stmt->error]); exit; }
$id = $stmt->insert_id;
$stmt->close();

$q = $mysqli->query("SELECT * FROM experiencias WHERE id = $id LIMIT 1");
$row = $q->fetch_assoc();

// Decodificar JSON de imágenes adicionales si existe
if (isset($row['imagenes']) && !empty($row['imagenes'])) {
    // Limpiar escapes literales de MySQL
    $cleanJson = str_replace(['\"', '\\/'], ['"', '/'], $row['imagenes']);
    $decoded = json_decode($cleanJson, true);
    if (is_array($decoded)) {
        $row['imagenes'] = $decoded;
    } else {
        $row['imagenes'] = [];
    }
}

// Normalizar campo de imagen en la respuesta: siempre exponer `image` y `image_url`
$detectedImageCol = $colImage ?? null; // definido arriba cuando detectamos columnas
$imageVal = null;
if ($detectedImageCol && isset($row[$detectedImageCol])) {
    $imageVal = $row[$detectedImageCol];
}
// También soportar si la columna ya es 'image'
if (!$imageVal && isset($row['image'])) { $imageVal = $row['image']; }

// Construir URL absoluta basada en el host actual
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
$imageUrl = $imageVal ? rtrim($baseUrl, '/') . '/' . ltrim($imageVal, '/') : null;

// Asegurar que la respuesta tenga la clave `image` y `image_url` para el frontend
$row['image'] = $imageVal;
$row['image_url'] = $imageUrl;

echo json_encode(['experiencia'=>$row]);
$mysqli->close();
