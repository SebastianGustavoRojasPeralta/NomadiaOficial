<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Router for PHP built-in server to add CORS headers and route API requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin) {
    if (preg_match('#^https?://(localhost|127\.0\.0\.1)(:\d+)?$#', $origin)) {
        header('Access-Control-Allow-Origin: ' . $origin);
        header('Access-Control-Allow-Credentials: true');
    }
}
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$file = __DIR__ . $uri;

// If the requested resource exists as a file (like images), serve it directly without JSON headers
if ($uri !== '/' && file_exists($file) && !is_dir($file)) {
    // Don't set Content-Type to JSON for static files
    if (preg_match('/\.(jpg|jpeg|png|gif|webp|avif|svg|css|js|ico)$/i', $file)) {
        // Let PHP determine the proper MIME type
        return false;
    }
    return false;
}

// Set JSON header only for API requests
header('Content-Type: application/json; charset=utf-8');

// If the path points to /api/v1/something and there's a corresponding .php file, include it
if (preg_match('#^/api/v1/(?P<name>[A-Za-z0-9_\-]+)$#', $uri, $m)) {
    $php = __DIR__ . '/api/v1/' . $m['name'] . '.php';
    if (file_exists($php)) {
        require $php;
        exit;
    }
}

// Fallback: serve index.html for SPA or 404 JSON for API
if (strpos($uri, '/api/') === 0) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found', 'path' => $uri]);
    exit;
}

// Serve SPA index
readfile(__DIR__ . '/index.html');
exit;
