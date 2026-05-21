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
   2. Alle producten updaten
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
   3. Check of ALLE waarden NULL zijn
--------------------------------------------------- */
$stmtCheck = $pdo->prepare("
    SELECT COUNT(*) 
    FROM BedrijfProduct 
    WHERE Medewerker = ? AND Bedrijf = ? AND Waarde IS NOT NULL
");
$stmtCheck->execute([$naam, $bedrijf]);
$heeftNogProducten = $stmtCheck->fetchColumn();

/* ---------------------------------------------------
   4. Als medewerker geen producten meer heeft → verwijderen
--------------------------------------------------- */
if ($heeftNogProducten == 0) {

    // Verwijder uit BedrijfProduct
    $delBP = $pdo->prepare("
        DELETE FROM BedrijfProduct 
        WHERE Medewerker = ? AND Bedrijf = ?
    ");
    $delBP->execute([$naam, $bedrijf]);

    // Verwijder uit Medewerker
    $delMed = $pdo->prepare("
        DELETE FROM Medewerker 
        WHERE Naam = ? AND Bedrijf = ?
    ");
    $delMed->execute([$naam, $bedrijf]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft medewerker $naam volledig verwijderd (geen producten meer, bedrijf: $bedrijf)",
        "Huidige Medewerker"
    ]);
} else {

    // Normale log
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft alle producten verwijderd voor $naam (bedrijf: $bedrijf)",
        "Huidige Medewerker"
    ]);
}

/* ---------------------------------------------------
   5. Redirect
--------------------------------------------------- */
header("Location: ../../HuidigeToegangen.php?naam=" . urlencode($naam));
exit;
