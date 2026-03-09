<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$afdeling = "HR";
$gebruikerEmail = $_SESSION['email'];

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   1. Controle: heeft gebruiker toegang tot deze afdeling?
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}

/* ---------------------------------------------------
   2. Medewerkers ophalen
--------------------------------------------------- */
$stmt = $pdo->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC");
$medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   3. Kolommen ophalen
--------------------------------------------------- */
$stmt = $pdo->query("SHOW COLUMNS FROM Medewerker");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Kolommen die GEEN toegang zijn */
$exclude = ["Naam", "Functie", "Locatie", "Leidinggevende", "Bedrijf", "Referentie", "Email"];

/* ---------------------------------------------------
   4. Medewerker ophalen
--------------------------------------------------- */
$medewerker = null;

if (isset($_GET['naam'])) {
    $naam = $_GET['naam'];

    $stmt = $pdo->prepare("SELECT * FROM Medewerker WHERE Naam = ?");
    $stmt->execute([$naam]);
    $medewerker = $stmt->fetch(PDO::FETCH_ASSOC);
}

/* ---------------------------------------------------
   5. Bestanden ophalen
--------------------------------------------------- */
$bestanden = [];

if ($medewerker) {
    $stmt = $pdo->prepare("
        SELECT ID, BestandNaam, BestandType, OCTET_LENGTH(BestandData) AS Grootte, UploadDatum
        FROM MedewerkerBestanden
        WHERE MedewerkerEmail = ?
        ORDER BY UploadDatum DESC
    ");
    $stmt->execute([$medewerker['Email']]);
    $bestanden = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/* ---------------------------------------------------
   6. Toegang toevoegen/verwijderen
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie']) && ($_POST['actie'] === "toevoegen" || $_POST['actie'] === "verwijderen")) {

    $veld = $_POST['veld'];
    $naam = $_POST['naam'];
    $actie = $_POST['actie'];

    // Huidige waarde ophalen
    $stmt = $pdo->prepare("SELECT `$veld` FROM Medewerker WHERE Naam = ?");
    $stmt->execute([$naam]);
    $huidigeWaarde = $stmt->fetchColumn();

    // Validatie
    if ($actie === "toevoegen" && $huidigeWaarde !== null) {
        die("FOUT: kan niet toevoegen, waarde is niet NULL");
    }

    if ($actie === "verwijderen" && $huidigeWaarde != 1) {
        die("FOUT: kan niet verwijderen, waarde is niet 1");
    }

    $actieTekst = ($actie === "toevoegen") ? "toegevoegd" : "verwijderd";
    $waarde = ($actie === "toevoegen") ? 0 : 2;

    // Update uitvoeren
    $stmt = $pdo->prepare("UPDATE Medewerker SET `$veld` = ? WHERE Naam = ?");
    $stmt->execute([$waarde, $naam]);

    // Verantwoordelijke mailadressen ophalen
    $stmt = $pdo->prepare("SELECT Contactpersoon FROM Product WHERE Product = ?");
    $stmt->execute([$veld]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    $emails = [];
    if (!empty($contact['Contactpersoon'])) {
        $emails[] = $contact['Contactpersoon'];
    }

    // Webhook payload
    $payload = [
        "naam" => $naam,
        "actie" => $actieTekst,
        "producten" => [$veld],
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

    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($naam));
    exit;
}

/* ---------------------------------------------------
   7. Bovenste velden opslaan
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['functie']) && !isset($_POST['actie'])) {

    $stmt = $pdo->prepare("
        UPDATE Medewerker 
        SET Functie=?, Locatie=?, Leidinggevende=?, Bedrijf=?, Email=?
        WHERE Naam=?
    ");

    $stmt->execute([
        $_POST['functie'],
        $_POST['locatie'],
        $_POST['leidinggevende'],
        $_POST['bedrijf'],
        $_POST['email'],
        $_POST['naam']
    ]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        $gebruikerNaam . " heeft de gegevens van " . $_POST['naam'] . " aangepast",
        "Huidige Medewerker"
    ]);

    $_SESSION['melding'] = "Gegevens succesvol opgeslagen!";
    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($_POST['naam']));
    exit;
}

/* ---------------------------------------------------
   8. Bestand verwijderen
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie']) && $_POST['actie'] === "verwijderen_bestand") {

    $stmt = $pdo->prepare("DELETE FROM MedewerkerBestanden WHERE ID = ?");
    $stmt->execute([$_POST['bestand_id']]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft een bestand verwijderd voor " . $medewerker['Email'],
        "Huidige Medewerker"
    ]);

    $_SESSION['melding'] = "Bestand succesvol verwijderd!";
    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($_POST['naam']));

    exit;
}
