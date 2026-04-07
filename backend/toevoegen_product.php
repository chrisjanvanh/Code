<?php
session_start();
require 'config2.php';

if (!isset($_SESSION['email'])) {
    exit("Geen toegang");
}

$naam = $_POST['naam'] ?? null;
$prijs = str_replace('.', '', $_POST['prijs']);
$prijs = str_replace(',', '.', $prijs);

if (!is_numeric($prijs)) {
    exit("Ongeldig bedrag");
}

if (!$naam || !$prijs) {
    exit("Ontbrekende gegevens");
}

$stmt = $pdo->prepare("INSERT INTO HardwareBestellen (Naam, Prijs) VALUES (?, ?)");
$stmt->execute([$naam, $prijs]);

echo "OK";
