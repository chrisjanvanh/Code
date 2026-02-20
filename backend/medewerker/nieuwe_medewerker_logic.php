<?php
session_start();

require_once __DIR__ . '/../config.php';

// Haal alle kolommen op van de tabel Medewerker
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN checkbox zijn
$exclude = [
    "Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie"
];

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $naam = $_POST['naam'];
    $functie = $_POST['functie'];
    $locatie = $_POST['locatie'];
    $leidinggevende = $_POST['leidinggevende'];
    $bedrijf = $_POST['bedrijf'];
    $referentie = $_POST['referentie'];

    // Dynamisch checkbox‑waarden verzamelen
    $values = [];
    foreach ($columns as $col) {
        $kolom = $col['Field'];
        if (in_array($kolom, $exclude)) continue;

        $values[$kolom] = isset($_POST[$kolom]) ? 0 : NULL;
    }

    // Check of medewerker al bestaat
    $check = $conn->prepare("SELECT 1 FROM Medewerker WHERE Naam = ?");
    $check->bind_param("s", $naam);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $melding = "Deze medewerker bestaat al.";
    } else {

        // Dynamische INSERT opbouwen
        $kolomnamen = array_keys($values);
        $kolomnamen_sql = implode(", ", array_map(fn($c) => "`$c`", $kolomnamen));
        $placeholders = implode(", ", array_fill(0, count($kolomnamen), "?"));

        $sql = "
            INSERT INTO Medewerker 
            (Naam, Functie, Locatie, Leidinggevende, Bedrijf, Referentie, $kolomnamen_sql)
            VALUES (?, ?, ?, ?, ?, ?, $placeholders)
        ";

        $stmt = $conn->prepare($sql);

        $types = "ssssss" . str_repeat("i", count($values));

        $params = array_merge(
            [$naam, $functie, $locatie, $leidinggevende, $bedrijf, $referentie],
            array_values($values)
        );

        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $melding = "Nieuwe medewerker succesvol toegevoegd!";

            // Check of er een productveld op 0 staat
            // Zoek alle geselecteerde producten (waarde = 0)
                $geselecteerdeProducten = [];
                foreach ($values as $product => $v) {
                    if ($v === 0) {
                        $geselecteerdeProducten[] = $product;
                    }
                }

                if (!empty($geselecteerdeProducten)) {

                    // Mailadressen ophalen van verantwoordelijken
                    $emails = [];

                    $stmtProd = $conn->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");
                    foreach ($geselecteerdeProducten as $prod) {
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

                    // Dubbele mailadressen verwijderen
                    $emails = array_unique($emails);

                    // Webhook aanroepen met mailadressen + medewerker info
                    $payload = [
                        "naam" => $naam,
                        "actie" => "toegevoegd",
                        "producten" => $geselecteerdeProducten,
                        "emails" => $emails
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
