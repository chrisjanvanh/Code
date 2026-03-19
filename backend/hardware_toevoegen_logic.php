<?php
session_start();
require 'config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam  = $_SESSION['gebruikernaam'] ?? "Onbekend";
$afdelingRechten = "IT Contact";

/* ---------------------------------------------------
   1. Haal bedrijven op waar deze gebruiker toegang toe heeft
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT DISTINCT Bedrijf FROM AfdelingEmails WHERE Email = ? AND Afdeling = ?");
$stmt->execute([$gebruikerEmail, $afdelingRechten]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);

/* ---------------------------------------------------
   2. POST verwerking
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serienummer  = trim($_POST['serienummer']);
    $bedrijf      = trim($_POST['bedrijf']);
    $merk         = trim($_POST['merk']);
    $model        = trim($_POST['model']);
    $prijs        = trim($_POST['prijs']);
    $aankoopdatum = trim($_POST['aankoopdatum']);

    /* ---------------------------------------------------
       3. Validatie bedrijf
    --------------------------------------------------- */
    if (!in_array($bedrijf, $bedrijven)) {
        $_SESSION['melding'] = "Ongeldig bedrijf geselecteerd.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

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
       4. Serienummer moet uniek zijn
    --------------------------------------------------- */
    $check = $pdo->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
    $check->execute([$serienummer]);

    if ($check->rowCount() > 0) {
        $_SESSION['melding'] = "Dit serienummer bestaat al. Kies een uniek serienummer.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

    /* ---------------------------------------------------
       5. INSERT uitvoeren (inclusief Bedrijf!)
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
        $bedrijf
    ]);

    /* ---------------------------------------------------
       6. Logboek
    --------------------------------------------------- */
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft hardware ($serienummer) toegevoegd aan bedrijf $bedrijf",
        "Hardware"
    ]);

    if ($ok) {
        $_SESSION['melding'] = "Hardware succesvol toegevoegd!";
    } else {
        error_log("Insert error: " . implode(" | ", $stmt->errorInfo()), 3, __DIR__ . "/error.log");
        $_SESSION['melding'] = "Er is iets fout gegaan bij het opslaan. Probeer het opnieuw.";
    }

    header("Location: ../HardwareToevoegen.php");
    exit;
}
