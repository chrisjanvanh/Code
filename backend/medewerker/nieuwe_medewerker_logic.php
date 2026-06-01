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


// Login check
if (!isset($_SESSION['email'])) {
    header("Location: /../../login.php");
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

$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Email = ?");
$stmt->execute([$gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}

/* -----------------------------------------------------------
   2. Kolommen ophalen uit database
----------------------------------------------------------- */

$exclude = [
    "Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf",
    "Referentie", "Email", "VDL AD Account", "MyVDL", "Kelio"
];

$melding = "";

/* -----------------------------------------------------------
   Bedrijven ophalen voor dropdown
----------------------------------------------------------- */

// Bedrijven ophalen waar HR toegang toe heeft
$stmt = $pdo->prepare("
    SELECT DISTINCT Bedrijf 
    FROM AfdelingEmails 
    WHERE Email = ?
");
$stmt->execute([$gebruikerEmail]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);

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

    // Producten ophalen die bij dit bedrijf horen
    $stmt = $pdo->prepare("SELECT Product FROM Product WHERE Bedrijf = ? ORDER BY Product ASC");
    $stmt->execute([$bedrijf]);
    $productKolommen = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Bouw $columns in hetzelfde formaat als SHOW COLUMNS
    $columns = [];
    foreach ($productKolommen as $p) {
        $columns[] = ['Field' => $p];
    }


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
        return;
    } else {

        /* -----------------------------------------------------------
           7. Dynamische INSERT bouwen
        ----------------------------------------------------------- */

        $sql = "
            INSERT INTO Medewerker 
            (Naam, Functie, Locatie, Leidinggevende, Bedrijf, Referentie, Email)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$naam, $functie, $locatie, $leidinggevende, $bedrijf, $referentie, $email]);


        $melding = "Medewerker succesvol aangemeld.";

        foreach ($values as $product => $waarde) {

        $stmt = $pdo->prepare("
            INSERT INTO BedrijfProduct (Bedrijf, Product, Medewerker, Waarde)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([
            $bedrijf,
            $product,
            $naam,
            $waarde   // 0 of NULL of 3 (business rules)
        ]);
    }


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

        $stmtProd = $pdo->prepare("SELECT Contactpersoon 
                                FROM Product 
                                WHERE Product = ? AND Bedrijf = ?");

        foreach ($geselecteerdeProductenWebhook as $prod) {
            $stmtProd->execute([$prod, $bedrijf]);
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
