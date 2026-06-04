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

require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: /../../login.php");
    exit;
}

$afdeling       = "HR";
$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam  = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   1. Alle bedrijven ophalen waarvoor deze gebruiker HR is
--------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT Bedrijf 
    FROM AfdelingEmails 
    WHERE Email = ?
");
$stmt->execute([$gebruikerEmail]);
$bedrijvenHR = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($bedrijvenHR)) {
    header("Location: forbidden.php");
    exit;
}

$stmt2 = $pdo->prepare("
    SELECT Bedrijf 
    FROM AfdelingEmails 
    WHERE Email = ? AND Afdeling = 'HR'
");
$stmt2->execute([$gebruikerEmail]);
$isHR = $stmt2->fetchAll(PDO::FETCH_COLUMN);

/* Helper: placeholders voor IN (...) */
$placeholdersBedrijven = implode(',', array_fill(0, count($bedrijvenHR), '?'));

/* ---------------------------------------------------
   2. Medewerkers ophalen (alleen bedrijven waar HR toegang toe heeft)
--------------------------------------------------- */
$stmt = $pdo->prepare("
    SELECT Naam 
    FROM Medewerker 
    WHERE Bedrijf IN ($placeholdersBedrijven)
    ORDER BY Naam ASC
");
$stmt->execute($bedrijvenHR);
$medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* ---------------------------------------------------
   3. Medewerker + toegangen + bestanden ophalen
--------------------------------------------------- */
$medewerker = null;
$toegangen  = [];
$bestanden  = [];

if (isset($_GET['naam'])) {
    $naam = $_GET['naam'];

    // Medewerker ophalen, maar alleen als hij in een bedrijf zit waar HR toegang toe heeft
    $params = array_merge([$naam], $bedrijvenHR);

    $stmt = $pdo->prepare("
        SELECT * 
        FROM Medewerker 
        WHERE Naam = ?
          AND Bedrijf IN ($placeholdersBedrijven)
    ");
    $stmt->execute($params);
    $medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($medewerker) {
        $bedrijfMedewerker = $medewerker['Bedrijf'];

        // Toegangen uit BedrijfProduct voor deze medewerker + zijn bedrijf
        $stmt = $pdo->prepare("
            SELECT Product, Waarde
            FROM BedrijfProduct
            WHERE Medewerker = ? AND Bedrijf = ?
            ORDER BY Product ASC
        ");
        $stmt->execute([$naam, $bedrijfMedewerker]);
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

    // Medewerker opnieuw ophalen om zeker te zijn van bedrijf + rechten
    $params = array_merge([$naam], $bedrijvenHR);
    $stmt = $pdo->prepare("
        SELECT * 
        FROM Medewerker 
        WHERE Naam = ?
          AND Bedrijf IN ($placeholdersBedrijven)
    ");
    $stmt->execute($params);
    $medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medewerker) {
        die("FOUT: geen rechten op deze medewerker of medewerker niet gevonden.");
    }

    $bedrijfMedewerker = $medewerker['Bedrijf'];

    // Huidige waarde ophalen uit BedrijfProduct
    $stmt = $pdo->prepare("
        SELECT Waarde 
        FROM BedrijfProduct 
        WHERE Medewerker = ? AND Product = ? AND Bedrijf = ?
    ");
    $stmt->execute([$naam, $veld, $bedrijfMedewerker]);
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
    $stmt->execute([$waarde, $naam, $veld, $bedrijfMedewerker]);

    // Verantwoordelijke mailadressen ophalen (per product + bedrijf)
    $stmt = $pdo->prepare("
        SELECT Contactpersoon 
        FROM Product 
        WHERE Product = ? AND Bedrijf = ?
    ");
    $stmt->execute([$veld, $bedrijfMedewerker]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);

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

    // Medewerker ophalen + check bedrijf
    $naamPost = $_POST['naam'];
    $params   = array_merge([$naamPost], $bedrijvenHR);

    $stmt = $pdo->prepare("
        SELECT * 
        FROM Medewerker 
        WHERE Naam = ?
          AND Bedrijf IN ($placeholdersBedrijven)
    ");
    $stmt->execute($params);
    $medewerker = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medewerker) {
        die("FOUT: geen rechten op deze medewerker of medewerker niet gevonden.");
    }

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
