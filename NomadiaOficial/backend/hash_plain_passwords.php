<?php
// hash_plain_passwords.php
// Escanea la tabla `users` y hashea contraseñas que parecen estar en texto plano.

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

$env = load_env(__DIR__ . '/.env');
$dbHost = $env['DB_HOST'] ?? '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? '3306';
$dbName = $env['DB_DATABASE'] ?? 'nomadia';
$dbUser = $env['DB_USERNAME'] ?? 'root';
$dbPass = $env['DB_PASSWORD'] ?? '';

$mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    echo "DB connection failed: " . $mysqli->connect_error . PHP_EOL;
    exit(1);
}

// Find users whose password does not start with '$' (likely plaintext)
$res = $mysqli->query("SELECT id,email,password FROM users");
if (!$res) { echo "Query failed: " . $mysqli->error . PHP_EOL; exit(1); }

$updated = 0;
while ($row = $res->fetch_assoc()) {
    $pw = $row['password'];
    if (strlen($pw) === 0) continue;
    // If it starts with a dollar sign, assume it's already hashed (bcrypt/argon)
    if ($pw[0] === '$') continue;
    // Otherwise hash and update
    $new = password_hash($pw, PASSWORD_DEFAULT);
    $id = (int)$row['id'];
    $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param('si', $new, $id);
    if ($stmt->execute()) {
        echo "Updated user {$row['email']} (id={$id})\n";
        $updated++;
    } else {
        echo "Failed to update user {$row['email']}: " . $stmt->error . PHP_EOL;
    }
    $stmt->close();
}

echo "Done. Updated $updated users." . PHP_EOL;

$mysqli->close();

?>
