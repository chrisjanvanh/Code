<?php
session_start();
require_once __DIR__ . '/../config2.php';

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
$stmt = $pdo->prepare("SELECT ID FROM AfdelingEmails WHERE Afdeling = ? AND Email = ?");
$stmt->execute([$afdeling, $gebruikerEmail]);

if ($stmt->rowCount() === 0) {
    header("Location: forbidden.php");
    exit;
}

// 4. Haal producten op
$stmt = $pdo->prepare("SELECT * FROM Product WHERE Afdeling = ?");
$stmt->execute([$afdeling]);
$producten = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. Haal emailadressen op
$stmt2 = $pdo->prepare("SELECT * FROM AfdelingEmails WHERE Afdeling = ?");
$stmt2->execute([$afdeling]);
$emails = $stmt2->fetchAll(PDO::FETCH_ASSOC);
