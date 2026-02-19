<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

require_once "config.php";

$gebruikerEmail = $_SESSION['email'];

// Haal alle afdelingen op waar deze gebruiker toegang toe heeft
$stmt = $conn->prepare("SELECT Afdeling FROM AfdelingEmails WHERE Email = ?");
$stmt->bind_param("s", $gebruikerEmail);
$stmt->execute();
$result = $stmt->get_result();

$afdelingen = [];
while ($row = $result->fetch_assoc()) {
    $afdelingen[] = $row['Afdeling'];
}

// Geen afdelingen → forbidden
if (empty($afdelingen)) {
    header("Location: forbidden.php");
    exit;
}

// Eén afdeling → direct doorsturen
if (count($afdelingen) === 1) {
    $afdeling = urlencode($afdelingen[0]);
    header("Location: Afdeling.php?afdeling=$afdeling");
    exit;
}
