<?php
// session_set_cookie_params([
//     'lifetime' => 0,
//     'path' => '/',
//     'domain' => '',
//     'secure' => true,
//     'httponly' => true,
//     'samesite' => 'None'
// ]);

session_start();

require 'config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: /../login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam  = $_SESSION['gebruikernaam'] ?? "Onbekend";
$afdelingRecht = "IT Contact";

/* 1. Bedrijven ophalen */
$stmt = $pdo->prepare("SELECT DISTINCT Bedrijf FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdelingRecht, $gebruikerEmail]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($bedrijven)) {
    header("Location: forbidden.php");
    exit;
}

/* 2. Bepaal bedrijf */
if (count($bedrijven) === 1) {
    $bedrijf = $bedrijven[0];
} else {
    $bedrijf = $_GET['bedrijf'] ?? $_POST['bedrijf'] ?? "";
}

$bedrijfGekozen = !empty($bedrijf);

/* 3. Data ophalen */
$medewerkers = $voorraad = $toegewezen = $alle_regels = [];

if ($bedrijfGekozen) {

    $stmt = $pdo->prepare("
        SELECT MedewerkerID, Naam 
        FROM Medewerker 
        WHERE Bedrijf = ? 
        ORDER BY Naam ASC
    ");
    $stmt->execute([$bedrijf]);
    $medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT Serienummer, Model, 'Voorraad' AS Naam, NULL AS Uitgiftedatum, 'voorraad' AS type
        FROM Hardware
        WHERE Bedrijf = ?
        AND Serienummer NOT IN (SELECT Serienummer FROM Gebruikname)
    ");
    $stmt->execute([$bedrijf]);
    $voorraad = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("
        SELECT g.Serienummer, h.Model, m.Naam, g.Uitgiftedatum, 'toegewezen' AS type
        FROM Gebruikname g
        JOIN Hardware h ON g.Serienummer = h.Serienummer
        JOIN Medewerker m ON g.MedewerkerID = m.MedewerkerID
        WHERE h.Bedrijf = ?
        ORDER BY g.Uitgiftedatum DESC
    ");
    $stmt->execute([$bedrijf]);
    $toegewezen = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $alle_regels = array_merge($voorraad, $toegewezen);
}

/* 4. Opslaan */
if (isset($_POST['opslaan'])) {

    if (!$bedrijfGekozen) {
        $_SESSION['melding'] = "Kies eerst een bedrijf.";
        header("Location: ../HardwareToewijzen.php");
        exit;
    }

    $medewerkerID = (int)($_POST['medewerkerID'] ?? 0);
    $serienummer  = trim($_POST['serienummer'] ?? '');
    $uitgiftedatum = $_POST['uitgiftedatum'] ?? '';

    if ($medewerkerID === 0 || $serienummer === "" || $uitgiftedatum === "") {
        $_SESSION['melding'] = "Vul alle velden in.";
        header("Location: ../HardwareToewijzen.php?bedrijf=" . urlencode($bedrijf));
        exit;
    }

    // Check of medewerker bij bedrijf hoort
    $checkNaam = $pdo->prepare("
        SELECT Naam 
        FROM Medewerker 
        WHERE MedewerkerID = ? AND Bedrijf = ?
    ");
    $checkNaam->execute([$medewerkerID, $bedrijf]);
    $medewerker = $checkNaam->fetch(PDO::FETCH_ASSOC);

    if (!$medewerker) {
        $_SESSION['melding'] = "Deze medewerker hoort niet bij dit bedrijf.";
        header("Location: ../HardwareToewijzen.php?bedrijf=" . urlencode($bedrijf));
        exit;
    }

    $naam = $medewerker['Naam'];

    // Check hardware binnen bedrijf
    $checkHW = $pdo->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ? AND Bedrijf = ?");
    $checkHW->execute([$serienummer, $bedrijf]);

    if ($checkHW->rowCount() === 0) {
        $_SESSION['melding'] = "Dit serienummer bestaat niet binnen dit bedrijf.";
        header("Location: ../HardwareToewijzen.php?bedrijf=" . urlencode($bedrijf));
        exit;
    }

    // Check of serienummer al in gebruik is
    $checkSN = $pdo->prepare("SELECT 1 FROM Gebruikname WHERE Serienummer = ?");
    $checkSN->execute([$serienummer]);

    if ($checkSN->rowCount() > 0) {
        $_SESSION['melding'] = "Dit serienummer is al toegewezen.";
        header("Location: ../HardwareToewijzen.php?bedrijf=" . urlencode($bedrijf));
        exit;
    }

    // INSERT met MedewerkerID
    $stmt = $pdo->prepare("
        INSERT INTO Gebruikname (Serienummer, MedewerkerID, Uitgiftedatum)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([$serienummer, $medewerkerID, $uitgiftedatum]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft hardware ($serienummer) toegewezen aan $naam (MedewerkerID: $medewerkerID) in bedrijf $bedrijf",
        "Toewijzen Hardware"
    ]);

    $_SESSION['melding'] = "Hardware succesvol toegewezen!";
    header("Location: ../HardwareToewijzen.php?bedrijf=" . urlencode($bedrijf));
    exit;
}

/* 5. Verwijderen */
if (isset($_POST['verwijder'])) {

    $sn   = $_POST['verwijder'];
    $type = $_POST['type'];

    if ($type === "toegewezen") {

        $oph = $pdo->prepare("
            SELECT g.MedewerkerID, g.Uitgiftedatum, h.Bedrijf
            FROM Gebruikname g
            JOIN Hardware h ON g.Serienummer = h.Serienummer
            WHERE g.Serienummer = ?
        ");
        $oph->execute([$sn]);
        $opgehaald = $oph->fetch(PDO::FETCH_ASSOC);

        if ($opgehaald) {
            $medewerkerID = $opgehaald['MedewerkerID'];
            $uitgiftedatum = $opgehaald['Uitgiftedatum'];
            $bedrijfje = $opgehaald['Bedrijf'];

            $m = $pdo->prepare("SELECT Naam FROM Medewerker WHERE MedewerkerID = ?");
            $m->execute([$medewerkerID]);
            $med = $m->fetch(PDO::FETCH_ASSOC);
            $naamMedewerker = $med['Naam'] ?? 'Onbekend';

            $ges = $pdo->prepare("
                INSERT INTO GeschiedenisGebruikname (Serienummer, Naam, Uitgiftedatum, Bedrijf)
                VALUES (?, ?, ?, ?)
            ");
            $ges->execute([$sn, $naamMedewerker, $uitgiftedatum, $bedrijfje]);

            $del = $pdo->prepare("DELETE FROM Gebruikname WHERE Serienummer = ?");
            $del->execute([$sn]);

            $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
            $log->execute([
                "$gebruikerNaam heeft toegewezen hardware ($sn) teruggezet naar voorraad in bedrijf $bedrijfje",
                "Toewijzen Hardware"
            ]);
        }
    }

    if ($type === "voorraad") {
        $del = $pdo->prepare("DELETE FROM Hardware WHERE Serienummer = ? AND Bedrijf = ?");
        $del->execute([$sn, $bedrijf]);

        $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
        $log->execute([
            "$gebruikerNaam heeft hardware ($sn) verwijderd uit bedrijf $bedrijf",
            "Hardware"
        ]);
    }

    $_SESSION['melding'] = "Hardware verwijderd.";
    header("Location: ../HardwareToewijzen.php?bedrijf=" . urlencode($bedrijf));
    exit;
}
