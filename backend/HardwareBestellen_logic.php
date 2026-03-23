<?php
session_start();

// Login check
if (!isset($_SESSION['email'])) {
    header("Location: /../../login.php");
    exit;
}

require_once __DIR__ . '/../config2.php'; // bevat $pdo

$afdeling = "IT Contact";

// $_SESSION['gebruikernaam'] = $_POST['gebruikernaam'] ?? "Onbekend";
$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

/* -----------------------------------------------------------
   Controleer of gebruiker toegang heeft tot deze afdeling
----------------------------------------------------------- */

$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}

/* -----------------------------------------------------------
   Bedrijven ophalen voor dropdown
----------------------------------------------------------- */

// Bedrijven ophalen waar HR toegang toe heeft
$stmt = $pdo->prepare("
    SELECT DISTINCT Bedrijf 
    FROM AfdelingEmails 
    WHERE Afdeling = 'HR' AND Email = ?
");
$stmt->execute([$gebruikerEmail]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);