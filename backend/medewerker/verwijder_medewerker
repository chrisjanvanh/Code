<?php
session_start();
require_once __DIR__ . '/../config.php';

// 1. Check of naam is meegegeven
if (!isset($_GET['naam'])) {
    die("FOUT: geen naam opgegeven.");
}

$naam = $_GET['naam'];

// 2. Check of medewerker bestaat
$stmt = $conn->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
$stmt->bind_param("s", $naam);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("FOUT: medewerker niet gevonden.");
}

$medewerker = $res->fetch_assoc();
$stmt->close();

// 3. Alle productkolommen ophalen
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN product zijn
$exclude = ["Naam","Functie","Locatie","Leidinggevende","Bedrijf","Referentie","Email"];

// 4. Alle producten op NULL zetten
foreach ($columns as $col) {
    $kolom = $col['Field'];
    if (in_array($kolom, $exclude)) continue;

    $update = $conn->prepare("UPDATE Medewerker SET `$kolom` = NULL WHERE Naam = ?");
    $update->bind_param("s", $naam);
    $update->execute();
    $update->close();
}

// 5. Medewerker verwijderen
$delete = $conn->prepare("DELETE FROM Medewerker WHERE Naam = ?");
$delete->bind_param("s", $naam);
$delete->execute();
$delete->close();

// 6. Redirect terug naar medewerkerspagina
header("Location: ../../HuidigeToegangen.php?verwijderd=" . urlencode($naam));
exit;
