<?php
require __DIR__ . '/../config.php';

if (!isset($_POST['naam'], $_POST['kolom'], $_POST['waarde'])) {
    echo "FOUT: ontbrekende parameters";
    exit;
}

$naam   = $_POST['naam'];
$kolom  = $_POST['kolom'];
$waarde = $_POST['waarde'];

// Kolomnamen die nooit automatisch worden aangepast
$exclude = ["Naam","Functie","Locatie","Leidinggevende","Bedrijf","Referentie","Email"];

// Speciale kolommen
$colVDLAD = "VDL AD Account";
$colMyVDL = "MyVDL";
$colKelio = "Kelio";

// NULL correct verwerken
if ($waarde === "null") {
    $waarde = NULL;
}

// Huidige medewerker ophalen
$stmt = $conn->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
$stmt->bind_param("s", $naam);
$stmt->execute();
$res = $stmt->get_result();
$medewerker = $res->fetch_assoc();
$stmt->close();

if (!$medewerker) {
    echo "FOUT: medewerker niet gevonden";
    exit;
}

// 1. BASISUPDATE: kolom op 1 of NULL zetten
$update = $conn->prepare("UPDATE Medewerker SET `$kolom` = ? WHERE Naam = ?");
$update->bind_param("ss", $waarde, $naam);
$update->execute();
$update->close();

// 2. AUTOMATISCHE LOGICA
$productenNaarNul = []; // producten die 3 → 0 gaan

// A. VDL AD Account afgerond → MyVDL 3 → 0
if ($kolom === $colVDLAD && $waarde == 1) {
    if ($medewerker[$colMyVDL] == 3) {
        $conn->query("UPDATE Medewerker SET `$colMyVDL` = 0 WHERE Naam = '$naam'");
        $productenNaarNul[] = $colMyVDL;
    }
}

// B. MyVDL afgerond → alle 3 → 0
if ($kolom === $colMyVDL && $waarde == 1) {
    $cols = $conn->query("SHOW COLUMNS FROM Medewerker")->fetch_all(MYSQLI_ASSOC);

    foreach ($cols as $c) {
        $field = $c['Field'];
        if (in_array($field, $exclude)) continue;
        if ($field === $colMyVDL) continue;

        if ($medewerker[$field] == 3) {
            $conn->query("UPDATE Medewerker SET `$field` = 0 WHERE Naam = '$naam'");
            $productenNaarNul[] = $field;
        }
    }
}

// C. Kelio afgerond → alle 3 → 0
if ($kolom === $colKelio && $waarde == 1) {
    $cols = $conn->query("SHOW COLUMNS FROM Medewerker")->fetch_all(MYSQLI_ASSOC);

    foreach ($cols as $c) {
        $field = $c['Field'];
        if (in_array($field, $exclude)) continue;
        if ($field === $colKelio) continue;

        if ($medewerker[$field] == 3) {
            $conn->query("UPDATE Medewerker SET `$field` = 0 WHERE Naam = '$naam'");
            $productenNaarNul[] = $field;
        }
    }
}

// 3. WEBHOOK versturen (alleen voor producten die 0 zijn geworden)
if (!empty($productenNaarNul)) {

    $emails = [];

    $stmtProd = $conn->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");
    foreach ($productenNaarNul as $prod) {
        $stmtProd->bind_param("s", $prod);
        $stmtProd->execute();
        $res = $stmtProd->get_result();

        if ($row = $res->fetch_assoc()) {
            if (!empty($row['Contactpersoon'])) {
                $emails[] = $row['Contactpersoon'];
            }
        }
    }
    $stmtProd->close();

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
