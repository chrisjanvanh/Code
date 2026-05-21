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

if (!isset($_SESSION['email'])) {
    exit("Geen toegang");
}

$naam = $_POST['naam'] ?? null;
$prijs = $_POST['prijs'] ?? null;

if (!$naam || !$prijs) {
    exit("Ontbrekende gegevens");
}

$stmt = $pdo->prepare("INSERT INTO HardwareBestellen (Naam, Prijs) VALUES (?, ?)");
$stmt->execute([$naam, $prijs]);

echo "OK";
