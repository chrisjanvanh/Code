<?php
session_start();
require_once __DIR__ . '/../config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam  = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   1. Haal bedrijven op waar deze gebruiker toegang toe heeft
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT DISTINCT Bedrijf FROM AfdelingEmails WHERE Email = ?");
$stmt->execute([$gebruikerEmail]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* ---------------------------------------------------
   2. Geen bedrijven → forbidden
--------------------------------------------------- */
if (empty($bedrijven)) {
    header("Location: forbidden.php");
    exit;
}

/* ---------------------------------------------------
   3. Eén bedrijf → automatisch selecteren
--------------------------------------------------- */
if (count($bedrijven) === 1) {
    $geselecteerdBedrijf = $bedrijven[0];
} else {
    // Meerdere bedrijven → dropdown keuze
    $geselecteerdBedrijf = $_POST['bedrijf'] ?? "";
}

/* ---------------------------------------------------
   4. POST verwerking
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Controle: bedrijf moet gekozen zijn bij meerdere bedrijven
    if (count($bedrijven) > 1 && empty($_POST['bedrijf'])) {
        $_SESSION['melding'] = "Kies een bedrijf.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

    $serienummer  = trim($_POST['serienummer']);
    $merk         = trim($_POST['merk']);
    $model        = trim($_POST['model']);
    $prijs        = trim($_POST['prijs']);
    $aankoopdatum = trim($_POST['aankoopdatum']);

    if ($aankoopdatum === "") {
        $aankoopdatum = null;
    }

    // Prijs normaliseren
    $prijs = str_replace(',', '.', $prijs);

    if ($prijs === "") {
        $prijs = null;
    } elseif (!is_numeric($prijs)) {
        $_SESSION['melding'] = "Prijs is geen geldige waarde.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

    /* ---------------------------------------------------
       5. Serienummer moet uniek zijn
    --------------------------------------------------- */
    $check = $pdo->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
    $check->execute([$serienummer]);

    if ($check->rowCount() > 0) {
        $_SESSION['melding'] = "Dit serienummer bestaat al. Kies een uniek serienummer.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

    /* ---------------------------------------------------
       6. INSERT uitvoeren (inclusief Bedrijf)
    --------------------------------------------------- */
    $stmt = $pdo->prepare("
        INSERT INTO Hardware (Serienummer, Merk, Model, Prijs, Aankoopdatum, Bedrijf)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $ok = $stmt->execute([
        $serienummer,
        $merk,
        $model,
        $prijs,
        $aankoopdatum,
        $geselecteerdBedrijf
    ]);

    /* ---------------------------------------------------
       7. Logboek
    --------------------------------------------------- */
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft hardware toegevoegd (Serienummer: $serienummer, Bedrijf: $geselecteerdBedrijf)",
        "Hardware"
    ]);

    /* ---------------------------------------------------
       8. Resultaat
    --------------------------------------------------- */
    if ($ok) {
        $_SESSION['melding'] = "Hardware succesvol toegevoegd!";
    } else {
        error_log("Insert error: " . implode(" | ", $stmt->errorInfo()), 3, __DIR__ . "/error.log");
        $_SESSION['melding'] = "Er is iets fout gegaan bij het opslaan. Probeer het opnieuw.";
    }

    header("Location: ../HardwareToevoegen.php");
    exit;
}
