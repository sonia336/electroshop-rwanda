<?php
/**
 * Database connection using PDO.
 * Reads credentials from environment variables (set in docker-compose.yml
 * or in your hosting provider's dashboard). Falls back to local defaults
 * so it also works if you run PHP directly without Docker.
 */

$host   = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'electroshop';
$user   = getenv('DB_USER') ?: 'root';
$pass   = getenv('DB_PASS') ?: '';
$port   = getenv('DB_PORT') ?: '3306';

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE  => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // In production you would log this instead of exposing details.
    die("Database connection failed: " . $e->getMessage());
}
