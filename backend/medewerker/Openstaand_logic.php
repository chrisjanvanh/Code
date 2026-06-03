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

$afdeling = "HR";
$gebruikerEmail = $_SESSION['email'];
$gebruikerNaam = $_SESSION['gebruikernaam'] ?? "Onbekend";

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

/* Helper: placeholders voor IN (...) */
$placeholdersBedrijven = implode(',', array_fill(0, count($bedrijvenHR), '?'));

/* ---------------------------------------------------
   2. Openstaande taken ophalen (waarde 0 of 2)
--------------------------------------------------- */
$sql = "
    SELECT Medewerker, Product, Waarde
    FROM BedrijfProduct
    WHERE Bedrijf IN ($placeholdersBedrijven)
      AND (Waarde IN (0, 2, 3))
    ORDER BY Medewerker ASC
";


$stmt = $pdo->prepare($sql);
$stmt->execute($bedrijvenHR);
$openstaandeTaken = $stmt->fetchAll(PDO::FETCH_ASSOC);