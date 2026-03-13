<?php
session_start();

// Login check
if (!isset($_SESSION['email'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../config2.php'; // bevat $pdo

$afdeling = "HR";

// $_SESSION['gebruikernaam'] = $_POST['gebruikernaam'] ?? "Onbekend";
$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* -----------------------------------------------------------
   1. Controleer of gebruiker toegang heeft tot deze afdeling
----------------------------------------------------------- */

$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}

/* -----------------------------------------------------------
   2. Kolommen ophalen uit database
----------------------------------------------------------- */

$columns = $pdo->query("SHOW COLUMNS FROM Medewerker")->fetchAll(PDO::FETCH_ASSOC);

$exclude = [
    "Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf",
    "Referentie", "Email", "VDL AD Account", "MyVDL", "Kelio"
];

$melding = "";

/* -----------------------------------------------------------
   3. POST verwerking
----------------------------------------------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $naam           = $_POST['naam'];
    $functie        = $_POST['functie'];
    $locatie        = $_POST['locatie'];
    $leidinggevende = $_POST['leidinggevende'];
    $bedrijf        = $_POST['bedrijf'];
    $email          = $_POST['email'];
    $referentie     = $_POST['referentie'];
    $contractueel   = ($_POST['radiogroep'] ?? '') === 'Ja';

    /* -----------------------------------------------------------
       4. Checkbox‑waarden verzamelen
    ----------------------------------------------------------- */

    $values = [];

    foreach ($columns as $col) {
        $kolom = $col['Field'];
        if (in_array($kolom, $exclude)) continue;

        // Aangevinkt = 0, anders NULL
        $values[$kolom] = isset($_POST[$kolom]) ? 0 : null;
    }

    /* -----------------------------------------------------------
       5. Business rules
    ----------------------------------------------------------- */

    $colVDLAD = 'VDL AD Account';
    $colMyVDL = 'MyVDL';
    $colKelio = 'Kelio';

    if ($contractueel) {

        // Contractueel
        $values[$colVDLAD] = 0;
        $values[$colMyVDL] = 3;

        foreach ($values as $product => $v) {
            if ($product === $colVDLAD || $product === $colMyVDL) continue;
            if ($v === 0) $values[$product] = 3;
        }

        $geselecteerdeProductenWebhook = [$colVDLAD];

    } else {

        // Niet‑contractueel
        $values[$colKelio] = 0;

        if (isset($values[$colMyVDL]) && $values[$colMyVDL] === 0) {
            $values[$colMyVDL] = 3;
        }

        foreach ($values as $product => $v) {
            if ($product === $colKelio) continue;
            if ($v === 0) $values[$product] = 3;
        }

        $geselecteerdeProductenWebhook = [$colKelio];
    }

    /* -----------------------------------------------------------
       6. Bestaat medewerker al?
    ----------------------------------------------------------- */

    $check = $pdo->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
    $check->execute([$naam]);

    if ($check->rowCount() > 0) {
        $melding = "Deze medewerker bestaat al.";
    } else {

        /* -----------------------------------------------------------
           7. Dynamische INSERT bouwen
        ----------------------------------------------------------- */

        $kolomnamen = array_keys($values);
        $kolomnamen_sql = implode(", ", array_map(fn($c) => "`$c`", $kolomnamen));

        $placeholders = implode(", ", array_fill(0, count($values), "?"));

        $sql = "
            INSERT INTO Medewerker 
            (Naam, Functie, Locatie, Leidinggevende, Bedrijf, Referentie, Email, $kolomnamen_sql)
            VALUES (" . rtrim(str_repeat("?,", 7 + count($values)), ",") . ")
        ";

        $stmt = $pdo->prepare($sql);

        $params = array_merge(
            [$naam, $functie, $locatie, $leidinggevende, $bedrijf, $referentie, $email],
            array_values($values)
        );

        $melding = "Medewerker succesvol aangemeld.";

        $stmt->execute($params);

        /* -----------------------------------------------------------
           8. Logboek
        ----------------------------------------------------------- */

        $productenMee = [];

        foreach ($values as $product => $v) {
            if ($v === 3) {
                $productenMee[] = $product;
            }
        }

        $productenTekst = empty($productenMee)
            ? "geen producten"
            : implode(", ", $productenMee);

        $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
        $log->execute([
            "$naam is aangemaakt als nieuwe medewerker door $gebruikerNaam met de producten $productenTekst",
            "Nieuwe Medewerker"
        ]);

        /* -----------------------------------------------------------
           9. Webhook 1: e‑mail medewerker
        ----------------------------------------------------------- */

        if (!empty($email)) {
            $payloadEmail = [
                "email" => $email,
                "naam"  => $naam
            ];

            $urlEmail = "https://hook.eu1.make.com/ve7v2yrp6w7x0tfm250ptn88f8hn17f6";
            $chEmail = curl_init($urlEmail);
            curl_setopt($chEmail, CURLOPT_POST, true);
            curl_setopt($chEmail, CURLOPT_POSTFIELDS, json_encode($payloadEmail));
            curl_setopt($chEmail, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
            curl_setopt($chEmail, CURLOPT_RETURNTRANSFER, true);
            curl_exec($chEmail);
            curl_close($chEmail);
        }

        /* -----------------------------------------------------------
           10. Webhook 2: product‑verantwoordelijken
        ----------------------------------------------------------- */

        $emails = [];

        $stmtProd = $pdo->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");

        foreach ($geselecteerdeProductenWebhook as $prod) {
            $stmtProd->execute([$prod]);
            if ($row = $stmtProd->fetch(PDO::FETCH_ASSOC)) {
                if (!empty($row['Contactpersoon'])) {
                    $emails[] = $row['Contactpersoon'];
                }
            }
        }

        $emails = array_unique($emails);

        $payload = [
            "naam"      => $naam,
            "actie"     => "toegevoegd",
            "producten" => $geselecteerdeProductenWebhook,
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
}
?>
