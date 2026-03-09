<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    die("Niet ingelogd");
}

if (!isset($_POST['email']) || !isset($_FILES['upload'])) {
    die("Fout: ontbrekende data");
}

$email = $_POST['email'];
$bestand = $_FILES['upload'];

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

// Bestand uitlezen
$naam = $bestand['name'];
$type = $bestand['type'];
$data = file_get_contents($bestand['tmp_name']);

try {
    $stmt = $pdo->prepare("
        INSERT INTO MedewerkerBestanden 
        (MedewerkerEmail, BestandNaam, BestandType, BestandData, UploadDatum)
        VALUES (?, ?, ?, ?, NOW())
    ");

    $stmt->execute([$email, $naam, $type, $data]);

} catch (PDOException $e) {
    die("Database fout: " . $e->getMessage());
}

// Terug naar medewerkerpagina
header("Location: ../../HuidigeToegangen.php?naam=" . urlencode($_GET['naam'] ?? ''));
exit;
