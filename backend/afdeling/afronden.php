<?php
session_start();
require __DIR__ . '/../config2.php';

if (!isset($_POST['naam'], $_POST['kolom'], $_POST['waarde'], $_POST['bedrijf'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$naam     = $_POST['naam'];
$kolom    = $_POST['kolom'];   // = productnaam
$waarde   = $_POST['waarde'];
$bedrijf  = $_POST['bedrijf'];

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

if ($waarde === "null") {
    $waarde = null;
}

/* ---------------------------------------------------
   1. BASISUPDATE in BedrijfProduct
--------------------------------------------------- */
$stmt = $pdo->prepare("
    UPDATE BedrijfProduct
    SET Waarde = ?
    WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
");
$stmt->execute([$waarde, $naam, $kolom, $bedrijf]);

// Logboek
$log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
$log->execute([
    "$gebruikerNaam heeft de taak $kolom naar $waarde afgerond voor $naam",
    "Taken"
]);

$productenNaarNul = [];

/* ---------------------------------------------------
   2. AUTOMATISCHE LOGICA
--------------------------------------------------- */

$colVDLAD = "VDL AD Account";
$colMyVDL = "MyVDL";
$colKelio = "Kelio";

/* A. VDL AD Account afgerond → MyVDL 3 → 0 */
if ($kolom === $colVDLAD && $waarde == 1) {

    $stmt = $pdo->prepare("
        SELECT Waarde FROM BedrijfProduct
        WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
    ");
    $stmt->execute([$naam, $colMyVDL, $bedrijf]);
    $myvdl = $stmt->fetchColumn();

    if ($myvdl == 3) {
        $pdo->prepare("
            UPDATE BedrijfProduct SET Waarde = 0
            WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
        ")->execute([$naam, $colMyVDL, $bedrijf]);

        $productenNaarNul[] = $colMyVDL;
    }
}

/* B. MyVDL afgerond → alle 3 → 0 */
if ($kolom === $colMyVDL && $waarde == 1) {

    $stmt = $pdo->prepare("
        SELECT Product FROM BedrijfProduct
        WHERE Medewerker = ? AND Bedrijf = ? AND Waarde = 3
    ");
    $stmt->execute([$naam, $bedrijf]);
    $producten = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($producten as $p) {
        if ($p === $colMyVDL) continue;

        $pdo->prepare("
            UPDATE BedrijfProduct SET Waarde = 0
            WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
        ")->execute([$naam, $p, $bedrijf]);

        $productenNaarNul[] = $p;
    }
}

/* C. Kelio afgerond → alle 3 → 0 */
if ($kolom === $colKelio && $waarde == 1) {

    $stmt = $pdo->prepare("
        SELECT Product FROM BedrijfProduct
        WHERE Medewerker = ? AND Bedrijf = ? AND Waarde = 3
    ");
    $stmt->execute([$naam, $bedrijf]);
    $producten = $stmt->fetchAll(PDO::FETCH_COLUMN);

    foreach ($producten as $p) {
        if ($p === $colKelio) continue;

        $pdo->prepare("
            UPDATE BedrijfProduct SET Waarde = 0
            WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
        ")->execute([$naam, $p, $bedrijf]);

        $productenNaarNul[] = $p;
    }
}

/* ---------------------------------------------------
   3. WEBHOOK
--------------------------------------------------- */
if (!empty($productenNaarNul)) {

    $emails = [];

    $stmtProd = $pdo->prepare("
        SELECT Contactpersoon FROM Product
        WHERE Product = ? AND Bedrijf = ?
    ");

    foreach ($productenNaarNul as $prod) {
        $stmtProd->execute([$prod, $bedrijf]);
        $row = $stmtProd->fetch(PDO::FETCH_ASSOC);

        if (!empty($row['Contactpersoon'])) {
            $emails[] = $row['Contactpersoon'];
        }
    }

    $emails = array_unique($emails);

    $payload = [
        "naam"      => $naam,
        "actie"     => "toegevoegd",
        "producten" => $productenNaarNul,
        "emails"    => $emails
    ];

    $url = "https://hook.eu1.make.com/113rh6zbq8knken7iynmqmtto1k0f67n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

echo "OK";
