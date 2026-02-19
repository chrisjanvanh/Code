<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require 'config.php';

$melding = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $serienummer = trim($_POST['serienummer']);
    $merk = trim($_POST['merk']);
    $model = trim($_POST['model']);
    $prijs = trim($_POST['prijs']);
    $aankoopdatum = trim($_POST['aankoopdatum']);
    if ($aankoopdatum === "") {
        $aankoopdatum = NULL;
    }


    // Prijs normaliseren
    $prijs = trim($_POST['prijs']);
    $prijs = str_replace(',', '.', $prijs);

    // Prijs mag leeg zijn
    if ($prijs === "") {
        $prijs = NULL;
    } elseif (!is_numeric($prijs)) {
        $_SESSION['melding'] = "Prijs is geen geldige waarde.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }


    // Check of serienummer al bestaat
    $check = $conn->prepare("SELECT 1 FROM Hardware WHERE Serienummer = ?");
    $check->bind_param("s", $serienummer);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $_SESSION['melding'] = "Dit serienummer bestaat al. Kies een uniek serienummer.";
        header("Location: ../HardwareToevoegen.php");
        exit;
    }

    // INSERT uitvoeren
    $stmt = $conn->prepare("
        INSERT INTO Hardware (Serienummer, Merk, Model, Prijs, Aankoopdatum)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("sssss", $serienummer, $merk, $model, $prijs, $aankoopdatum);

    if ($stmt->execute()) {
        $_SESSION['melding'] = "Hardware succesvol toegevoegd!";
    } else {
        error_log("Insert error: " . $stmt->error, 3, __DIR__ . "/error.log");
        $_SESSION['melding'] = "Er is iets fout gegaan bij het opslaan. Probeer het opnieuw.";
    }

    header("Location: ../HardwareToevoegen.php");
    exit;
}
