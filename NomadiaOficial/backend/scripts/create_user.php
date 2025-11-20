<?php
// Usage: php create_user.php name email password
// Creates a user in the `users` table using DB credentials from ../../.env

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

$env = load_env(__DIR__ . '/../../.env');
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'nomadia';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

$name = $argv[1] ?? null;
$email = $argv[2] ?? null;
$password = $argv[3] ?? null;

if (!$name || !$email || !$password) {
    echo "Usage: php create_user.php \"Full Name\" email@example.com password\n";
    exit(1);
}

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    echo "DB connect error: " . $mysqli->connect_error . "\n";
    exit(1);
}

$emailEsc = $mysqli->real_escape_string($email);
$res = $mysqli->query("SELECT id FROM users WHERE email = '$emailEsc' LIMIT 1");
if ($res && $res->num_rows > 0) {
    echo "User with email $email already exists.\n";
    exit(0);
}

$nameEsc = $mysqli->real_escape_string($name);
$passHash = password_hash($password, PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');
$sql = "INSERT INTO users (name,email,password,role,created_at,updated_at) VALUES ('$nameEsc','$emailEsc','$passHash','traveler','$now','$now')";
if ($mysqli->query($sql)) {
    echo "Created user $email with id " . $mysqli->insert_id . "\n";
} else {
    echo "Insert failed: " . $mysqli->error . "\n";
}

$mysqli->close();
