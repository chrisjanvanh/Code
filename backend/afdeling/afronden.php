<?php
session_start();
require __DIR__ . '/../config2.php';

if (!isset($_POST['naam'], $_POST['kolom'], $_POST['waarde'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$naam   = $_POST['naam'];
$kolom  = $_POST['kolom'];
$waarde = $_POST['waarde'];

// Kolommen die nooit automatisch worden aangepast
$exclude = ["Naam","Functie","Locatie","Leidinggevende","Bedrijf","Referentie","Email"];

// Speciale kolommen
$colVDLAD = "VDL AD Account";
$colMyVDL = "MyVDL";
$colKelio = "Kelio";

// NULL correct verwerken
if ($waarde === "null") {
    $waarde = null;
}

/* ---------------------------------------------------
   1. Medewerker ophalen
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
$stmt->execute([$naam]);
$medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medewerker) {
    echo "FOUT: medewerker niet gevonden";
    exit;
}

/* ---------------------------------------------------
   2. BASISUPDATE
--------------------------------------------------- */
$stmt = $pdo->prepare("UPDATE Medewerker SET `$kolom` = ? WHERE Naam = ?");
$stmt->execute([$waarde, $naam]);

$productenNaarNul = [];

/* ---------------------------------------------------
   3. AUTOMATISCHE LOGICA
--------------------------------------------------- */

// A. VDL AD Account afgerond → MyVDL 3 → 0
if ($kolom === $colVDLAD && $waarde == 1) {
    if ($medewerker[$colMyVDL] == 3) {
        $pdo->prepare("UPDATE Medewerker SET `$colMyVDL` = 0 WHERE Naam = ?")->execute([$naam]);
        $productenNaarNul[] = $colMyVDL;
    }
}

// B. MyVDL afgerond → alle 3 → 0
if ($kolom === $colMyVDL && $waarde == 1) {

    $cols = $pdo->query("SHOW COLUMNS FROM Medewerker")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cols as $c) {
        $field = $c['Field'];

        if (in_array($field, $exclude)) continue;
        if ($field === $colMyVDL) continue;

        if ($medewerker[$field] == 3) {
            $pdo->prepare("UPDATE Medewerker SET `$field` = 0 WHERE Naam = ?")->execute([$naam]);
            $productenNaarNul[] = $field;
        }
    }
}

// C. Kelio afgerond → alle 3 → 0
if ($kolom === $colKelio && $waarde == 1) {

    $cols = $pdo->query("SHOW COLUMNS FROM Medewerker")->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cols as $c) {
        $field = $c['Field'];

        if (in_array($field, $exclude)) continue;
        if ($field === $colKelio) continue;

        if ($medewerker[$field] == 3) {
            $pdo->prepare("UPDATE Medewerker SET `$field` = 0 WHERE Naam = ?")->execute([$naam]);
            $productenNaarNul[] = $field;
        }
    }
}

/* ---------------------------------------------------
   4. WEBHOOK versturen
--------------------------------------------------- */
if (!empty($productenNaarNul)) {

    $emails = [];

    $stmtProd = $pdo->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");

    foreach ($productenNaarNul as $prod) {
        $stmtProd->execute([$prod]);
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
