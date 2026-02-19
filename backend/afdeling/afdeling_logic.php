<?php
session_start();
require_once __DIR__ . '/../config.php';

// 1. Check of afdeling is meegegeven
if (!isset($_GET['afdeling'])) {
    header("Location: forbidden.php");
    exit;
}

$afdeling = $_GET['afdeling'];

// 2. Check of gebruiker is ingelogd
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$gebruikerEmail = $_SESSION['email'];

// 3. Controleer of gebruiker toegang heeft tot deze afdeling
$stmt = $conn->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->bind_param("ss", $afdeling, $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: forbidden.php");
    exit;
}

// 4. Haal producten op
$stmt = $conn->prepare("SELECT * FROM Product WHERE Afdeling = ?");
$stmt->bind_param("s", $afdeling);
$stmt->execute();
$producten = $stmt->get_result();

// 5. Haal emailadressen op
$emailQuery = $conn->prepare("SELECT * FROM AfdelingEmails WHERE Afdeling = ?");
$emailQuery->bind_param("s", $afdeling);
$emailQuery->execute();
$emails = $emailQuery->get_result();
