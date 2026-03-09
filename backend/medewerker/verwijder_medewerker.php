<?php
session_start();
require_once __DIR__ . '/../config2.php';

// 1. Check of naam is meegegeven
if (!isset($_GET['naam'])) {
    die("FOUT: geen naam opgegeven.");
}

$naam = $_GET['naam'];

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

// 2. Check of medewerker bestaat
$stmt = $pdo->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
$stmt->execute([$naam]);
$medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medewerker) {
    die("FOUT: medewerker niet gevonden.");
}

// 3. Alle productkolommen ophalen
$stmt = $pdo->query("SHOW COLUMNS FROM Medewerker");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kolommen die GEEN product zijn
$exclude = ["Naam","Functie","Locatie","Leidinggevende","Bedrijf","Referentie","Email"];

// 4. Alle producten op NULL zetten
foreach ($columns as $col) {
    $kolom = $col['Field'];
    if (in_array($kolom, $exclude)) continue;

    $update = $pdo->prepare("UPDATE Medewerker SET `$kolom` = NULL WHERE Naam = ?");
    $update->execute([$naam]);
}

// 5. Medewerker verwijderen
$delete = $pdo->prepare("DELETE FROM Medewerker WHERE Naam = ?");
$delete->execute([$naam]);

// 6. Redirect terug naar medewerkerspagina
header("Location: ../../HuidigeToegangen.php?verwijderd=" . urlencode($naam));
exit;
