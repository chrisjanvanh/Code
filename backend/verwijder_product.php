<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();

require 'config2.php';

// Alleen ingelogde gebruikers
if (!isset($_SESSION['email'])) {
    exit("Geen toegang");
}

$id = $_POST['id'] ?? null;

if (!$id) {
    exit("Geen ID ontvangen");
}

$stmt = $pdo->prepare("DELETE FROM HardwareBestellen WHERE ID = ?");
$stmt->execute([$id]);

echo "OK";
