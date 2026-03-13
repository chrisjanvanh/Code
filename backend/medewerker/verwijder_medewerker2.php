<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_GET['naam'])) {
    die("FOUT: geen naam opgegeven.");
}

$naam = $_GET['naam'];
$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

// Check of medewerker bestaat
$stmt = $pdo->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
$stmt->execute([$naam]);
$medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medewerker) {
    die("FOUT: medewerker niet gevonden.");
}

// Kolommen ophalen
$stmt = $pdo->query("SHOW COLUMNS FROM Medewerker");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

$exclude = ["Naam","Functie","Locatie","Leidinggevende","Bedrijf","Referentie","Email"];

foreach ($columns as $col) {
    $kolom = $col['Field'];
    if (in_array($kolom, $exclude)) continue;

    // Update in één query
    $update = $pdo->prepare("
        UPDATE Medewerker
        SET `$kolom` = CASE
            WHEN `$kolom` = 1 THEN 2
            WHEN `$kolom` IN (0,3) THEN NULL
            ELSE `$kolom`
        END
        WHERE Naam = ?
    ");
    $update->execute([$naam]);
}

$melding = "Succes het opzeggen van alle producten aangevraagd voor $naam"

// Redirect
header("Location: ../../HuidigeToegangen.php?verwijderd=" . urlencode($naam));
exit;
