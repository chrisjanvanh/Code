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
    header("Location: forbidden.php");
    exit;
}

/* ---------------------------------------------------
   2. Medewerkers + hardware ophalen
--------------------------------------------------- */
$medewerkers = $pdo->query("SELECT Naam FROM Medewerker ORDER BY Naam ASC")
                   ->fetchAll(PDO::FETCH_ASSOC);

$hardware = $pdo->query("SELECT DISTINCT Serienummer FROM GeschiedenisGebruikname")
                ->fetchAll(PDO::FETCH_ASSOC);

$melding = "";

/* ---------------------------------------------------
   3. 
--------------------------------------------------- */
$toegewezen = $pdo->query("
    SELECT Serienummer, Naam, Uitgiftedatum, Einddatum
    FROM GeschiedenisGebruikname
    ORDER BY Einddatum DESC
")->fetchAll(PDO::FETCH_ASSOC);

$alle_regels = array_merge($toegewezen);
