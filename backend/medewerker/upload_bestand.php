<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['email'])) {
    die("Niet ingelogd");
}

if (!isset($_POST['email']) || !isset($_FILES['upload'])) {
    die("Fout: ontbrekende data");
}

$email = $_POST['email'];
$bestand = $_FILES['upload'];

// Bestand uitlezen
$naam = $bestand['name'];
$type = $bestand['type'];
$data = file_get_contents($bestand['tmp_name']);

// Opslaan in database
$stmt = $conn->prepare("
    INSERT INTO MedewerkerBestanden (MedewerkerEmail, BestandNaam, BestandType, BestandData, UploadDatum)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt->bind_param("ssss", $email, $naam, $type, $data);
$stmt->execute();
$stmt->close();

// Terug naar medewerkerpagina
header("Location: ../../HuidigeToegangen.php?naam=" . urlencode($_GET['naam'] ?? ''));
exit;
