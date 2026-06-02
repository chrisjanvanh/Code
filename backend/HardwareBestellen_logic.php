<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'maar',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

session_start();

require 'config2.php';

// Login check
if (!isset($_SESSION['email'])) {
    header("Location: /../login.php");
    exit;
}

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
    WHERE Afdeling = 'IT Contact' AND Email = ?
");
$stmt->execute([$gebruikerEmail]);
$bedrijven = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("SELECT ID, Naam, Prijs FROM HardwareBestellen ORDER BY ID ASC");
$stmt->execute();
$producten = $stmt->fetchAll(PDO::FETCH_ASSOC);
