<?php

ini_set('display_errors', 0);
error_reporting(E_ALL);

function required_env(string $name): string
{
    $value = getenv($name);

    if ($value === false || $value === '') {
        error_log("Missing required environment variable: " . $name);
        http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Server configuration error"
        ]);
        exit;
    }

    return $value;
}

$host = required_env('DB_HOST');
$user = required_env('DB_USER');
$password = required_env('DB_PASSWORD');
$database = required_env('DB_NAME');

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$database};charset=utf8mb4",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());

    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed"
    ]);
    exit;
}