<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require __DIR__ . '/../config.php';

// Medewerkers ophalen
$medewerkers = $conn->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")->fetch_all(MYSQLI_ASSOC);

// Kolommen ophalen
$columnsResult = $conn->query("SHOW COLUMNS FROM Medewerker");
$columns = $columnsResult->fetch_all(MYSQLI_ASSOC);

// Kolommen die GEEN toegang zijn
$exclude = ["Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie"];

// Medewerker ophalen
$medewerker = null;

if (isset($_GET['naam'])) {
    $naam = $_GET['naam'];

    $stmt = $conn->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
    $stmt->bind_param("s", $naam);
    $stmt->execute();
    $result = $stmt->get_result();
    $medewerker = $result->fetch_assoc();
}

/* ---------------------------------------------------
   1. Toevoegen / Verwijderen van toegang
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie'])) {

    $veld = $_POST['veld'];   // productnaam
    $naam = $_POST['naam'];   // medewerker
    $actie = $_POST['actie']; // toevoegen of verwijderen

    // Nieuwe waarde bepalen
    // toevoegen → 0 (taak)
    // verwijderen → 2 (verwijderd)
    $waarde = ($actie === "toevoegen") ? 0 : 2;

    // Update uitvoeren
    $stmt = $conn->prepare("UPDATE Medewerker SET `$veld` = ? WHERE Naam = ?");
    $stmt->bind_param("is", $waarde, $naam);
    $stmt->execute();
    $stmt->close();

    // Verantwoordelijke mailadressen ophalen
    $stmtProd = $conn->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");
    $stmtProd->bind_param("s", $veld);
    $stmtProd->execute();
    $res = $stmtProd->get_result();

    $emails = [];
    if ($row = $res->fetch_assoc()) {
        if (!empty($row['Contactpersoon'])) {
            $emails[] = $row['Contactpersoon'];
        }
    }
    $stmtProd->close();

    // Webhook payload
    $payload = [
        "naam" => $naam,
        "actie" => $actie,
        "producten" => $veld,
        "emails" => $emails
    ];

    // Webhook call
    $url = "https://hook.eu1.make.com/113rh6zbq8knken7iynmqmtto1k0f67n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

    // Terug naar medewerker
    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($naam));
    exit;
}

/* ---------------------------------------------------
   2. Opslaan van bovenste velden
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['functie']) && !isset($_POST['actie'])) {

    $stmt = $conn->prepare("
        UPDATE Medewerker 
        SET Functie=?, Locatie=?, Leidinggevende=?, Bedrijf=? 
        WHERE Naam=?
    ");
    $stmt->bind_param(
        "sssss",
        $_POST['functie'],
        $_POST['locatie'],
        $_POST['leidinggevende'],
        $_POST['bedrijf'],
        $_POST['naam']
    );
    $stmt->execute();

    $_SESSION['melding'] = "Gegevens succesvol opgeslagen!";

    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($_POST['naam']));
    exit;
}
