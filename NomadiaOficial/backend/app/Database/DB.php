<?php
namespace App\Database;

class DB
{
    public static function getConnection()
    {
        $envPath = __DIR__ . '/../../../.env';
        $result = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (!strpos($line, '=')) continue;
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                $value = preg_replace('/(^["\']|["\']$)/', '', $value);
                $result[$name] = $value;
            }
        }

        $host = $result['DB_HOST'] ?? '127.0.0.1';
        $port = $result['DB_PORT'] ?? '3306';
        $name = $result['DB_DATABASE'] ?? 'nomadia';
        $user = $result['DB_USERNAME'] ?? 'root';
        $pass = $result['DB_PASSWORD'] ?? '';

        $mysqli = new \mysqli($host, $user, $pass, $name, (int)$port);
        if ($mysqli->connect_errno) {
            throw new \Exception('DB connection failed: ' . $mysqli->connect_error);
        }
        return $mysqli;
    }
}
