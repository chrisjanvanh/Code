<?php
session_start();
require 'config2.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$afdeling = "Business IT";
$gebruikerEmail = $_SESSION['email'];

$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* ---------------------------------------------------
   1. Controle: heeft gebruiker toegang?
--------------------------------------------------- */
$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: ../forbidden.php");
    exit;
}

$melding = "";

/* ---------------------------------------------------
   2. POST verwerking
--------------------------------------------------- */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

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
       3. Serienummer moet uniek zijn
    --------------------------------------------------- */
    $check = $pdo->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
    $check->execute([$serienummer]);

    if ($check->rowCount() > 0) {
        $_SESSION['melding'] = "Dit serienummer bestaat al. Kies een uniek serienummer.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

    /* ---------------------------------------------------
       4. INSERT uitvoeren
    --------------------------------------------------- */
    $stmt = $pdo->prepare("
        INSERT INTO Hardware (Serienummer, Merk, Model, Prijs, Aankoopdatum)
        VALUES (?, ?, ?, ?, ?)
    ");

    $ok = $stmt->execute([
        $serienummer,
        $merk,
        $model,
        $prijs,
        $aankoopdatum
    ]);

    // Logboek
    $log = $pdo->prepare("INSERT INTO Logboek (Actie, Soort) VALUES (?, ?)");
    $log->execute([
        "$gebruikerNaam heeft de hardware met als serienummer $serienummer aangemaakt",
        "Nieuwe Hardware"
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
