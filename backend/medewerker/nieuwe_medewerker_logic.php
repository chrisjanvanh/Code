<?php
session_start();

// Login check
if (!isset($_SESSION['email'])) {
    header("Location: /login.php");
    exit;
}

require_once __DIR__ . '/../config.php';

$afdeling = "HR";

$gebruikerEmail = $_SESSION['email'];

$stmt = $conn->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->bind_param("ss", $afdeling, $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: forbidden.php");
    exit;
}

// Kolommen ophalen
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN checkbox zijn
$exclude = [
    "Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie", "Email", "VDL AD Account", "MyVDL", "Kelio"
];

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $naam          = $_POST['naam'];
    $functie       = $_POST['functie'];
    $locatie       = $_POST['locatie'];
    $leidinggevende= $_POST['leidinggevende'];
    $bedrijf       = $_POST['bedrijf'];
    $email         = $_POST['email'];
    $referentie    = $_POST['referentie'];
    $contractueel  = ($_POST['radiogroep'] ?? '') === 'Ja'; // Ja = contractueel

    // 1. Basis: alle aangevinkte producten → 0, anders NULL
    $values = [];
    foreach ($columns as $col) {
        $kolom = $col['Field'];
        if (in_array($kolom, $exclude)) continue;

        $values[$kolom] = isset($_POST[$kolom]) ? 0 : NULL;
    }

    // 2. Business rules voor nieuwe medewerker

    // Zorg dat deze kolomnamen exact overeenkomen met je DB:
    $colVDLAD = 'VDL AD Account';
    $colMyVDL = 'MyVDL';
    $colKelio = 'Kelio';

    if ($contractueel) {
        // Contractueel:
        // VDL AD Account → 0
        $values[$colVDLAD] = 0;

        // MyVDL → altijd 3
        $values[$colMyVDL] = 3;

        // Alle andere aangevinkte producten → 3 (behalve VDL AD Account)
        foreach ($values as $product => $v) {
            if ($product === $colVDLAD || $product === $colMyVDL) continue;
            if ($v === 0) {
                $values[$product] = 3;
            }
        }

        // Webhook: alleen VDL AD Account
        $geselecteerdeProductenWebhook = [$colVDLAD];

    } else {
        // Niet‑contractueel:
        // Kelio → 0
        $values[$colKelio] = 0;

        // MyVDL: alleen 3 als aangevinkt
        if (isset($values[$colMyVDL]) && $values[$colMyVDL] === 0) {
            $values[$colMyVDL] = 3;
        }

        // Alle andere aangevinkte producten → 3 (behalve Kelio)
        foreach ($values as $product => $v) {
            if ($product === $colKelio) continue;
            if ($v === 0) {
                $values[$product] = 3;
            }
        }

        // Webhook: alleen Kelio
        $geselecteerdeProductenWebhook = [$colKelio];
    }

    // 3. Check of medewerker al bestaat
    $check = $conn->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
    $check->bind_param("s", $naam);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $melding = "Deze medewerker bestaat al.";
    } else {

        // 4. Dynamische INSERT
        $kolomnamen     = array_keys($values);
        $kolomnamen_sql = implode(", ", array_map(fn($c) => "`$c`", $kolomnamen));
        $placeholders   = implode(", ", array_fill(0, count($kolomnamen), "?"));

        $sql = "
            INSERT INTO Medewerker 
            (Naam, Functie, Locatie, Leidinggevende, Bedrijf, Referentie, Email, $kolomnamen_sql)
            VALUES (?, ?, ?, ?, ?, ?, ?, $placeholders)
        ";

        $stmt = $conn->prepare($sql);

        $types  = "sssssss" . str_repeat("i", count($values));
        $params = array_merge(
            [$naam, $functie, $locatie, $leidinggevende, $bedrijf, $referentie, $email],
            array_values($values)
        );

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $melding = "Nieuwe medewerker succesvol toegevoegd!";

            // 1. Webhook met e‑mail van medewerker
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

            // 2. Product‑webhook: alleen VDL AD Account of Kelio
            if (!empty($geselecteerdeProductenWebhook)) {

                $emails = [];

                $stmtProd = $conn->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");
                foreach ($geselecteerdeProductenWebhook as $prod) {
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

        } else {
            error_log("Medewerker insert error: " . $stmt->error, 3, __DIR__ . "/error.log");
            $melding = "Er is iets fout gegaan, probeer het later opnieuw.";
        }

        $stmt->close();
    }

    $check->close();
}
