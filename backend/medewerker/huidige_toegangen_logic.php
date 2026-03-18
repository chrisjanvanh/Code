<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$afdeling       = "HR";
$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam  = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   1. Controle: heeft gebruiker toegang tot deze afdeling?
   + bedrijf ophalen
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT ID, Bedrijf FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);
$info = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$info) {
    header("Location: forbidden.php");
    exit;
}

$bedrijf = $info['Bedrijf'];

/* ---------------------------------------------------
   2. Medewerkers ophalen (alleen dit bedrijf)
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT Naam FROM Medewerker WHERE Bedrijf = ? ORDER BY Naam ASC");
$stmt->execute([$bedrijf]);
$medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   3. Medewerker + toegangen ophalen
--------------------------------------------------- */
$medewerker = null;
$toegangen  = [];
$bestanden  = [];

if (isset($_GET['naam'])) {
    $naam = $_GET['naam'];

    // Medewerker ophalen, gecontroleerd op bedrijf
    $stmt = $pdo->prepare("SELECT * FROM Medewerker WHERE Naam = ? AND Bedrijf = ?");
    $stmt->execute([$naam, $bedrijf]);
    $medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($medewerker) {
        // Toegangen uit BedrijfProduct
        $stmt = $pdo->prepare("
            SELECT Product, Waarde
            FROM BedrijfProduct
            WHERE Medewerker = ? AND Bedrijf = ?
            ORDER BY Product ASC
        ");
        $stmt->execute([$naam, $bedrijf]);
        $toegangen = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Bestanden ophalen
        $stmt = $pdo->prepare("
            SELECT ID, BestandNaam, BestandType, OCTET_LENGTH(BestandData) AS Grootte, UploadDatum
            FROM MedewerkerBestanden
            WHERE MedewerkerEmail = ?
            ORDER BY UploadDatum DESC
        ");
        $stmt->execute([$medewerker['Email']]);
        $bestanden = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

/* ---------------------------------------------------
   4. Toegang toevoegen / verwijderen (BedrijfProduct)
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie']) && ($_POST['actie'] === "toevoegen" || $_POST['actie'] === "verwijderen")) {

    $veld  = $_POST['veld'];   // Productnaam
    $naam  = $_POST['naam'];
    $actie = $_POST['actie'];

    // Huidige waarde ophalen uit BedrijfProduct
    $stmt = $pdo->prepare("
        SELECT Waarde 
        FROM BedrijfProduct 
        WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
    ");
    $stmt->execute([$naam, $veld, $bedrijf]);
    $huidigeWaarde = $stmt->fetchColumn();

    // Validatie
    if ($actie === "toevoegen" && $huidigeWaarde !== null) {
        die("FOUT: kan niet toevoegen, waarde is niet NULL");
    }

    if ($actie === "verwijderen" && $huidigeWaarde != 1) {
        die("FOUT: kan niet verwijderen, waarde is niet 1");
    }

    $actieTekst = ($actie === "toevoegen") ? "toegevoegd" : "verwijderd";
    $waarde     = ($actie === "toevoegen") ? 0 : 2;

    // Update uitvoeren in BedrijfProduct
    $stmt = $pdo->prepare("
        UPDATE BedrijfProduct 
        SET Waarde = ?
        WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
    ");
    $stmt->execute([$waarde, $naam, $veld, $bedrijf]);

    // Verantwoordelijke mailadressen ophalen (per product + bedrijf)
    $stmt = $pdo->prepare("
        SELECT Contactpersoon 
        FROM Product 
        WHERE Product = ? AND Bedrijf = ?
    ");
    $stmt->execute([$veld, $bedrijf]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

    $melding = "Toegang succesvol aangevraagd";

    $emails = [];
    if (!empty($contact['Contactpersoon'])) {
        $emails[] = $contact['Contactpersoon'];
    }

    // Webhook payload
    $payload = [
        "naam"      => $naam,
        "actie"     => $actieTekst,
        "producten" => [$veld],
        "emails"    => $emails
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
   5. Bovenste velden opslaan (Medewerker)
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['functie']) && !isset($_POST['actie'])) {

    $stmt = $pdo->prepare("
        UPDATE Medewerker 
        SET Functie = ?, Locatie = ?, Leidinggevende = ?, Bedrijf = ?, Email = ?
        WHERE Naam = ?
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
   6. Bestand verwijderen
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['actie']) && $_POST['actie'] === "verwijderen_bestand") {

    $stmt = $pdo->prepare("DELETE FROM MedewerkerBestanden WHERE ID = ?");
    $stmt->execute([$_POST['bestand_id']]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft een bestand verwijderd van " . $_POST['naam'],
        "Huidige Medewerker"
    ]);

    $_SESSION['melding'] = "Bestand succesvol verwijderd!";
    header("Location: ../HuidigeToegangen.php?naam=" . urlencode($_POST['naam']));
    exit;
}
