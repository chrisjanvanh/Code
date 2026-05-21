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

$id = $_POST['id'] ?? null;
$prijs = $_POST['prijs'] ?? null;

if (!$id || !$prijs) {
    exit("Fout: ontbrekende data");
}

$stmt = $pdo->prepare("UPDATE HardwareBestellen SET Prijs = ? WHERE ID = ?");
$stmt->execute([$prijs, $id]);

echo "OK";
