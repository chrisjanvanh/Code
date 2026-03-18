<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_GET['naam'])) {
    die("FOUT: geen naam opgegeven.");
}

$naam = $_GET['naam'];
$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   1. Medewerker ophalen + bedrijf bepalen
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
$stmt->execute([$naam]);
$medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medewerker) {
    die("FOUT: medewerker niet gevonden.");
}

$bedrijf = $medewerker['Bedrijf'];

/* ---------------------------------------------------
   2. Alle producten ophalen uit BedrijfProduct
--------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT Product, Waarde
    FROM BedrijfProduct
    WHERE Medewerker = ? AND Bedrijf = ?
");
$stmt->execute([$naam, $bedrijf]);
$toegangen = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   3. Alle producten in één keer updaten
      Oude logica:
      - 1 → 2 (verwijderd)
      - 0 of 3 → NULL (geen toegang)
--------------------------------------------------- */
$stmtUpdate = $pdo->prepare("
    UPDATE BedrijfProduct
    SET Waarde = CASE
        WHEN Waarde = 1 THEN 2
        WHEN Waarde IN (0,3) THEN NULL
        ELSE Waarde
    END
    WHERE Medewerker = ? AND Bedrijf = ?
");

$stmtUpdate->execute([$naam, $bedrijf]);

/* ---------------------------------------------------
   4. Logboek
--------------------------------------------------- */
$log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
$log->execute([
    "$gebruikerNaam heeft alle producten verwijderd voor $naam (bedrijf: $bedrijf)",
    "Huidige Medewerker"
]);

/* ---------------------------------------------------
   5. Redirect
--------------------------------------------------- */
header("Location: ../../HuidigeToegangen.php?naam=" . urlencode($naam));
exit;
