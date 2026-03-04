<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

$host = "217.76.24.141";
$user = "VDL";
$password = "1234";
$database = "VDL";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database fout: " . $e->getMessage()
    ]);
    exit;
}
